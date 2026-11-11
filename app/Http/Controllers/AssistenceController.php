<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Librerias\HorarioAsistencia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Models\Assistance;
use App\Models\Workertype;

class AssistenceController extends Controller{
    protected $folderview      = 'app.assistence';
    protected $tituloAdmin     = 'Asistencias';
    protected $tituloRegistrar = 'Registrar asistencia';
    protected $tituloModificar = 'Modificar asistencia';
    protected $tituloEliminar  = 'Eliminar asistencia';
    protected $rutas           = array('create' => 'assistence.create',
        'edit'   => 'assistence.edit',
        'delete' => 'assistence.eliminar',
        'search' => 'assistence.search',
        'index'  => 'assistence.index',
        'destroy' => 'assistence.destroy',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $entidad          = 'Assistance';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;

        // Resumen del día de hoy
        $today = date('Y-m-d');
        $resumenDia = $this->getResumenDia($today);

        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'resumenDia', 'today'));
    }

    /**
     * Calcula el resumen del día: puntual, tardanza, ausentes.
     */
    private function getResumenDia($fecha)
    {
        $base = session('base');

        // Horario general de respaldo; cada marca usa el horario de su grado
        $horario    = HorarioAsistencia::horarioGeneral($base);
        $horaLimite = HorarioAsistencia::horaLimite($horario);
        $hayHorarios = DB::connection($base)->selectOne(
            "SELECT COUNT(id) as total FROM schedule WHERE deleted_at IS NULL"
        );

        $totalPersonas = DB::connection($base)->selectOne(
            "SELECT COUNT(id) as total FROM person WHERE id != 1 AND deleted_at IS NULL"
        );
        $total = $totalPersonas ? (int)$totalPersonas->total : 0;

        if (!$hayHorarios || (int)$hayHorarios->total === 0) {
            return [
                'total'     => $total,
                'puntual'   => 0,
                'tardanza'  => 0,
                'ausentes'  => $total,
                'horario'   => null,
            ];
        }

        // Se toma la primera marca del día de cada persona para definir su estado
        $limite = HorarioAsistencia::limiteSql($horaLimite);
        $conteo = DB::connection($base)->selectOne(
            "SELECT
                SUM(CASE WHEN x.limite IS NOT NULL AND x.hora <= x.limite THEN 1 ELSE 0 END) as puntuales,
                SUM(CASE WHEN x.limite IS NOT NULL AND x.hora >  x.limite THEN 1 ELSE 0 END) as tardanzas
             FROM (
                SELECT a.person_id, MIN(TIME(a.dateregister)) as hora, MIN({$limite}) as limite
                FROM assistance a" . HorarioAsistencia::joinSql('a') . "
                WHERE DATE(a.dateregister) = :fecha
                  AND a.deleted_at IS NULL
                  AND a.person_id != 1
                GROUP BY a.person_id
             ) x",
            ['fecha' => $fecha]
        );
        $puntuales = (object)['total' => $conteo ? (int)$conteo->puntuales : 0];
        $tardanzas = (object)['total' => $conteo ? (int)$conteo->tardanzas : 0];

        $totalPuntual  = $puntuales  ? (int)$puntuales->total  : 0;
        $totalTardanza = $tardanzas  ? (int)$tardanzas->total  : 0;
        $totalAusentes = $total - $totalPuntual - $totalTardanza;
        if ($totalAusentes < 0) $totalAusentes = 0;

        return [
            'total'      => $total,
            'puntual'    => $totalPuntual,
            'tardanza'   => $totalTardanza,
            'ausentes'   => $totalAusentes,
            'horario'    => $horario,
            'hora_limite' => $horaLimite,
        ];
    }

    public function search(Request $request){
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Assistance';
        $name      = Libreria::getParam($request->input('name'));
        $begindate = Libreria::getParam($request->input('begindate'));
        $enddate   = Libreria::getParam($request->input('enddate'));
        $filtro_tardanza = Libreria::getParam($request->input('filtro_tardanza')); // 'todos','puntual','tardanza'

        // Horario general de respaldo; cada marca usa el horario de su grado
        $horario    = HorarioAsistencia::horarioGeneral(session('base'));
        $horaLimite = HorarioAsistencia::horaLimite($horario);

        $from   = " FROM assistance wt
                LEFT JOIN person p ON wt.person_id = p.id"
                . HorarioAsistencia::joinSql('wt')
                . " WHERE wt.deleted_at IS NULL AND wt.person_id != 1";
        $params = [];

        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $from .= " AND (CONCAT_WS(' ', p.firstname, p.lastname) LIKE :filter1 OR CONCAT_WS(' ', p.lastname, p.firstname) LIKE :filter2)";
            $params['filter1'] = $filter;
            $params['filter2'] = $filter;
        }
        if ($begindate != "" && !is_null($begindate)) {
            $from .= " AND DATE(wt.dateregister) >= :begindate";
            $params['begindate'] = $begindate;
        }
        if ($enddate != "" && !is_null($enddate)) {
            $from .= " AND DATE(wt.dateregister) <= :enddate";
            $params['enddate'] = $enddate;
        }
        $from .= HorarioAsistencia::filtroSql('wt', $horaLimite, $filtro_tardanza);

        // ---- COUNT ----
        $resultado = DB::connection(session('base'))->selectOne("SELECT COUNT(wt.id) as cantidad" . $from . " LIMIT 0,1", $params);
        $cantidad  = ($resultado && $resultado->cantidad !== null) ? $resultado->cantidad : 0;

        $begincompagination = ($pagina - 1) * $filas;

        // ---- LIST ----
        $sql = "SELECT wt.id,
                       CONCAT_WS(' ', p.firstname, p.lastname) as nombre,
                       wt.grade,
                       wt.section,
                       wt.dateregister,
                       wt.tardanza,
                       " . HorarioAsistencia::estadoSql('wt', $horaLimite) . " as estado_asistencia"
             . $from
             . " ORDER BY wt.dateregister DESC"
             . " LIMIT :begincompagination, :endcompagination";
        $params['begincompagination'] = (int) $begincompagination;
        $params['endcompagination']   = (int) $filas;

        $lista    = DB::connection(session('base'))->select($sql, $params);
        $cabecera = [];
        $cabecera[] = ['valor' => '#',                  'numero' => '1'];
        $cabecera[] = ['valor' => 'Nombre',             'numero' => '1'];
        $cabecera[] = ['valor' => 'Grado',              'numero' => '1'];
        $cabecera[] = ['valor' => 'Fecha de registro',  'numero' => '1'];
        $cabecera[] = ['valor' => 'Hora',               'numero' => '1'];
        $cabecera[] = ['valor' => 'Estado',             'numero' => '1'];
        $cabecera[] = ['valor' => 'Operaciones',        'numero' => '1'];

        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;
        $horaLimiteDisplay = $horaLimite ? substr($horaLimite, 0, 5) : null;

        if ($cantidad > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion2($cantidad, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $request->replace(['page' => $paginaactual]);
            return view($this->folderview . '.list')->with(compact(
                'lista', 'paginacion', 'inicio', 'fin', 'entidad',
                'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'pagina', 'horaLimiteDisplay'
            ));
        }
        return view($this->folderview . '.list')->with(compact('lista', 'entidad'));
    }

    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Workertype';
        $workertype = null;
        $formData = array('workertype.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar';
        return view($this->folderview.'.mant')->with(compact('workertype', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('name' => 'required|max:60');
        $mensajes   = array();
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $workertype       = new Workertype();
            $workertype->name = strtoupper($request->input('name'));
            $workertype->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'workertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $workertype = DB::connection(session('base'))->selectOne('SELECT id,name FROM workertype WHERE id = :id', ['id' => $id]);
        $entidad  = 'Workertype';
        $formData = array('workertype.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('workertype', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'workertype');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('name' => 'required|max:60');
        $mensajes   = array();
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $workertype       = Workertype::find($id);
            $workertype->name = strtoupper($request->input('name'));
            $workertype->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'assistance');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $assistance = Assistance::find($id);
            $assistance->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'assistance');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Assistance::find($id);
        $personname = $modelo->person ? $modelo->person->firstname . ' ' . $modelo->person->lastname : 'N/A';
        $mensaje = '<p class="text-inverse fs-5">¿Esta seguro de eliminar la asistencia de <b class="text-danger">"'.$personname.'"</b>?</p>';
        $entidad  = 'Assistance';
        $formData = array('route' => array('assistence.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

}

?>
