<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    protected $folderview      = 'app.schedule';
    protected $tituloAdmin     = 'Horario de Entrada';
    protected $tituloRegistrar = 'Registrar Horario';
    protected $tituloModificar = 'Modificar Horario';
    protected $tituloEliminar  = 'Eliminar Horario';
    protected $rutas           = [
        'create'  => 'schedule.create',
        'edit'    => 'schedule.edit',
        'delete'  => 'schedule.eliminar',
        'search'  => 'schedule.search',
        'index'   => 'schedule.index',
        'destroy' => 'schedule.destroy',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $entidad          = 'Schedule';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview . '.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function search(Request $request)
    {
        $pagina  = $request->input('page');
        $filas   = $request->input('filas');
        $entidad = 'Schedule';

        $sql    = "SELECT COUNT(s.id) as cantidad FROM schedule s WHERE 1=1";
        $params = [];

        $sql .= " LIMIT 0,1";
        $resultado = DB::connection(session('base'))->selectOne($sql, $params);
        $cantidad  = ($resultado && $resultado->cantidad) ? $resultado->cantidad : 0;

        $begincompagination = ($pagina - 1) * $filas;

        $params = [];
        $sql    = "SELECT s.id, s.start_time, s.grace_minutes, s.created_at FROM schedule s WHERE 1=1";
        $sql   .= " ORDER BY s.id DESC";
        $sql   .= " LIMIT :begincompagination, :endcompagination";
        $params['begincompagination'] = (int) $begincompagination;
        $params['endcompagination']   = (int) $filas;

        $lista    = DB::connection(session('base'))->select($sql, $params);
        $cabecera = [];
        $cabecera[] = ['valor' => '#',               'numero' => '1'];
        $cabecera[] = ['valor' => 'Hora de entrada', 'numero' => '1'];
        $cabecera[] = ['valor' => 'Min. de tolerancia', 'numero' => '1'];
        $cabecera[] = ['valor' => 'Registrado',      'numero' => '1'];
        $cabecera[] = ['valor' => 'Operaciones',     'numero' => '2'];

        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;

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
                'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'pagina'
            ));
        }
        return view($this->folderview . '.list')->with(compact('lista', 'entidad'));
    }

    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Schedule';
        $schedule = null;
        $formData = ['route' => ['schedule.store'], 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off'];
        $boton    = 'Registrar';
        return view($this->folderview . '.mant')->with(compact('schedule', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $reglas   = [
            'start_time'    => 'required',
            'grace_minutes' => 'required|integer|min:0',
        ];
        $mensajes   = [
            'start_time.required'    => 'La hora de entrada es obligatoria.',
            'grace_minutes.required' => 'Los minutos de tolerancia son obligatorios.',
        ];
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function () use ($request) {
            $schedule                = new Schedule();
            $schedule->start_time    = $request->input('start_time');
            $schedule->grace_minutes = (int) $request->input('grace_minutes');
            $schedule->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'schedule');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $schedule = DB::connection(session('base'))->selectOne(
            'SELECT id, start_time, grace_minutes FROM schedule WHERE id = :id',
            ['id' => $id]
        );
        $entidad  = 'Schedule';
        $formData = ['route' => ['schedule.update', $id], 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off'];
        $boton    = 'Modificar';
        return view($this->folderview . '.mant')->with(compact('schedule', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'schedule');
        if ($existe !== true) {
            return $existe;
        }
        $reglas   = [
            'start_time'    => 'required',
            'grace_minutes' => 'required|integer|min:0',
        ];
        $mensajes   = [
            'start_time.required'    => 'La hora de entrada es obligatoria.',
            'grace_minutes.required' => 'Los minutos de tolerancia son obligatorios.',
        ];
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function () use ($request, $id) {
            $schedule                = Schedule::find($id);
            $schedule->start_time    = $request->input('start_time');
            $schedule->grace_minutes = (int) $request->input('grace_minutes');
            $schedule->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'schedule');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function () use ($id) {
            $schedule = Schedule::find($id);
            $schedule->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'schedule');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo  = Schedule::find($id);
        $mensaje = '<p class="text-inverse fs-5">¿Está seguro de eliminar el horario de las <b class="text-danger">' . $modelo->start_time . '</b>?</p>';
        $entidad = 'Schedule';
        $formData = ['route' => ['schedule.destroy', $id], 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off'];
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'mensaje'));
    }
}
?>
