<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    /**
     * Marcar alerta como atendida
     */
    public function marcarAtendida($id)
    {
        try {
            $alerta = Alerta::find($id);
            
            if (!$alerta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alerta no encontrada'
                ], 404);
            }

            $alerta->atendida = 1;
            $alerta->save();

            return response()->json([
                'success' => true,
                'message' => 'Alerta marcada como atendida'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}