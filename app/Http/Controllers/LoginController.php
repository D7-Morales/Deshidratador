<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (session()->has('id_usuario')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle authentication.
     */
    public function login(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
        ], [
            'usuario.required' => 'El correo electrónico o CI es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Search user by email or CI
        $usuario = Usuario::with('rol')
            ->where('email_usuario', $request->usuario)
            ->orWhere('ci_usuario', $request->usuario)
            ->first();

        if (!$usuario) {
            return back()->withErrors([
                'usuario' => 'Las credenciales proporcionadas no coinciden con nuestros registros.'
            ])->withInput($request->only('usuario'));
        }

        // Validate state (active, inactive, suspended)
        if (strtolower($usuario->estado_usuario) !== 'activo') {
            return back()->withErrors([
                'usuario' => 'Este usuario se encuentra inactivo o suspendido. Consulte al administrador.'
            ])->withInput($request->only('usuario'));
        }

        // Validate password hash (Bcrypt)
        if (!Hash::check($request->password, $usuario->password_hash)) {
            return back()->withErrors([
                'password' => 'La contraseña ingresada es incorrecta.'
            ])->withInput($request->only('usuario'));
        }

        // Create Manual Session
        session([
            'id_usuario' => $usuario->id_usuario,
            'nombre_completo' => $usuario->nombre_completo,
            'usuario_username' => $usuario->email_usuario,
            'rol' => strtoupper($usuario->rol->nombre_rol ?? 'OPERADOR'),
        ]);

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', '¡Bienvenido al sistema, ' . $usuario->nombre_completo . '!');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $request->session()->forget(['id_usuario', 'nombre_completo', 'usuario_username', 'rol']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')->with('success', 'Sesión cerrada correctamente.');
    }
}
