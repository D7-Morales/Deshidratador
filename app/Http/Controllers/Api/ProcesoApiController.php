<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CargaFruta;
use App\Models\LecturaSensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProcesoApiController extends Controller
{
    /**
     * Listar todos los procesos/cargas
     */
    public function index(Request $request)
    {
        $query = CargaFruta::with(['fruta', 'usuario', 'deshidratador']);

        // Filtrar por estado si se proporciona
        if ($request->has('estado')) {
            $query->where('estado_proceso', $request->estado);
        }

        // Filtrar por usuario si se proporciona
        if ($request->has('usuario_id')) {
            $query->where('id_usuario', $request->usuario_id);
        }

        $procesos = $query->orderBy('fecha_inicio', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $procesos,
            'total' => $procesos->count()
        ]);
    }

    /**
     * Mostrar un proceso específico
     */
    public function show($id)
    {
        $proceso = CargaFruta::with(['fruta', 'usuario', 'deshidratador'])->find($id);

        if (!$proceso) {
            return response()->json([
                'success' => false,
                'message' => 'Proceso no encontrado'
            ], 404);
        }

        // Obtener lecturas del sensor para este proceso
        $lecturas = LecturaSensor::where('id_carga', $id)
            ->orderBy('fecha_hora', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $proceso,
            'lecturas' => $lecturas
        ]);
    }

    /**
     * Crear nuevo proceso/carga
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_fruta' => 'required|exists:frutas,id_fruta',
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_deshidratador' => 'required|exists:deshidratadores,id_deshidratador',
            'peso_inicial_gramos' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'numero_lote' => 'nullable|string|max:50|unique:cargas_fruta,numero_lote',
            'cantidad_frutas' => 'nullable|integer|min:1',
            'bandeja' => 'nullable|integer|min:1',
            'observaciones' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $proceso = CargaFruta::create([
            'id_fruta' => $request->id_fruta,
            'id_usuario' => $request->id_usuario,
            'id_deshidratador' => $request->id_deshidratador,
            'peso_inicial_gramos' => $request->peso_inicial_gramos,
            'fecha_inicio' => $request->fecha_inicio,
            'numero_lote' => $request->numero_lote,
            'cantidad_frutas' => $request->cantidad_frutas ?? 1,
            'bandeja' => $request->bandeja ?? 1,
            'estado_proceso' => 'activo',
            'observaciones' => $request->observaciones
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proceso creado exitosamente',
            'data' => $proceso->load(['fruta', 'usuario', 'deshidratador'])
        ], 201);
    }

    /**
     * Actualizar proceso (finalizar, cancelar, etc.)
     */
    public function update(Request $request, $id)
    {
        $proceso = CargaFruta::find($id);

        if (!$proceso) {
            return response()->json([
                'success' => false,
                'message' => 'Proceso no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'estado_proceso' => 'nullable|in:pendiente,activo,completado,cancelado',
            'peso_final_gramos' => 'nullable|numeric|min:0',
            'fecha_fin' => 'nullable|date',
            'observaciones' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $proceso->update($request->all());

        // Calcular duración si se proporcionó fecha_fin
        if ($request->has('fecha_fin') && $proceso->fecha_inicio) {
            $inicio = \Carbon\Carbon::parse($proceso->fecha_inicio);
            $fin = \Carbon\Carbon::parse($request->fecha_fin);
            $proceso->duracion_horas = $inicio->diffInHours($fin);
            $proceso->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Proceso actualizado exitosamente',
            'data' => $proceso->load(['fruta', 'usuario', 'deshidratador'])
        ]);
    }

    /**
     * Eliminar proceso
     */
    public function destroy($id)
    {
        $proceso = CargaFruta::find($id);

        if (!$proceso) {
            return response()->json([
                'success' => false,
                'message' => 'Proceso no encontrado'
            ], 404);
        }

        $proceso->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proceso eliminado exitosamente'
        ]);
    }

    /**
     * Obtener proceso activo actual
     */
    public function procesoActivo()
    {
        $proceso = CargaFruta::with(['fruta', 'usuario', 'deshidratador'])
            ->where('estado_proceso', 'activo')
            ->orderBy('fecha_inicio', 'desc')
            ->first();

        if (!$proceso) {
            return response()->json([
                'success' => true,
                'message' => 'No hay procesos activos',
                'data' => null
            ]);
        }

        // Obtener última lectura del sensor
        $ultimaLectura = LecturaSensor::where('id_carga', $proceso->id_carga)
            ->orderBy('fecha_hora', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $proceso,
            'ultima_lectura' => $ultimaLectura
        ]);
    }
}