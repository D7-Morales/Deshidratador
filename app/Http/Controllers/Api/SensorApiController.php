<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\LecturaSensor;
use App\Models\CargaFruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SensorApiController extends Controller
{
    /**
     * Store a new sensor reading.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temperatura' => 'required|numeric',
            'humedad' => 'required|numeric',
            'presion' => 'required|numeric',
            'radiacion_solar' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validación fallida',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the first active sensor
        $sensor = Sensor::where('estado_sensor', 'activo')->first();

        if (!$sensor) {
            return response()->json([
                'success' => false,
                'message' => 'No hay sensores activos registrados en el sistema.'
            ], 404);
        }

        // Find if there is an active dehydration load/process
        $cargaActiva = CargaFruta::where('estado_proceso', 'activo')->first();
        $idCarga = $cargaActiva ? $cargaActiva->id_carga : null;

        // Save reading matching new table columns
        LecturaSensor::create([
            'id_sensor' => $sensor->id_sensor,
            'id_carga' => $idCarga,
            'temperatura' => $request->temperatura,
            'humedad' => $request->humedad,
            'presion' => $request->presion,
            'radiacion_solar' => $request->radiacion_solar ?? null,
            'fecha_hora' => now(), // Laravel's current timezone-aware timestamp
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Datos almacenados correctamente'
        ], 201);
    }
}
