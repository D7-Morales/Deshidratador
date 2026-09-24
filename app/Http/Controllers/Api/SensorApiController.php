<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\LecturaSensor;
use App\Models\ProcesoDeshidratacion;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SensorApiController extends Controller
{
    /**
     * Store a new sensor reading or a batch of readings.
     */
    public function store(Request $request)
    {
        $input = $request->all();

        // Check if the input is a batch (array of readings) or a single reading
        if (is_array($input) && isset($input[0]) && is_array($input[0])) {
            // It's a batch of readings
            $results = [];
            foreach ($input as $readingData) {
                $processed = $this->processSingleReading($readingData);
                $results[] = $processed;
            }
            return response()->json([
                'success' => true,
                'message' => 'Lote de datos procesado',
                'details' => $results
            ], 201);
        } else {
            // It's a single reading
            $processed = $this->processSingleReading($input);
            if (!$processed['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $processed['message'],
                    'errors' => $processed['errors'] ?? null
                ], $processed['code']);
            }
            return response()->json([
                'success' => true,
                'message' => 'Datos almacenados correctamente'
            ], 201);
        }
    }

    /**
     * Helper to process and save a single reading.
     */
    private function processSingleReading(array $data): array
    {
        $validator = Validator::make($data, [
            'temperatura' => 'required|numeric',
            'humedad' => 'required|numeric',
            'presion' => 'nullable|numeric',
            'fecha_hora' => 'nullable|string', 
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validación fallida',
                'errors' => $validator->errors(),
                'code' => 422
            ];
        }

        // Find the first active sensor
        $sensor = Sensor::where('estado_sensor', 'activo')->first();
        if (!$sensor) {
            return [
                'success' => false,
                'message' => 'No hay sensores activos registrados en el sistema.',
                'code' => 404
            ];
        }

        // Find if there is an active dehydration process
        $procesoActivo = ProcesoDeshidratacion::where('estado_proceso', 'activo')->first();
        $idProceso = $procesoActivo ? $procesoActivo->id_proceso : null;

        // Parse date
        $fechaHora = isset($data['fecha_hora']) ? Carbon::parse($data['fecha_hora']) : now();

        // Create the reading
        
        $lectura = LecturaSensor::create([
            'id_sensor' => $sensor->id_sensor,
            'id_proceso' => $idProceso,
            'temperatura' => $data['temperatura'],
            'humedad' => $data['humedad'],
            'presion' => $data['presion'] ?? 1013.25,
            'estado_ventilador' => $data['ventilador_activado'] ?? 0,
            'fecha_hora' => $fechaHora,
        ]);

        // Auto generation of alerts
        if ($idProceso) {
            // Thresholds: temp > 70°C or humidity < 20%
            if ($data['temperatura'] > 70.0) {
                Alerta::create([
                    'id_proceso' => $idProceso,
                    'id_lectura' => $lectura->id_lectura,
                    'tipo_alerta' => 'temperatura',
                    'mensaje' => 'Temperatura crítica superada: ' . number_format($data['temperatura'], 1) . '°C (Máx. 70.0°C)',
                    'atendida' => 0,
                    'fecha_alerta' => $fechaHora
                ]);
            }

            if ($data['humedad'] < 20.0) {
                Alerta::create([
                    'id_proceso' => $idProceso,
                    'id_lectura' => $lectura->id_lectura,
                    'tipo_alerta' => 'humedad',
                    'mensaje' => 'Humedad crítica detectada: ' . number_format($data['humedad'], 1) . '% (Mín. 20.0%)',
                    'atendida' => 0,
                    'fecha_alerta' => $fechaHora
                ]);
            }
        }

        return [
            'success' => true,
            'id_lectura' => $lectura->id_lectura,
            'code' => 201
        ];
    }

        /**
     * Obtener la última lectura del sensor (Para tiempo real en Android)
     */
    public function getLatestReading()
    {
        $lectura = \App\Models\LecturaSensor::orderBy('id_lectura', 'desc')->first();
        
        if (!$lectura) {
            return response()->json(['message' => 'No hay lecturas disponibles'], 404);
        }
        
        // Devolvemos exactamente los campos que espera SensorReading.kt
        return response()->json([
            'id_lectura' => $lectura->id_lectura,
            'id_sensor' => $lectura->id_sensor,
            'id_proceso' => $lectura->id_proceso,
            'temperatura' => (float) $lectura->temperatura,
            'humedad' => (float) $lectura->humedad,
            'presion' => (float) $lectura->presion,
            'fecha_hora' => $lectura->fecha_hora->format('Y-m-d H:i:s'), // Formato que espera tu SimpleDateFormat
            'created_at' => $lectura->created_at ? $lectura->created_at->format('Y-m-d H:i:s') : null
        ]);
    }

    /**
     * Obtener lista de lecturas (Para el historial/gráfico en Android)
     */
    public function getReadings(\Illuminate\Http\Request $request)
    {
        $procesoId = $request->query('proceso_id');
        $limit = (int) $request->query('limit', 50);
        
        $query = \App\Models\LecturaSensor::orderBy('id_lectura', 'desc');
        
        if ($procesoId) {
            $query->where('id_proceso', $procesoId);
        }
        
        $lecturas = $query->take($limit)->get();
        
        // IMPORTANTE: Devolvemos un array JSON plano
        return response()->json($lecturas->map(function ($lectura) {
            return [
                'id_lectura' => $lectura->id_lectura,
                'id_sensor' => $lectura->id_sensor,
                'id_proceso' => $lectura->id_proceso,
                'temperatura' => (float) $lectura->temperatura,
                'humedad' => (float) $lectura->humedad,
                'presion' => (float) $lectura->presion,
                'fecha_hora' => $lectura->fecha_hora->format('Y-m-d H:i:s'),
                'created_at' => $lectura->created_at ? $lectura->created_at->format('Y-m-d H:i:s') : null
            ];
        })->toArray());
    }
}
