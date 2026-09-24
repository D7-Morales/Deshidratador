<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProcesoDeshidratacion;
use App\Models\LecturaSensor;
use App\Models\Alerta;
use Illuminate\Http\Request;

class MetricasController extends Controller
{
    /**
     * Get optimized metrics for both Web and Mobile App.
     */
        public function getMetrics()
    {
        // 1. Cantidad de procesos totales
        $totalProcesos = ProcesoDeshidratacion::count();

        // 2. Peso inicial / final total (en kg)
        $pesoInicialTotalGramos = ProcesoDeshidratacion::sum('peso_inicial_gramos');
        $pesoFinalTotalGramos = ProcesoDeshidratacion::where('estado_proceso', 'completado')->sum('peso_final_gramos');
        
        $pesoInicialTotalKg = $pesoInicialTotalGramos / 1000;
        $pesoFinalTotalKg = $pesoFinalTotalGramos / 1000;

        // 3. % de secado promedio
        $procesosCompletados = ProcesoDeshidratacion::where('estado_proceso', 'completado')
            ->where('peso_inicial_gramos', '>', 0)
            ->whereNotNull('peso_final_gramos')
            ->get();

        $sumaPorcentajeSecado = 0;
        $cantidadCompletados = $procesosCompletados->count();

        foreach ($procesosCompletados as $proceso) {
            $dif = $proceso->peso_inicial_gramos - $proceso->peso_final_gramos;
            $sumaPorcentajeSecado += ($dif / $proceso->peso_inicial_gramos) * 100;
        }

        $porcentajeSecadoPromedio = $cantidadCompletados > 0 ? ($sumaPorcentajeSecado / $cantidadCompletados) : 0;

        // 4. Tiempo total acumulado
        $tiempoTotalHoras = ProcesoDeshidratacion::sum('duracion_horas');

        // 5. Temperatura y humedad promedio globales
        $tempPromedio = LecturaSensor::avg('temperatura') ?? 0;
        $humPromedio = LecturaSensor::avg('humedad') ?? 0;

        // 6. Número de alertas totales
        $totalAlertas = Alerta::count();

        // 7. Fruta recuperada
        $frutaRecuperadaKg = $pesoFinalTotalKg;

        // Extra: online/offline status y estado del ventilador
        $ultimaLectura = LecturaSensor::orderBy('id_lectura', 'desc')->first();
        $isOnline = false;
        $estadoVentilador = 'DESACTIVADO';
        
        if ($ultimaLectura) {
            $isOnline = now()->diffInMinutes($ultimaLectura->fecha_hora) < 2;
            
            if (isset($ultimaLectura->estado_ventilador) && $ultimaLectura->estado_ventilador == 1) {
                $estadoVentilador = 'ACTIVADO';
            }
        }

        // === AQUÍ ESTÁ LA CLAVE: Usamos los nombres exactos que tu App Android espera ===
        return response()->json([
            'total_procesos' => $totalProcesos,
            'peso_inicial_promedio' => round($pesoInicialTotalKg, 2),       // Coincide con Android
            'peso_final_promedio' => round($pesoFinalTotalKg, 2),           // Coincide con Android
            'porcentaje_secado_promedio' => round($porcentajeSecadoPromedio, 2),
            'tiempo_total_deshidratacion' => round($tiempoTotalHoras, 2),   // Coincide con Android
            'temperatura_promedio' => round($tempPromedio, 2),
            'humedad_promedio' => round($humPromedio, 2),
            'total_alertas' => $totalAlertas,
            'fruta_recuperada_kg' => round($frutaRecuperadaKg, 2),
            
            // Nuevos campos agregados
            'is_online' => $isOnline,
            'estado_ventilador' => $estadoVentilador,
            'ultima_conexion' => $ultimaLectura ? $ultimaLectura->fecha_hora->format('d/m/Y H:i:s') : 'Sin registros'
        ]);
    }

    /**
     * Get alerts for the active dehydration process.
     */
        /**
     * Get alerts (Devuelve un array plano para coincidir con Android)
     */
    public function getAlerts(Request $request)
    {
        $procesoId = $request->query('proceso_id');
        $limit = (int) $request->query('limit', 50);

        $query = Alerta::orderBy('id_alerta', 'desc');

        if ($procesoId) {
            $query->where('id_proceso', $procesoId);
        }

        $alertas = $query->take($limit)->get();

        // Devolver un array JSON plano [...] en lugar de {"success": true, "alerts": [...]}
        return response()->json($alertas->map(function ($alerta) {
            return [
                'id_alerta' => $alerta->id_alerta,
                'id_proceso' => $alerta->id_proceso,
                'id_lectura' => $alerta->id_lectura,
                'tipo_alerta' => $alerta->tipo_alerta,
                'mensaje' => $alerta->mensaje,
                'atendida' => (bool) $alerta->atendida, // Asegura que sea booleano
                'fecha_alerta' => $alerta->fecha_alerta->format('Y-m-d H:i:s'), // Formato que espera SimpleDateFormat en Android
            ];
        })->toArray());
    }
}