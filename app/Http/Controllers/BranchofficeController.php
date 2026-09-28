<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Librerias\Libreria;
use App\Models\Branchoffice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class BranchofficeController extends Controller
{
    protected $folderview      = 'app.branchoffice';
    protected $tituloAdmin     = 'Listado de Sedes';
    protected $tituloRegistrar = 'Registrar Sede';
    protected $tituloModificar = 'Modificar Sede';
    protected $tituloEliminar  = 'Eliminar Sede';
    protected $rutas           = array(
        'create' => 'branchoffice.create',
        'edit'   => 'branchoffice.edit',
        'delete' => 'branchoffice.eliminar',
        'search' => 'branchoffice.search',
        'index'  => 'branchoffice.index',
    );

    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request)
    {
        $entidad = 'Branchoffice';
        $title   = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad','title','titulo_registrar','ruta'));
    }

    public function search(Request $request) {
        $pagina  = $request->input('page');
        $filas   = $request->input('filas');
        $name    = Libreria::getParam($request->input('name'), '');
        $entidad = 'Branchoffice';
        $params = array();
        $sql = "SELECT COUNT(bo.id) as cantidad FROM branchoffice as bo WHERE bo.deleted_at IS NULL";
        if ($name != "" && !is_null($name)) {
            $sql .= " AND bo.name like :name";
            $params['name'] = "%" . $name . "%";
        }
        $sql .= " LIMIT 1 OFFSET 0";
        $resultado = DB::connection(session('base'))->selectOne($sql, $params);
        $cantidad = ($resultado !== null && $resultado->cantidad !== null) ? $resultado->cantidad : 0;
        $begincompagination = ($pagina - 1) * $filas;

        $params = array();
        $sql = "SELECT bo.id, bo.name
                FROM branchoffice as bo
                WHERE bo.deleted_at IS NULL";
        if ($name != "" && !is_null($name)) {
            $sql .= " AND bo.name like :name";
            $params['name'] = "%" . $name . "%";
        }
        $sql .= " ORDER BY bo.`id` ASC LIMIT :begincompagination, :endcompagination ";
        $params["begincompagination"] = (int)$begincompagination;
        $params["endcompagination"] = (int)$filas;
        $lista = DB::connection(session('base'))->select($sql, $params);

        $cabecera = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'Sede', 'numero' => '1');
        $cabecera[] = array('valor' => 'Operaciones', 'numero' => '2');
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta = $this->rutas;
        if ($cantidad > 0) {
            $clsLibreria = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion2($cantidad, $pagina, $filas, $entidad);
            $paginacion   = $paramPaginacion['cadenapaginacion'];
            $inicio       = $paramPaginacion['inicio'];
            $fin          = $paramPaginacion['fin'];
            $paginaactual = $paramPaginacion['nuevapagina'];
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista','paginacion','inicio','fin','entidad','cabecera','titulo_modificar','titulo_eliminar','ruta','pagina'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function create(Request $request) {
        $listar  = Libreria::getParam($request->input('listar'), 'NO');
        $entidad = 'Branchoffice';
        $branchoffice = null;
        $formData = array('route' => array('branchoffice.store'), 'class' => 'form', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off', 'enctype' => 'multipart/form-data');
        $boton = 'Registrar';
        return view($this->folderview.'.mant')->with(compact('branchoffice','formData','entidad','boton','listar'));
    }

    public function store(Request $request) {
        $reglas  = array(
            'name' => 'required|max:100',
        );
        $mensaje = array(
            'name.required' => 'Debe ingresar un nombre',
            'name.max'      => 'El nombre debe tener como máximo 100 caracteres',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensaje);
        if ($validacion->fails()) { return $validacion->messages()->toJson(); }

        $error = DB::transaction(function() use($request){
            $m = new Branchoffice();
            $m->name = strtoupper($request->input('name'));
            $m->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit(Request $request,$id) {
        $existe = Libreria::verificarExistencia($id, 'branchoffice');
        if ($existe !== true) { return $existe; }
        $listar  = Libreria::getParam($request->input('listar'), 'NO');
        $branchoffice = Branchoffice::find($id);
        $entidad = 'Branchoffice';
        $formData = array('route' => array('branchoffice.update', $id), 'method' => 'PUT', 'class' => 'form', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off', 'enctype' => 'multipart/form-data');
        $boton = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('branchoffice','formData','entidad','boton','listar'));
    }

    public function update(Request $request,$id) {
        $existe = Libreria::verificarExistencia($id, 'branchoffice');
        if ($existe !== true) { return $existe; }
        $reglas  = array(
            'name' => 'required|max:100',
        );
        $mensaje = array(
            'name.required' => 'Debe ingresar un nombre',
            'name.max'      => 'El nombre debe tener como máximo 100 caracteres',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensaje);
        if ($validacion->fails()) { return $validacion->messages()->toJson(); }

        $error = DB::transaction(function() use($request, $id){
            $m = Branchoffice::find($id);
            $m->name = strtoupper($request->input('name'));
            $m->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id,$listarLuego) {
        $existe = Libreria::verificarExistencia($id, 'branchoffice');
        if ($existe !== true) { return $existe; }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) { $listar = $listarLuego; }
        $modelo = Branchoffice::find($id);
        $mensaje = '<p class="text-inverse fs-5">¿Está seguro de eliminar la sede <b class="text-danger">'.$modelo->name.'</b>?</p>';
        $entidad = 'Branchoffice';
        $formData = array('route' => array('branchoffice.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo','formData','entidad','boton','listar','mensaje'));
    }

    public function destroy($id) {
        $existe = Libreria::verificarExistencia($id, 'branchoffice');
        if ($existe !== true) { return $existe; }
        $error = DB::transaction(function() use($id){
            $m = Branchoffice::find($id);
            $m->delete();
        });
        return is_null($error) ? "OK" : $error;
    }
}
