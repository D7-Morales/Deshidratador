<?php

namespace App\Http\Controllers;

use App\Models\CargaFruta;
use App\Models\Fruta;
use App\Models\Deshidratador;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProcesoController extends Controller
{
    /**
     * Display a listing of the processes.
     */
    public function index()
    {
        $procesos = CargaFruta::with(['fruta', 'deshidratador'])
            ->orderBy('id_carga', 'desc')
            ->paginate(15);
        return view('procesos.index', compact('procesos'));
    }

    /**
     * Show the form for starting a new process.
     */
    public function create()
    {
        $frutas = Fruta::orderBy('nombre_fruta', 'asc')->get();
        $deshidratadores = Deshidratador::where('estado', 'activo')->orderBy('nombre', 'asc')->get();
        return view('procesos.create', compact('frutas', 'deshidratadores'));
    }

    /**
     * Store the new process.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_fruta' => 'required|exists:frutas,id_fruta',
            'id_deshidratador' => 'required|exists:deshidratadores,id_deshidratador',
            'bandeja' => 'required|integer|min:1',
            'cantidad_frutas' => 'required|integer|min:1',
            'peso_inicial' => 'required|numeric|between:0.01,999.99',
            'fecha_inicio' => 'required|date',
            'observaciones' => 'nullable|string',
        ], [
            'id_fruta.required' => 'Debe seleccionar una fruta.',
            'id_fruta.exists' => 'La fruta seleccionada no es válida.',
            'id_deshidratador.required' => 'Debe seleccionar un deshidratador.',
            'id_deshidratador.exists' => 'El deshidratador seleccionado no es válido.',
            'bandeja.required' => 'Debe ingresar el número de bandeja.',
            'cantidad_frutas.required' => 'Debe ingresar la cantidad de frutas.',
            'peso_inicial.required' => 'El peso inicial es obligatorio.',
            'peso_inicial.numeric' => 'El peso inicial debe ser un número decimal.',
            'fecha_inicio.required' => 'La fecha y hora de inicio es requerida.',
        ]);

        // Auto-generate batch code
        $numeroLote = 'LOTE-' . date('Ymd') . '-' . mt_rand(100, 999);

        // Convert weight to grams
        $pesoInicialGramos = $request->peso_inicial * 1000;

        CargaFruta::create([
            'numero_lote' => $numeroLote,
            'id_fruta' => $request->id_fruta,
            'id_usuario' => session('id_usuario'),
            'id_deshidratador' => $request->id_deshidratador,
            'cantidad_frutas' => $request->cantidad_frutas,
            'bandeja' => $request->bandeja,
            'fecha_inicio' => $request->fecha_inicio,
            'peso_inicial_gramos' => $pesoInicialGramos,
            'estado_proceso' => 'activo',
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('procesos.index')->with('success', 'Carga de fruta iniciada correctamente. Lote: ' . $numeroLote);
    }

    /**
     * Show the form to finalize the process.
     */
    public function edit($id)
    {
        $proceso = CargaFruta::with(['fruta', 'deshidratador'])->findOrFail($id);
        
        if (strtolower($proceso->estado_proceso) === 'completado') {
            return redirect()->route('procesos.index')->withErrors(['error' => 'Este proceso ya ha sido finalizado.']);
        }

        return view('procesos.edit', compact('proceso'));
    }

    /**
     * Complete/finalize the process.
     */
    public function update(Request $request, $id)
    {
        $proceso = CargaFruta::findOrFail($id);

        if (strtolower($proceso->estado_proceso) === 'completado') {
            return redirect()->route('procesos.index')->withErrors(['error' => 'Este proceso ya ha sido finalizado.']);
        }

        $pesoInicialKg = $proceso->peso_inicial_gramos / 1000;

        $request->validate([
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'peso_final' => 'required|numeric|between:0.01,999.99|lte:' . $pesoInicialKg,
            'observaciones' => 'nullable|string',
        ], [
            'fecha_fin.required' => 'La fecha y hora de finalización es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio (' . $proceso->fecha_inicio->format('Y-m-d H:i') . ').',
            'peso_final.required' => 'El peso final es obligatorio.',
            'peso_final.lte' => 'El peso final no puede ser mayor que el peso inicial (' . $pesoInicialKg . ' kg).',
        ]);

        // Convert weight to grams
        $pesoFinalGramos = $request->peso_final * 1000;

        // Calculate duration in hours
        $start = Carbon::parse($proceso->fecha_inicio);
        $end = Carbon::parse($request->fecha_fin);
        $duracionHoras = $start->diffInMinutes($end) / 60;

        $proceso->update([
            'fecha_fin' => $request->fecha_fin,
            'peso_final_gramos' => $pesoFinalGramos,
            'duracion_horas' => $duracionHoras,
            'observaciones' => $request->observaciones,
            'estado_proceso' => 'completado',
        ]);

        return redirect()->route('procesos.index')->with('success', 'Carga de fruta completada y registrada con éxito.');
    }

    /**
     * Delete a process.
     */
    public function destroy($id)
    {
        $proceso = CargaFruta::findOrFail($id);
        $proceso->delete();

        return redirect()->route('procesos.index')->with('success', 'Registro de proceso eliminado correctamente.');
    }
}
