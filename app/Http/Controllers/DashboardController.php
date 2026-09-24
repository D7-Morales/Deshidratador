<?php

namespace App\Http\Controllers;

use App\Models\ProcesoDeshidratacion;
use App\Models\LecturaSensor;
use App\Models\Alerta;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the main dashboard with metrics and recent readings.
     */
    public function index()
    {
        // 1. Cantidad de procesos totales
        $totalProcesos = ProcesoDeshidratacion::count();

        // 2. Peso inicial y final total (en kg)
        $pesoInicialTotalKg = ProcesoDeshidratacion::sum('peso_inicial_gramos') / 1000;
        $pesoFinalTotalKg = ProcesoDeshidratacion::where('estado_proceso', 'completado')->sum('peso_final_gramos') / 1000;

        // 3. % de secado promedio (sobre procesos completados)
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

        // 4. Tiempo total acumulado de deshidratado (en horas)
        $tiempoTotalHoras = ProcesoDeshidratacion::sum('duracion_horas');

        // 5. Temperatura y humedad promedio globales
        $tempPromedio = LecturaSensor::avg('temperatura') ?? 0;
        $humPromedio = LecturaSensor::avg('humedad') ?? 0;

        // 6. Número de alertas totales
        $totalAlertas = Alerta::count();

        // 7. Fruta recuperada (Suma de peso final en kg)
        $frutaRecuperadaKg = $pesoFinalTotalKg;

        // Ultima lectura para las tarjetas
        $ultimaLectura = LecturaSensor::orderBy('id_lectura', 'desc')->first();

        // Ultimas 10 lecturas para la tabla
        $ultimasLecturas = LecturaSensor::orderBy('id_lectura', 'desc')->take(10)->get();

        // Estado de conexion (Online si hubo reporte en el ultimo minuto)
        $isOnline = false;
        if ($ultimaLectura) {
            $isOnline = now()->diffInMinutes($ultimaLectura->fecha_hora) < 1;
        }

        // Proceso activo actual
        $procesoActivo = ProcesoDeshidratacion::with('fruta')
            ->where('estado_proceso', 'activo')
            ->first();

        // ==========================================
        // NUEVAS VARIABLES AGREGADAS (MEJORAS)
        // ==========================================

        // 1. Estado del ventilador (LEER DIRECTAMENTE DE LA BD)
        $estadoVentilador = 'DESACTIVADO';
        $razonVentilador = 'Sistema en reposo';

        if ($ultimaLectura) {
            // Leer el estado real que envía el ESP32
            if ($ultimaLectura->estado_ventilador == 1) {
                $estadoVentilador = 'ACTIVADO';
        
                // Determinar la razón basada en las lecturas actuales
                if ($ultimaLectura->temperatura > 24.0) {
                    $razonVentilador = 'Temperatura > 24°C (actual: ' . number_format($ultimaLectura->temperatura, 1) . '°C)';
                } elseif ($ultimaLectura->humedad < 20.0) {
                    $razonVentilador = 'Humedad < 20% (actual: ' . number_format($ultimaLectura->humedad, 1) . '%)';
                } else {
                    $razonVentilador = 'Control automático activado';
                }
            } else {
                $razonVentilador = 'Temperatura y humedad dentro del rango normal';
            }
        }

        // 2. Últimas 5 alertas
        $ultimasAlertas = Alerta::with(['proceso', 'lectura'])
            ->orderBy('fecha_alerta', 'desc')
            ->take(5)
            ->get();

        // 3. Detalles del proceso activo (si existe)
        $procesoActivoDetalles = null;
        if ($procesoActivo) {
            $procesoActivoDetalles = [
                'numero_lote' => $procesoActivo->numero_lote,
                'fruta' => $procesoActivo->fruta->nombre_fruta ?? 'N/A',
                'fecha_inicio' => $procesoActivo->fecha_inicio,
                'tiempo_transcurrido_horas' => now()->diffInHours($procesoActivo->fecha_inicio),
                'peso_inicial_kg' => number_format($procesoActivo->peso_inicial_gramos / 1000, 2),
                'peso_actual_kg' => $procesoActivo->peso_final_gramos ? 
                    number_format($procesoActivo->peso_final_gramos / 1000, 2) : 'En proceso',
                'porcentaje_secado' => $procesoActivo->peso_inicial_gramos > 0 && $procesoActivo->peso_final_gramos ? 
                    number_format((($procesoActivo->peso_inicial_gramos - $procesoActivo->peso_final_gramos) / $procesoActivo->peso_inicial_gramos) * 100, 1) : 'Calculando...',
            ];
        }

        return view('dashboard', compact(
            'totalProcesos',
            'pesoInicialTotalKg',
            'pesoFinalTotalKg',
            'porcentajeSecadoPromedio',
            'tiempoTotalHoras',
            'tempPromedio',
            'humPromedio',
            'totalAlertas',
            'frutaRecuperadaKg',
            'ultimaLectura',
            'ultimasLecturas',
            'isOnline',
            'procesoActivo',
            // NUEVAS VARIABLES
            'estadoVentilador',
            'razonVentilador',
            'ultimasAlertas',
            'procesoActivoDetalles'
        ));
    }
}