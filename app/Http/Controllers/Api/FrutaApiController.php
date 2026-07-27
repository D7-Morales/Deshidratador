<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FrutaApiController extends Controller
{
    /**
     * Listar todas las frutas
     */
    public function index()
    {
        $frutas = Fruta::orderBy('nombre_fruta', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $frutas,
            'total' => $frutas->count()
        ]);
    }

    /**
     * Mostrar una fruta específica
     */
    public function show($id)
    {
        $fruta = Fruta::find($id);

        if (!$fruta) {
            return response()->json([
                'success' => false,
                'message' => 'Fruta no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $fruta
        ]);
    }

    /**
     * Crear nueva fruta
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_fruta' => 'required|string|max:50|unique:frutas,nombre_fruta',
            'temperatura_recomendada' => 'required|numeric|min:0|max:100',
            'humedad_recomendada' => 'required|numeric|min:0|max:100',
            'porcentaje_humedad_final' => 'nullable|numeric|min:0|max:100',
            'tiempo_estimado_horas' => 'nullable|integer|min:1',
            'observaciones' => 'nullable|string|max:300'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $fruta = Fruta::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Fruta creada exitosamente',
            'data' => $fruta
        ], 201);
    }

    /**
     * Actualizar fruta
     */
    public function update(Request $request, $id)
    {
        $fruta = Fruta::find($id);

        if (!$fruta) {
            return response()->json([
                'success' => false,
                'message' => 'Fruta no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_fruta' => 'required|string|max:50|unique:frutas,nombre_fruta,' . $id . ',id_fruta',
            'temperatura_recomendada' => 'required|numeric|min:0|max:100',
            'humedad_recomendada' => 'required|numeric|min:0|max:100',
            'porcentaje_humedad_final' => 'nullable|numeric|min:0|max:100',
            'tiempo_estimado_horas' => 'nullable|integer|min:1',
            'observaciones' => 'nullable|string|max:300'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $fruta->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Fruta actualizada exitosamente',
            'data' => $fruta
        ]);
    }

    /**
     * Eliminar fruta
     */
    public function destroy($id)
    {
        $fruta = Fruta::find($id);

        if (!$fruta) {
            return response()->json([
                'success' => false,
                'message' => 'Fruta no encontrada'
            ], 404);
        }

        $fruta->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fruta eliminada exitosamente'
        ]);
    }
}