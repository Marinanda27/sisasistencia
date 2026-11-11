<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Librerias\HorarioAsistencia;
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

        // Horario general de respaldo (cada asistencia usa el horario de su grado)
        $horario    = HorarioAsistencia::horarioGeneral($base);
        $horaLimite = HorarioAsistencia::horaLimite($horario);

        // ---- Resumen global del período ----
        $resumen = $this->getResumenPeriodo($base, $begindate, $enddate, $horaLimite);

        // ---- Datos por día (para gráfica) ----
        $datosPorDia = $this->getDatosPorDia($base, $begindate, $enddate, $horaLimite);

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
        $grade     = $request->input('grade', '');
        $grade     = in_array((string) $grade, ['1', '2', '3', '4', '5'], true) ? (int) $grade : null;
        $section   = $request->input('section', '');
        $section   = in_array((string) $section, ['A', 'B'], true) ? $section : null;

        $base = session('base');

        // Horario general de respaldo; cada asistencia usa el horario de su grado
        $horario    = HorarioAsistencia::horarioGeneral($base);
        $horaLimite = HorarioAsistencia::horaLimite($horario);

        // --- WHERE base ---
        $whereBase  = "FROM assistance wt
                        LEFT JOIN person p ON wt.person_id = p.id"
                    . HorarioAsistencia::joinSql('wt')
                    . " WHERE wt.deleted_at IS NULL AND wt.person_id != 1";
        $params = [];

        if (!empty($name)) {
            $whereBase .= " AND (CONCAT_WS(' ', p.firstname, p.lastname) LIKE :filter1
                              OR CONCAT_WS(' ', p.lastname, p.firstname) LIKE :filter2)";
            $params['filter1'] = '%' . $name . '%';
            $params['filter2'] = '%' . $name . '%';
        }
        if (!is_null($grade)) {
            $whereBase .= " AND wt.grade = :grade";
            $params['grade'] = $grade;
        }
        if (!is_null($section)) {
            $whereBase .= " AND wt.section = :section";
            $params['section'] = $section;
        }
        if (!empty($begindate)) {
            $whereBase .= " AND DATE(wt.dateregister) >= :begindate";
            $params['begindate'] = $begindate;
        }
        if (!empty($enddate)) {
            $whereBase .= " AND DATE(wt.dateregister) <= :enddate";
            $params['enddate'] = $enddate;
        }
        $whereBase .= HorarioAsistencia::filtroSql('wt', $horaLimite, $filtro);

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
                              wt.grade,
                              wt.section,
                              wt.dateregister,
                              " . HorarioAsistencia::estadoSql('wt', $horaLimite) . " as estado_asistencia";

        $sqlList = $selectCols . " " . $whereBase
            . " ORDER BY wt.dateregister DESC"
            . " LIMIT :begincompagination, :endcompagination";
        $params['begincompagination'] = $begincompagination;
        $params['endcompagination']   = $filas;

        $lista = DB::connection($base)->select($sqlList, $params);

        $cabecera = [
            ['valor' => '#',                  'numero' => '1'],
            ['valor' => 'Nombre',             'numero' => '1'],
            ['valor' => 'Grado',              'numero' => '1'],
            ['valor' => 'Sección',            'numero' => '1'],
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

        $limite = HorarioAsistencia::limiteSql($horaLimite);
        $conteo = DB::connection($base)->selectOne(
            "SELECT
                SUM(CASE WHEN TIME(a.dateregister) <= {$limite} THEN 1 ELSE 0 END) as puntuales,
                SUM(CASE WHEN TIME(a.dateregister) >  {$limite} THEN 1 ELSE 0 END) as tardanzas
             FROM assistance a" . HorarioAsistencia::joinSql('a') . "
             WHERE a.deleted_at IS NULL AND a.person_id != 1
               AND DATE(a.dateregister) BETWEEN :b AND :e",
            ['b' => $begindate, 'e' => $enddate]
        );
        $puntuales = (object)['total' => $conteo ? (int)$conteo->puntuales : 0];
        $tardanzas = (object)['total' => $conteo ? (int)$conteo->tardanzas : 0];

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
        $limite = HorarioAsistencia::limiteSql($horaLimite);
        $result = DB::connection($base)->select(
            "SELECT
                DATE(a.dateregister) as fecha,
                SUM(CASE WHEN TIME(a.dateregister) <= {$limite} THEN 1 ELSE 0 END) as puntuales,
                SUM(CASE WHEN TIME(a.dateregister) >  {$limite} THEN 1 ELSE 0 END) as tardanzas
             FROM assistance a" . HorarioAsistencia::joinSql('a') . "
             WHERE a.deleted_at IS NULL AND a.person_id != 1
               AND DATE(a.dateregister) BETWEEN :b AND :e
             GROUP BY DATE(a.dateregister)
             ORDER BY DATE(a.dateregister) ASC",
            ['b' => $begindate, 'e' => $enddate]
        );

        return $result;
    }
}
?>
