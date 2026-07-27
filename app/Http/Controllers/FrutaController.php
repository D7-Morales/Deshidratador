<?php

namespace App\Http\Controllers;

use App\Models\Fruta;
use Illuminate\Http\Request;

class FrutaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $frutas = Fruta::orderBy('nombre_fruta', 'asc')->paginate(15);
        return view('frutas.index', compact('frutas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('frutas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_fruta' => 'required|string|max:50|unique:frutas,nombre_fruta',
            'temperatura_recomendada' => 'required|numeric|between:0,99.99',
            'humedad_recomendada' => 'required|numeric|between:0,99.99',
            'porcentaje_humedad_final' => 'nullable|numeric|between:0,99.99',
            'tiempo_estimado_horas' => 'nullable|integer|min:1',
            'observaciones' => 'nullable|string|max:300',
        ], [
            'nombre_fruta.required' => 'El nombre de la fruta es obligatorio.',
            'nombre_fruta.unique' => 'Este nombre de fruta ya está registrado.',
            'temperatura_recomendada.required' => 'La temperatura recomendada es obligatoria.',
            'humedad_recomendada.required' => 'La humedad recomendada es obligatoria.',
        ]);

        Fruta::create($request->all());

        return redirect()->route('frutas.index')->with('success', 'Fruta registrada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $fruta = Fruta::findOrFail($id);
        return view('frutas.edit', compact('fruta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $fruta = Fruta::findOrFail($id);

        $request->validate([
            'nombre_fruta' => 'required|string|max:50|unique:frutas,nombre_fruta,' . $id . ',id_fruta',
            'temperatura_recomendada' => 'required|numeric|between:0,99.99',
            'humedad_recomendada' => 'required|numeric|between:0,99.99',
            'porcentaje_humedad_final' => 'nullable|numeric|between:0,99.99',
            'tiempo_estimado_horas' => 'nullable|integer|min:1',
            'observaciones' => 'nullable|string|max:300',
        ], [
            'nombre_fruta.required' => 'El nombre de la fruta es obligatorio.',
            'nombre_fruta.unique' => 'Este nombre de fruta ya está registrado.',
            'temperatura_recomendada.required' => 'La temperatura recomendada es obligatoria.',
            'humedad_recomendada.required' => 'La humedad recomendada es obligatoria.',
        ]);

        $fruta->update($request->all());

        return redirect()->route('frutas.index')->with('success', 'Fruta actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $fruta = Fruta::findOrFail($id);
        
        // Check if fruit has active dehydration processes
        if ($fruta->cargas()->where('estado_proceso', 'activo')->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar la fruta porque está asociada a un proceso de deshidratación activo.']);
        }

        $fruta->delete();

        return redirect()->route('frutas.index')->with('success', 'Fruta eliminada correctamente.');
    }
}
