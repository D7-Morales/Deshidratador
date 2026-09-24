<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComandoController extends Controller
{
    /**
     * Store a new command from mobile app
     */
    public function store(Request $request)
    {
        // Validar datos recibidos
        $validated = $request->validate([
            'id_usuario' => 'required|integer',
            'id_proceso' => 'nullable|integer',
            'accion' => 'required|string|max:100',
            'origen' => 'nullable|string|max:50'
        ]);

        try {
            // Insertar comando en la base de datos
            $comandoId = DB::table('comandos_control')->insertGetId([
                'id_usuario' => $validated['id_usuario'],
                'id_proceso' => $validated['id_proceso'] ?? null,
                'accion' => $validated['accion'],
                'origen' => $validated['origen'] ?? 'manual_app',
                'estado' => 'pendiente',
                'fecha_creacion' => now(),
                'fecha_ejecucion' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comando registrado correctamente',
                'data' => [
                    'id_comando' => $comandoId,
                    'estado' => 'pendiente'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el comando: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Obtener comandos pendientes para el ESP32
 */
public function getPendientes()
{
    $comandos = \App\Models\ComandoControl::where('estado', 'pendiente')
        ->orderBy('id_comando', 'asc')
        ->take(5) // Máximo 5 comandos por consulta
        ->get();

    return response()->json([
        'success' => true,
        'comandos' => $comandos->map(function ($comando) {
            return [
                'id_comando' => $comando->id_comando,
                'id_dispositivo' => $comando->id_dispositivo,
                'accion' => $comando->accion,
                'origen' => $comando->origen,
            ];
        })
    ]);
}

/**
 * Marcar comando como ejecutado
 */
public function marcarEjecutado($id)
{
    try {
        // ✅ CONSULTA EXPLÍCITA: Busca por 'id_comando', ignorando cualquier confusión con 'id'
        $comando = \App\Models\ComandoControl::where('id_comando', $id)->first();
        
        if (!$comando) {
            return response()->json([
                'success' => false,
                'message' => 'Comando no encontrado'
            ], 404);
        }

        // ✅ ACTUALIZAR EXPLÍCITAMENTE la columna 'estado'
        $comando->estado = 'ejecutado';
        $comando->fecha_ejecucion = now(); 
        $comando->save();

        return response()->json([
            'success' => true,
            'message' => 'Comando marcado como ejecutado'
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error del servidor: ' . $e->getMessage()
        ], 500);
    }
}

}