<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
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
        $begindate = date('Y-m-01');
        $enddate = date('Y-m-t');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function search(Request $request){
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Assistance';
        $name = Libreria::getParam($request->input('name'));
        $begindate = Libreria::getParam($request->input('begindate'));
        $enddate = Libreria::getParam($request->input('enddate'));
        $sql = "SELECT COUNT(wt.id) as cantidad FROM assistance wt
                LEFT JOIN person p ON wt.person_id = p.id
                WHERE wt.deleted_at IS NULL";
        $params = array();
        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND CONCAT_WS(' ' ,p.firstname,p.lastname) LIKE :filter1 OR CONCAT_WS(' ' ,p.lastname,p.firstname) LIKE :filter2";
            $params['filter1'] = $filter;
            $params['filter2'] = $filter;
        }

        $sql .= " ORDER BY wt.id DESC";
        $sql .= " LIMIT 0,1";

        $resultado = DB::connection(session('base'))->selectOne($sql, $params);
        $cantidad = 0;
        if ($resultado !== null) {
            if ($resultado->cantidad !== null) {
                $cantidad = $resultado->cantidad;
            }
        }

        $begincompagination = ($pagina - 1) * $filas;
        $endcompagination = $begincompagination + $filas;

        $params = array();
        $sql = "SELECT wt.id, CONCAT_WS(' ' ,p.firstname,p.lastname) as nombre, wt.dateregister FROM assistance wt
                LEFT JOIN person p ON wt.person_id = p.id
                WHERE wt.deleted_at IS NULL";

        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND CONCAT_WS(' ' ,p.firstname,p.lastname) LIKE :filter1 OR CONCAT_WS(' ' ,p.lastname,p.firstname) LIKE :filter2";
            $params['filter1'] = $filter;
            $params['filter2'] = $filter;
        }

        $sql .= " ORDER BY wt.id DESC";
        $sql .= " LIMIT :begincompagination, :endcompagination ";

        $params["begincompagination"] = (int)$begincompagination;
        $params["endcompagination"] = (int)$filas;

        $lista = DB::connection(session('base'))->select($sql, $params);
        $cabecera = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de registro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');
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
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview . '.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'pagina'));
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
        $mensaje = '<p class="text-inverse fs-5">¿Esta seguro de eliminar la asistencia <b class="text-danger">"'.$personname.'"</b>?</p>';
        $entidad  = 'Assistance';
        $formData = array('route' => array('assistence.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

}

?>
