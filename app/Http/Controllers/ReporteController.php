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

        return view('app.reporte.adminasistencia', compact(
            'begindate', 'enddate', 'filtro', 'name',
            'horario', 'horaLimite', 'resumen', 'datosPorDia'
        ));
    }

    /**
     * POST: devuelve el listado parcial (para AJAX) con filtros y paginación
     */
    public function asistenciasPost(Request $request)
    {
        $pagina    = (int) $request->input('page', 1);
        $filas     = (int) $request->input('filas', 10);
        $name      = $request->input('name', '');
        $begindate = $request->input('begindate', date('Y-m-01'));
        $enddate   = $request->input('enddate',   date('Y-m-t'));
        $filtro    = $request->input('filtro', 'todos');

        $base = session('base');

        // Horario activo
        $horario = DB::connection($base)->selectOne(
            "SELECT start_time, grace_minutes FROM schedule ORDER BY id ASC LIMIT 1"
        );
        $horaLimite = $horario
            ? date('H:i:s', strtotime($horario->start_time) + ($horario->grace_minutes * 60))
            : null;

        // --- WHERE base ---
        $whereBase  = "FROM assistance wt LEFT JOIN person p ON wt.person_id = p.id
                        WHERE wt.deleted_at IS NULL AND wt.person_id != 1";
        $params = [];

        if (!empty($name)) {
            $whereBase .= " AND (CONCAT_WS(' ', p.firstname, p.lastname) LIKE :filter1
                              OR CONCAT_WS(' ', p.lastname, p.firstname) LIKE :filter2)";
            $params['filter1'] = '%' . $name . '%';
            $params['filter2'] = '%' . $name . '%';
        }
        if (!empty($begindate)) {
            $whereBase .= " AND DATE(wt.dateregister) >= :begindate";
            $params['begindate'] = $begindate;
        }
        if (!empty($enddate)) {
            $whereBase .= " AND DATE(wt.dateregister) <= :enddate";
            $params['enddate'] = $enddate;
        }
        if ($filtro === 'puntual' && $horaLimite) {
            $whereBase .= " AND TIME(wt.dateregister) <= :hora_limite_f";
            $params['hora_limite_f'] = $horaLimite;
        } elseif ($filtro === 'tardanza' && $horaLimite) {
            $whereBase .= " AND TIME(wt.dateregister) > :hora_limite_f";
            $params['hora_limite_f'] = $horaLimite;
        }

        // --- COUNT ---
        $countResult = DB::connection($base)->selectOne(
            "SELECT COUNT(wt.id) as cantidad " . $whereBase . " LIMIT 0,1",
            $params
        );
        $cantidad = $countResult ? (int)$countResult->cantidad : 0;

        // --- LIST ---
        $begincompagination = ($pagina - 1) * $filas;

        $selectCols = "SELECT wt.id,
                              CONCAT_WS(' ', p.firstname, p.lastname) as nombre,
                              wt.dateregister";
        if ($horaLimite) {
            $selectCols .= ", CASE WHEN TIME(wt.dateregister) <= :hora_limite THEN 'Puntual' ELSE 'Tardanza' END as estado_asistencia";
            $params['hora_limite'] = $horaLimite;
        } else {
            $selectCols .= ", 'Sin horario' as estado_asistencia";
        }

        $sqlList = $selectCols . " " . $whereBase
            . " ORDER BY wt.dateregister DESC"
            . " LIMIT :begincompagination, :endcompagination";
        $params['begincompagination'] = $begincompagination;
        $params['endcompagination']   = $filas;

        $lista = DB::connection($base)->select($sqlList, $params);

        $cabecera = [
            ['valor' => '#',                  'numero' => '1'],
            ['valor' => 'Nombre',             'numero' => '1'],
            ['valor' => 'Fecha de registro',  'numero' => '1'],
            ['valor' => 'Hora',               'numero' => '1'],
            ['valor' => 'Estado',             'numero' => '1'],
        ];

        $entidad          = 'Asistencia';
        $ruta             = ['search' => 'reporte.asistencias.post'];
        $horaLimiteDisplay = $horaLimite ? substr($horaLimite, 0, 5) : null;

        if ($cantidad > 0) {
            $clsLibreria     = new \App\Librerias\Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion2($cantidad, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $request->replace(['page' => $paginaactual]);
            return view('app.reporte.listasistencia')->with(compact(
                'lista', 'paginacion', 'inicio', 'fin', 'entidad',
                'cabecera', 'ruta', 'horaLimiteDisplay'
            ));
        }

        return view('app.reporte.listasistencia')->with(compact('lista', 'entidad', 'cabecera'));
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
