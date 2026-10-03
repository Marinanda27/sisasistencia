<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Reporte de asistencias con detalle de puntualidad/tardanza
     */
    public function asistencias(Request $request)
    {
        $begindate = $request->input('begindate', date('Y-m-01'));
        $enddate   = $request->input('enddate',   date('Y-m-t'));
        $filtro    = $request->input('filtro', 'todos'); // todos, puntual, tardanza, ausente
        $name      = $request->input('name', '');

        $base = session('base');

        // Obtener horario activo
        $horario = DB::connection($base)->selectOne(
            "SELECT start_time, grace_minutes FROM schedule ORDER BY id ASC LIMIT 1"
        );
        $horaLimite = $horario
            ? date('H:i:s', strtotime($horario->start_time) + ($horario->grace_minutes * 60))
            : null;

        // ---- Resumen global del período ----
        $resumen = $this->getResumenPeriodo($base, $begindate, $enddate, $horaLimite);

        // ---- Datos por día (para gráfica) ----
        $datosPorDia = $this->getDatosPorDia($base, $begindate, $enddate, $horaLimite);

        // ---- Listado detallado ----
        $params = [];
        $sql = "SELECT
                    p.id as person_id,
                    CONCAT_WS(' ', p.firstname, p.lastname) as nombre,
                    DATE(a.dateregister) as fecha,
                    TIME(a.dateregister) as hora,
                    a.dateregister";

        if ($horaLimite) {
            $sql .= ", CASE WHEN TIME(a.dateregister) <= :hora_limite THEN 'Puntual' ELSE 'Tardanza' END as estado";
            $params['hora_limite'] = $horaLimite;
        } else {
            $sql .= ", 'Sin horario' as estado";
        }

        $sql .= " FROM assistance a
                  LEFT JOIN person p ON a.person_id = p.id
                  WHERE a.deleted_at IS NULL
                    AND a.person_id != 1
                    AND DATE(a.dateregister) BETWEEN :begindate AND :enddate";
        $params['begindate'] = $begindate;
        $params['enddate']   = $enddate;

        if (!empty($name)) {
            $sql .= " AND (CONCAT_WS(' ', p.firstname, p.lastname) LIKE :name OR CONCAT_WS(' ', p.lastname, p.firstname) LIKE :name2)";
            $params['name']  = '%' . $name . '%';
            $params['name2'] = '%' . $name . '%';
        }

        if ($filtro === 'puntual' && $horaLimite) {
            $sql .= " AND TIME(a.dateregister) <= :hora_f";
            $params['hora_f'] = $horaLimite;
        } elseif ($filtro === 'tardanza' && $horaLimite) {
            $sql .= " AND TIME(a.dateregister) > :hora_f";
            $params['hora_f'] = $horaLimite;
        }

        $sql .= " ORDER BY a.dateregister DESC";

        $lista = DB::connection($base)->select($sql, $params);

        return view('app.reporte.asistencias', compact(
            'lista', 'begindate', 'enddate', 'filtro', 'name',
            'horario', 'horaLimite', 'resumen', 'datosPorDia'
        ));
    }

    private function getResumenPeriodo($base, $begindate, $enddate, $horaLimite)
    {
        $totalPersonas = DB::connection($base)->selectOne(
            "SELECT COUNT(id) as total FROM person WHERE id != 1 AND deleted_at IS NULL"
        );
        $total = $totalPersonas ? (int)$totalPersonas->total : 0;

        // Días hábiles en el rango
        $diasRango = DB::connection($base)->selectOne(
            "SELECT COUNT(DISTINCT DATE(a.dateregister)) as dias
             FROM assistance a
             WHERE a.deleted_at IS NULL AND a.person_id != 1
               AND DATE(a.dateregister) BETWEEN :b AND :e",
            ['b' => $begindate, 'e' => $enddate]
        );
        $diasConRegistro = $diasRango ? (int)$diasRango->dias : 0;

        if ($horaLimite) {
            $puntuales = DB::connection($base)->selectOne(
                "SELECT COUNT(a.id) as total FROM assistance a
                 WHERE a.deleted_at IS NULL AND a.person_id != 1
                   AND DATE(a.dateregister) BETWEEN :b AND :e
                   AND TIME(a.dateregister) <= :hl",
                ['b' => $begindate, 'e' => $enddate, 'hl' => $horaLimite]
            );
            $tardanzas = DB::connection($base)->selectOne(
                "SELECT COUNT(a.id) as total FROM assistance a
                 WHERE a.deleted_at IS NULL AND a.person_id != 1
                   AND DATE(a.dateregister) BETWEEN :b AND :e
                   AND TIME(a.dateregister) > :hl",
                ['b' => $begindate, 'e' => $enddate, 'hl' => $horaLimite]
            );
        } else {
            $puntuales = (object)['total' => 0];
            $tardanzas = (object)['total' => 0];
        }

        $totalRegistros = ($puntuales ? (int)$puntuales->total : 0) + ($tardanzas ? (int)$tardanzas->total : 0);

        return [
            'total_personas'   => $total,
            'dias_con_registro'=> $diasConRegistro,
            'total_registros'  => $totalRegistros,
            'puntuales'        => $puntuales  ? (int)$puntuales->total  : 0,
            'tardanzas'        => $tardanzas  ? (int)$tardanzas->total  : 0,
        ];
    }

    private function getDatosPorDia($base, $begindate, $enddate, $horaLimite)
    {
        if (!$horaLimite) return [];

        $result = DB::connection($base)->select(
            "SELECT
                DATE(a.dateregister) as fecha,
                SUM(CASE WHEN TIME(a.dateregister) <= :hl THEN 1 ELSE 0 END) as puntuales,
                SUM(CASE WHEN TIME(a.dateregister) > :hl2 THEN 1 ELSE 0 END) as tardanzas
             FROM assistance a
             WHERE a.deleted_at IS NULL AND a.person_id != 1
               AND DATE(a.dateregister) BETWEEN :b AND :e
             GROUP BY DATE(a.dateregister)
             ORDER BY DATE(a.dateregister) ASC",
            ['hl' => $horaLimite, 'hl2' => $horaLimite, 'b' => $begindate, 'e' => $enddate]
        );

        return $result;
    }
}
?>
