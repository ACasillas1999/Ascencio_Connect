<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Temporal: Ejecutar migración SQL
        try {
            if (!\Schema::hasColumn('evento', 'gafete_qr_x')) {
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_qr_x` INT DEFAULT 1755");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_qr_y` INT DEFAULT 280");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_nombre_x` INT DEFAULT 202");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_nombre_y` INT DEFAULT 1050");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_font_size` INT DEFAULT 60");
            }
        } catch (\Exception $e) {
            \Log::error("Error en migración temporal: " . $e->getMessage());
        }

        if (Auth::check()) {
            if (Auth::user()->Rol === 'Vendedor') {
                return redirect()->route('participantes.index');
            }
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            if (Auth::user()->Rol === 'Vendedor') {
                return redirect()->intended(route('participantes.index'));
            }
            if (Auth::user()->Rol === 'Evento') {
                if (Auth::user()->ID_Evento) {
                    return redirect()->intended(route('eventos.show', Auth::user()->ID_Evento));
                }
                // Si no tiene evento asignado, cerrarle sesión por seguridad
                Auth::logout();
                return back()->withErrors(['username' => 'Este usuario Evento no tiene ningún evento asignado. Contacta al administrador.']);
            }
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => 'Usuario o contraseña incorrectos.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
