<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use App\Models\Evento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ProveedorController extends Controller
{
    /**
     * Muestra la interfaz de escaneo para el proveedor.
     */
    public function index(Request $request)
    {
        $usuario = Auth::user()->username;
        
        // Obtener todos los eventos en los que este proveedor está asignado
        $eventos_asignados = collect();
        if (Schema::hasTable('proveedor_evento')) {
            $eventos_asignados = DB::table('proveedor_evento')
                ->join('evento', 'evento.ID', '=', 'proveedor_evento.ID_Evento')
                ->where('proveedor_evento.NombreProveedor', $usuario)
                ->where('proveedor_evento.Activo', 1)
                ->select('proveedor_evento.Puntos', 'evento.name_evento', 'evento.ID as ID_Evento')
                ->get();
        }

        $selected_evento_id = $request->query('evento_id');
        
        $proveedor_evento = null;
        if ($selected_evento_id) {
            $proveedor_evento = $eventos_asignados->firstWhere('ID_Evento', $selected_evento_id);
        }
        if (!$proveedor_evento) {
            $proveedor_evento = $eventos_asignados->first();
        }

        $puntos = $proveedor_evento ? $proveedor_evento->Puntos : 0;
        $evento_nombre = $proveedor_evento ? $proveedor_evento->name_evento : 'Sin asignar';
        $id_evento = $proveedor_evento ? $proveedor_evento->ID_Evento : null;

                $historial = collect();
        $total_puntos_entregados = 0;
        $total_escaneos = 0;
        $total_prospectos = 0;

        if ($id_evento && Schema::hasTable('puntos_proveedor')) {
            // Intento de auto-migración segura
            if (!Schema::hasColumn('puntos_proveedor', 'es_prospecto')) {
                try {
                    DB::statement("ALTER TABLE `puntos_proveedor` ADD COLUMN `es_prospecto` TINYINT(1) NOT NULL DEFAULT 0");
                } catch (\Exception $e) {}
            }

            $hasProspectoCol = Schema::hasColumn('puntos_proveedor', 'es_prospecto');
            $prospectoField = $hasProspectoCol ? 'puntos_proveedor.es_prospecto' : DB::raw('0 as es_prospecto');
            $rfcField = Schema::hasColumn('participante', 'RFC') ? 'participante.RFC' : DB::raw("'' as RFC");
            $empresaField = Schema::hasColumn('participante', 'Empresa') ? 'participante.Empresa as participante_empresa' : DB::raw("'' as participante_empresa");
            $telefonoField = Schema::hasColumn('participante', 'Telefono') ? 'participante.Telefono as participante_telefono' : DB::raw("'' as participante_telefono");

            $historial = DB::table('puntos_proveedor')
                ->join('participante', 'participante.ID', '=', 'puntos_proveedor.id_participante')
                ->where('puntos_proveedor.usuario', $usuario)
                ->where('puntos_proveedor.id_evento', $id_evento)
                ->select(
                    'puntos_proveedor.ID as id_registro',
                    'puntos_proveedor.id_participante',
                    'puntos_proveedor.puntos',
                    'puntos_proveedor.fecha',
                    $prospectoField,
                    'participante.Nombre as participante_nombre',
                    $rfcField,
                    $empresaField,
                    $telefonoField
                )
                ->orderBy('puntos_proveedor.fecha', 'desc')
                ->take(50)
                ->get();

            $total_puntos_entregados = DB::table('puntos_proveedor')
                ->where('usuario', $usuario)
                ->where('id_evento', $id_evento)
                ->sum('puntos');

            $total_escaneos = DB::table('puntos_proveedor')
                ->where('usuario', $usuario)
                ->where('id_evento', $id_evento)
                ->count();

            $total_prospectos = $hasProspectoCol ? DB::table('puntos_proveedor')
                ->where('usuario', $usuario)
                ->where('id_evento', $id_evento)
                ->where('es_prospecto', 1)
                ->count() : 0;
        }

        return view('proveedor.index', compact(
            'usuario', 'puntos', 'evento_nombre', 'id_evento', 'eventos_asignados',
            'historial', 'total_puntos_entregados', 'total_escaneos', 'total_prospectos'
        ));
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
            return response()->json(['ok' => false, 'message' => "Participante no encontrado con ID: $codigo"], 404);
        }

        $id_evento = $participante->ID_Evento;
        $rfc = trim($participante->RFC);

        if (!$id_evento) {
            return response()->json(['ok' => false, 'message' => 'Participante sin evento válido.'], 400);
        }

        // 2) Buscar configuración del proveedor para este evento
        $prov_ev = DB::table('proveedor_evento')
            ->where('NombreProveedor', $usuario)
            ->where('ID_Evento', $id_evento)
            ->where('Activo', 1)
            ->first();

        if (!$prov_ev) {
            return response()->json(['ok' => false, 'message' => 'No tienes puntos configurados para este evento.'], 400);
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
                return response()->json(['ok' => false, 'cooldown' => true, 'message' => "Debes esperar 2 minutos para volver a otorgar puntos a este participante. Faltan {$mins}m {$secs}s."], 429);
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
            abort(403, 'No tienes permiso para acceder a esta pantalla.');
        }

        $proveedores = Usuario::whereIn('Rol', ['proveedor', 'Proveedor'])->get();

        foreach ($proveedores as $prov) {
            $prov->asignaciones = DB::table('proveedor_evento')
                ->join('evento', 'evento.ID', '=', 'proveedor_evento.ID_Evento')
                ->where('proveedor_evento.NombreProveedor', $prov->username)
                ->select('proveedor_evento.ID as id_asignacion', 'proveedor_evento.Puntos', 'proveedor_evento.Activo', 'evento.name_evento', 'evento.ID as ID_Evento')
                ->get();
        }

        $eventos = Evento::orderBy('fecha_inicio', 'desc')->get();

        return view('proveedor.gestion', compact('proveedores', 'eventos'));
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

    /**
     * Elimina una cuenta de usuario proveedor.
     */
    public function destroyUsuario(Usuario $usuario)
    {
        $rol = strtolower(Auth::user()->Rol);
        if ($rol !== 'admin' && $rol !== 'administrador') {
            abort(403);
        }

        $usuario->delete();

        return redirect()->back()->with('success', 'Cuenta de proveedor eliminada.');
    }

    /**
     * Actualiza la cuenta de usuario proveedor.
     */
    public function updateUsuario(Request $request, Usuario $usuario)
    {
        $rol = strtolower(Auth::user()->Rol);
        if ($rol !== 'admin' && $rol !== 'administrador') {
            abort(403);
        }

        $request->validate([
            'username' => 'required|string|max:255|unique:usuarios,username,' . $usuario->ID . ',ID',
            'password' => 'nullable|string|min:4',
        ]);

        $usuario->username = $request->username;
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
            $usuario->password_visible = $request->password;
        }
        $usuario->save();

        return redirect()->back()->with('success', 'Cuenta de proveedor actualizada correctamente.');
    }

    /**
     * Alterna el estado de prospecto para un registro de punto otorgado.
     */
    public function toggleProspecto(Request $request)
    {
        $usuario = Auth::user()->username;
        $id_registro = $request->input('id_registro');

        if (!Schema::hasColumn('puntos_proveedor', 'es_prospecto')) {
            try {
                DB::statement("ALTER TABLE `puntos_proveedor` ADD COLUMN `es_prospecto` TINYINT(1) NOT NULL DEFAULT 0");
            } catch (\Exception $e) {}
        }

        if (!$id_registro) {
            return response()->json(['ok' => false, 'message' => 'ID de registro inválido.'], 400);
        }

        $registro = DB::table('puntos_proveedor')
            ->where('ID', $id_registro)
            ->where('usuario', $usuario)
            ->first();

        if (!$registro) {
            return response()->json(['ok' => false, 'message' => 'Registro no encontrado o sin permisos.'], 404);
        }

        $nuevoEstado = !((bool)$registro->es_prospecto);

        DB::table('puntos_proveedor')
            ->where('ID', $id_registro)
            ->update(['es_prospecto' => $nuevoEstado ? 1 : 0]);

        return response()->json([
            'ok' => true,
            'es_prospecto' => $nuevoEstado,
            'message' => $nuevoEstado ? 'Marcado como prospecto' : 'Desmarcado de prospectos'
        ]);
    }

    /**
     * Obtiene el detalle de prospectos y métricas de un proveedor para un evento.
     */
        /**
     * Obtiene el detalle de prospectos y métricas de un proveedor para un evento con todos los campos.
     */
    public function getProspectosProveedor(Request $request, Evento $evento, $usuario)
    {
        if (!Schema::hasTable('puntos_proveedor')) {
            return response()->json([
                'ok' => true,
                'usuario' => $usuario,
                'evento_nombre' => $evento->name_evento,
                'total_escaneos' => 0,
                'total_puntos' => 0,
                'total_prospectos' => 0,
                'tasa_conversion' => 0,
                'prospectos' => [],
                'todos' => []
            ]);
        }

        $hasProspectoCol = Schema::hasColumn('puntos_proveedor', 'es_prospecto');
        $prospectoField = $hasProspectoCol ? 'puntos_proveedor.es_prospecto' : DB::raw('0 as es_prospecto');

        $partCols = Schema::getColumnListing('participante');
        $selectFields = [
            'puntos_proveedor.ID as id_registro',
            'puntos_proveedor.id_participante',
            'puntos_proveedor.puntos as puntos_otorgados',
            'puntos_proveedor.fecha as fecha_escaneo',
            $prospectoField,
            'participante.Nombre as participante_nombre',
            'participante.Puntos as participante_puntos_totales'
        ];

        $extraCols = ['RFC', 'Empresa', 'Telefono', 'Sucursal', 'Vendedor', 'Proveedor', 'QR_Code'];
        foreach ($extraCols as $col) {
            if (in_array($col, $partCols)) {
                $selectFields[] = 'participante.' . $col;
            } else {
                $selectFields[] = DB::raw("'' as " . $col);
            }
        }

        $registros = DB::table('puntos_proveedor')
            ->join('participante', 'participante.ID', '=', 'puntos_proveedor.id_participante')
            ->where('puntos_proveedor.usuario', $usuario)
            ->where('puntos_proveedor.id_evento', $evento->ID)
            ->select($selectFields)
            ->orderBy('puntos_proveedor.fecha', 'desc')
            ->get();

        $prospectos = $registros->filter(function($r) {
            return !empty($r->es_prospecto);
        })->values();

        $totalPuntos = $registros->sum('puntos_otorgados');
        $totalEscaneos = $registros->count();
        $totalProspectos = $prospectos->count();

        return response()->json([
            'ok' => true,
            'usuario' => $usuario,
            'evento_nombre' => $evento->name_evento,
            'total_escaneos' => $totalEscaneos,
            'total_puntos' => $totalPuntos,
            'total_prospectos' => $totalProspectos,
            'tasa_conversion' => $totalEscaneos > 0 ? round(($totalProspectos / $totalEscaneos) * 100, 1) : 0,
            'prospectos' => $prospectos,
            'todos' => $registros
        ]);
    }
}
