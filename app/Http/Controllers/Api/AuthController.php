<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        // Validar datos de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar usuario por email
        $usuario = Usuario::where('email_usuario', $request->email)
            ->with('rol')
            ->first();

        // Verificar si existe el usuario
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas. Usuario no encontrado.'
            ], 401);
        }

        // Verificar la contraseña
        if (!Hash::check($request->password, $usuario->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas. Contraseña incorrecta.'
            ], 401);
        }

        // Verificar que el usuario esté activo
        if ($usuario->estado_usuario !== 'activo') {
            return response()->json([
                'success' => false,
                'message' => 'Usuario inactivo. Contacta al administrador.'
            ], 403);
        }

        // Generar token de autenticación (Laravel Sanctum)
        $token = $usuario->createToken('auth_token')->plainTextToken;

        // Retornar datos del usuario + token
        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'id_usuario' => $usuario->id_usuario,
                'ci_usuario' => $usuario->ci_usuario,
                'nombres_usuario' => $usuario->nombres_usuario,
                'apellidos_usuario' => $usuario->apellidos_usuario,
                'email_usuario' => $usuario->email_usuario,
                'id_rol' => $usuario->id_rol,
                'rol' => $usuario->rol,
                'estado_usuario' => $usuario->estado_usuario,
                'token' => $token
            ]
        ], 200);
    }

    /**
     * Logout - Cerrar sesión
     */
    public function logout(Request $request)
    {
        // Eliminar el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ]);
    }

    /**
     * Obtener usuario autenticado
     */
    public function user(Request $request)
    {
        $usuario = $request->user()->load('rol');

        return response()->json([
            'success' => true,
            'data' => $usuario
        ]);
    }
}