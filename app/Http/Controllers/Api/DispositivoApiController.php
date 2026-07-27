<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DispositivoControl;
use App\Models\ComandoControl;
use App\Models\Deshidratador;
use App\Models\Sensor;
use App\Models\LecturaSensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DispositivoApiController extends Controller
{
    /**
     * Listar todos los dispositivos
     */
    public function index(Request $request)
    {
        $query = DispositivoControl::with('deshidratador');

        // Filtrar por deshidratador si se proporciona
        if ($request->has('deshidratador_id')) {
            $query->where('id_deshidratador', $request->deshidratador_id);
        }

        $dispositivos = $query->get();

        return response()->json([
            'success' => true,
            'data' => $dispositivos,
            'total' => $dispositivos->count()
        ]);
    }

    /**
     * Mostrar un dispositivo específico
     */
    public function show($id)
    {
        $dispositivo = DispositivoControl::with('deshidratador')->find($id);

        if (!$dispositivo) {
            return response()->json([
                'success' => false,
                'message' => 'Dispositivo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $dispositivo
        ]);
    }

    /**
     * Enviar comando a un dispositivo
     */
    public function sendCommand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_dispositivo' => 'required|exists:dispositivos_control,id_dispositivo',
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'accion' => 'required|in:activar,desactivar,ajustar',
            'id_carga' => 'nullable|exists:cargas_fruta,id_carga',
            'origen' => 'nullable|in:manual,automatico,ia'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear el comando
        $comando = ComandoControl::create([
            'id_dispositivo' => $request->id_dispositivo,
            'id_usuario' => $request->id_usuario,
            'id_carga' => $request->id_carga,
            'accion' => $request->accion,
            'origen' => $request->origen ?? 'manual',
            'estado_ejecucion' => 'pendiente',
            'fecha_envio' => now()
        ]);

        // Actualizar estado del dispositivo según la acción
        $dispositivo = DispositivoControl::find($request->id_dispositivo);
        
        if ($request->accion === 'activar') {
            $dispositivo->update(['estado_actual' => 'activo']);
        } elseif ($request->accion === 'desactivar') {
            $dispositivo->update(['estado_actual' => 'inactivo']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Comando enviado exitosamente',
            'data' => $comando,
            'dispositivo' => $dispositivo
        ], 201);
    }

    /**
     * Obtener estado de todos los dispositivos de un deshidratador
     */
    public function estadoDispositivos($deshidratadorId)
    {
        $dispositivos = DispositivoControl::where('id_deshidratador', $deshidratadorId)->get();

        return response()->json([
            'success' => true,
            'data' => $dispositivos
        ]);
    }

    /**
     * Obtener sensores de un deshidratador
     */
    public function sensoresDeshidratador($deshidratadorId)
    {
        $sensores = Sensor::where('id_deshidratador', $deshidratadorId)
            ->with('tipoSensor')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sensores
        ]);
    }

    /**
     * Obtener última lectura de todos los sensores
     */
    public function ultimaLectura()
    {
        $ultimaLectura = LecturaSensor::with('sensor')
            ->orderBy('fecha_hora', 'desc')
            ->first();

        if (!$ultimaLectura) {
            return response()->json([
                'success' => true,
                'message' => 'No hay lecturas registradas',
                'data' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $ultimaLectura
        ]);
    }

    /**
     * Obtener lecturas de un sensor específico
     */
    public function lecturasSensor($sensorId, Request $request)
    {
        $query = LecturaSensor::where('id_sensor', $sensorId);

        // Filtrar por carga si se proporciona
        if ($request->has('carga_id')) {
            $query->where('id_carga', $request->carga_id);
        }

        // Limitar resultados
        $limit = $request->limit ?? 50;

        $lecturas = $query->orderBy('fecha_hora', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $lecturas,
            'total' => $lecturas->count()
        ]);
    }

    /**
     * Obtener información completa de un deshidratador
     */
    public function infoDeshidratador($id)
    {
        $deshidratador = Deshidratador::with(['sensores', 'dispositivosControl'])->find($id);

        if (!$deshidratador) {
            return response()->json([
                'success' => false,
                'message' => 'Deshidratador no encontrado'
            ], 404);
        }

        // Obtener última lectura de cada sensor
        $sensoresConLectura = $deshidratador->sensores->map(function ($sensor) {
            $ultimaLectura = LecturaSensor::where('id_sensor', $sensor->id_sensor)
                ->orderBy('fecha_hora', 'desc')
                ->first();
            
            $sensor->ultima_lectura = $ultimaLectura;
            return $sensor;
        });

        $deshidratador->sensores = $sensoresConLectura;

        return response()->json([
            'success' => true,
            'data' => $deshidratador
        ]);
    }
}