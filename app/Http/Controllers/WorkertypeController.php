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
use App\Models\Person;
use App\Models\Workertype;

class WorkertypeController extends Controller{
    protected $folderview      = 'app.workertype';
    protected $tituloAdmin     = 'Tipo de trabajador';
    protected $tituloRegistrar = 'Registrar tipo de trabajador';
    protected $tituloModificar = 'Modificar tipo de trabajador';
    protected $tituloEliminar  = 'Eliminar tipo de trabajador';
    protected $rutas           = array('create' => 'workertype.create',
        'edit'   => 'workertype.edit',
        'delete' => 'workertype.eliminar',
        'search' => 'workertype.search',
        'index'  => 'workertype.index',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $entidad          = 'Workertype';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function search(Request $request){
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Employee';
        $workertype_id = Libreria::getParam($request->input('workertype_id'));
        $name = Libreria::getParam($request->input('name'));
        $dni = Libreria::getParam($request->input('dni'));
        $sql = "SELECT COUNT(wt.id) as cantidad FROM workertype wt
                WHERE wt.deleted_at IS NULL";
        $params = array();
        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND wt.name LIKE :filter1";
            $params['filter1'] = $filter;
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
        $sql = "SELECT wt.id, wt.name FROM workertype wt
                WHERE wt.deleted_at IS NULL";

        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND wt.name LIKE :filter1";
            $params['filter1'] = $filter;
        }

        $sql .= " ORDER BY wt.id DESC";
        $sql .= " LIMIT :begincompagination, :endcompagination ";

        $params["begincompagination"] = (int)$begincompagination;
        $params["endcompagination"] = (int)$filas;

        $lista = DB::connection(session('base'))->select($sql, $params);
        $cabecera = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
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
        $existe = Libreria::verificarExistencia($id, 'workertype');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $workertype = Workertype::find($id);
            $workertype->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'workertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Workertype::find($id);
        $mensaje = '<p class="text-inverse fs-5">¿Esta seguro de eliminar el tipo de trabajo <b class="text-danger">"'.$modelo->name.'"</b>?</p>';
        $entidad  = 'Workertype';
        $formData = array('route' => array('workertype.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

}

?>
