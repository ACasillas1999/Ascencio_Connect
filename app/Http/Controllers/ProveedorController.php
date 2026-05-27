<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use App\Models\Evento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProveedorController extends Controller
{
    /**
     * Muestra la interfaz de escaneo para el proveedor.
     */
    public function index()
    {
        $usuario = Auth::user()->username;
        
        // Obtener cuántos puntos da este proveedor y en qué evento activo
        $proveedor_evento = DB::table('proveedor_evento')
            ->join('evento', 'evento.ID', '=', 'proveedor_evento.ID_Evento')
            ->where('proveedor_evento.NombreProveedor', $usuario)
            ->where('proveedor_evento.Activo', 1)
            ->select('proveedor_evento.Puntos', 'evento.name_evento', 'evento.ID as ID_Evento')
            ->first();

        $puntos = $proveedor_evento ? $proveedor_evento->Puntos : 0;
        $evento_nombre = $proveedor_evento ? $proveedor_evento->name_evento : 'Sin asignar';
        $id_evento = $proveedor_evento ? $proveedor_evento->ID_Evento : null;

        return view('proveedor.index', compact('usuario', 'puntos', 'evento_nombre', 'id_evento'));
    }

    /**
     * Procesa el código QR escaneado y asigna los puntos.
     */
    public function asignarPuntos(Request $request)
    {
        $usuario = Auth::user()->username;
        $qr_texto = $request->input('codigo', '');

        if (empty($qr_texto)) {
            return response('❌ Código QR vacío.', 400);
        }

        // Parseo del formato ID1234Ñ...
        $partes = explode("Ñ", $qr_texto);
        if (isset($partes[0])) {
            $raw_id = trim(str_replace("ID", "", $partes[0]));
            $codigo = substr(preg_replace('/\D/', '', $raw_id), 0, 4);
            if ($codigo === '') {
                return response('❌ ID inválido extraído del QR.', 400);
            }
            $codigo = (int)$codigo;
        } else {
            return response('❌ Código QR no contiene formato esperado.', 400);
        }

        // 1) Buscar Participante
        $participante = Participante::find($codigo);
        if (!$participante) {
            return response("❌ Participante no encontrado con ID: $codigo", 404);
        }

        $id_evento = $participante->ID_Evento;
        $rfc = trim($participante->RFC);

        if (!$id_evento) {
            return response('❌ Participante sin evento válido.', 400);
        }

        // 2) Buscar configuración del proveedor para este evento
        $prov_ev = DB::table('proveedor_evento')
            ->where('NombreProveedor', $usuario)
            ->where('ID_Evento', $id_evento)
            ->where('Activo', 1)
            ->first();

        if (!$prov_ev) {
            return response('⚠️ No tienes puntos configurados para este evento.', 400);
        }

        $puntos_a_dar = $prov_ev->Puntos;
        if ($puntos_a_dar <= 0) {
            return response('⚠️ Configuración de puntos inválida para este evento.', 400);
        }

        // 3) Validar Cooldown de 2 minutos
        $ultimo_registro = DB::table('puntos_proveedor')
            ->where('id_participante', $participante->ID)
            ->where('usuario', $usuario)
            ->where('id_evento', $id_evento)
            ->orderBy('fecha', 'desc')
            ->first();

        if ($ultimo_registro) {
            $segundos_transcurridos = time() - strtotime($ultimo_registro->fecha);
            if ($segundos_transcurridos < 120) {
                $restan = 120 - $segundos_transcurridos;
                $mins = floor($restan / 60);
                $secs = $restan % 60;
                return response("⏳ Debes esperar 2 minutos para volver a dar puntos a este participante. Faltan {$mins}m {$secs}s.", 400);
            }
        }

        // 4) Procesar puntos según la configuración del evento
        $evento = Evento::find($id_evento);
        $tipo_puntos = $evento ? $evento->tipo_puntos : 'ninguno';

        DB::beginTransaction();
        try {
            // Registrar en el historial de puntos_proveedor
            DB::table('puntos_proveedor')->insert([
                'id_participante' => $participante->ID,
                'id_evento'       => $id_evento,
                'usuario'         => $usuario,
                'puntos'          => $puntos_a_dar,
                'fecha'           => now(),
            ]);

            $mensaje_wallet = "";

            if ($tipo_puntos === 'grupal' && !empty($rfc)) {
                // Sumar a la bolsa grupal por RFC
                DB::table('puntos_rfc')
                    ->updateOrInsert(
                        ['RFC' => $rfc, 'ID_Evento' => $id_evento],
                        ['Puntos' => DB::raw("Puntos + $puntos_a_dar")]
                    );
                
                $nuevo_saldo = DB::table('puntos_rfc')
                    ->where('RFC', $rfc)
                    ->where('ID_Evento', $id_evento)
                    ->value('Puntos');
                    
                $mensaje_wallet = "💼 Saldo grupal actualizado: $nuevo_saldo pts.";
            } elseif ($tipo_puntos === 'individual') {
                // Sumar directo al participante
                $participante->increment('Puntos', $puntos_a_dar);
                $mensaje_wallet = "👤 Saldo individual: {$participante->Puntos} pts.";
            } else {
                $mensaje_wallet = "ℹ️ Evento sin sistema de puntos (Visita registrada).";
            }

            DB::commit();

            return response("✅ $puntos_a_dar puntos asignados a {$participante->Nombre}\n$mensaje_wallet", 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response("❌ Error al registrar puntos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Muestra la pantalla de gestión de proveedores para el admin.
     */
    public function gestion()
    {
        $rol = strtolower(Auth::user()->Rol);
        if ($rol !== 'admin' && $rol !== 'administrador') {
            abort(403, 'No tienes permiso para acceder a esta pantalla. Tu rol actual es: ' . Auth::user()->Rol);
        }

        $proveedores = Usuario::where('Rol', 'proveedor')->get();
        return view('proveedor.gestion', compact('proveedores'));
    }

    /**
     * Crea un nuevo usuario proveedor.
     */
    public function storeUsuario(Request $request)
    {
        $rol = strtolower(Auth::user()->Rol);
        if ($rol !== 'admin' && $rol !== 'administrador') {
            abort(403);
        }

        $request->validate([
            'username' => 'required|string|max:255|unique:usuarios,username',
            'password' => 'required|string|min:4',
        ]);

        Usuario::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'Rol' => 'proveedor',
            'password_visible' => $request->password,
        ]);

        return redirect()->back()->with('success', 'Proveedor creado correctamente.');
    }
}
