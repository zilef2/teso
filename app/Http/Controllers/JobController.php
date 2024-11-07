<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function index()
    {
        // Obtener trabajos en la tabla de trabajos
        $jobs = DB::table('jobs')->get();

        $maxAttempts = 2; // Cambia este valor según tu configuracion
        // Filtrar los trabajos finalizados y en proceso
        $completedJobs = $jobs->filter(function ($job) use ($maxAttempts) {
            return $job->attempts >= $maxAttempts; // Consideramos que si se han alcanzado los intentos máximos, está finalizado
        })->take(2);

        $inProcessJobs = $jobs->filter(function ($job) use($maxAttempts) {
            return $job->attempts < $maxAttempts; // Trabajos en proceso
        });

        return view('jobs.index', compact('completedJobs', 'inProcessJobs'));
    }
}
