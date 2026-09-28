<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Person;
use App\Models\Assistance;
use Carbon\Carbon;

class DashboardMainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $totalPersonas = Person::where('id', '!=', 1)->count();

        $asistenciasHoy = Assistance::whereDate('dateregister', Carbon::today())
                                    ->where('person_id', '!=', 1)
                                    ->distinct('person_id')
                                    ->count('person_id');

        $ausentesHoy = $totalPersonas - $asistenciasHoy;

        $registrosNuevosSemana = Person::where('created_at', '>=', Carbon::now()->startOfWeek())->count();

        $labels7Dias = [];
        $datos7Dias = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $labels7Dias[] = $fecha->format('d/m');
            
            $asistencias = Assistance::whereDate('dateregister', $fecha)
                                     ->where('person_id', '!=', 1)
                                     ->distinct('person_id')
                                     ->count('person_id');
            $datos7Dias[] = $asistencias;
        }

        return view('app.dashboardmain.content', compact(
            'totalPersonas',
            'asistenciasHoy',
            'ausentesHoy',
            'registrosNuevosSemana',
            'labels7Dias',
            'datos7Dias'
        ));
    }
}
