<?php

namespace App\Http\Controllers;

use App\Models\LecturaSensor;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    /**
     * Show the reading history list.
     */
    public function index(Request $request)
    {
        $query = LecturaSensor::with('sensor');

        // Filter by start date
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_desde);
        }

        // Filter by end date
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_hasta);
        }

        // Filter by general search term (e.g., matching values)
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('temperatura', 'like', "%{$buscar}%")
                  ->orWhere('humedad', 'like', "%{$buscar}%")
                  ->orWhere('presion', 'like', "%{$buscar}%");
            });
        }

        // Sorting by date (default desc)
        $orden = $request->get('orden', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('id_lectura', $orden);

        // Paginate results
        $lecturas = $query->paginate(20)->withQueryString();

        return view('historial.index', compact('lecturas', 'orden'));
    }
}
