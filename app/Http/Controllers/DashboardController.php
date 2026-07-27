<?php

namespace App\Http\Controllers;

use App\Models\LecturaSensor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the main dashboard.
     */
    public function index()
    {
        // Get the single latest reading
        $ultimaLectura = LecturaSensor::orderBy('id_lectura', 'desc')->first();

        // Get the latest 10 readings for the table
        $ultimasLecturas = LecturaSensor::orderBy('id_lectura', 'desc')->take(10)->get();

        return view('dashboard', compact('ultimaLectura', 'ultimasLecturas'));
    }
}
