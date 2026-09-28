<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Librerias\Libreria;
use App\Models\Menuoptioncategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class CategorymenuController extends Controller
{

    protected $folderview      = 'app.categorymenu';
    protected $tituloAdmin     = 'Categoría opción menú';
    protected $tituloRegistrar = 'Registrar categoría';
    protected $tituloModificar = 'Modificar categoría';
    protected $tituloEliminar  = 'Eliminar categoría';
    protected $rutas           = array(
        'create' => 'categorymenu.create',
        'edit'   => 'categorymenu.edit',
        'delete' => 'categorymenu.eliminar',
        'search' => 'categorymenu.search',
        'index'  => 'categorymenu.index',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $entidad = 'Categorymenu';
        $title   = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function search(Request $request) {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Categorymenu';
        $name             = Libreria::getParam($request->input('name'));
        $sql = "SELECT COUNT(mc.id) as cantidad FROM menuoptioncategory mc
        WHERE mc.deleted_at IS NULL";
        $params = array();
        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND mc.name LIKE :filter1";
            $params['filter1'] = $filter;
        }

        $sql .= " ORDER BY mc.id DESC";
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

        $sql = "SELECT mc.id, mc.name,mc.order,mc.position FROM menuoptioncategory mc
        WHERE mc.deleted_at IS NULL";

        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND mc.name LIKE :filter1";
            $params['filter1'] = $filter;
        }

        $sql .= " ORDER BY mc.id DESC";
        $sql .= " LIMIT :begincompagination, :endcompagination ";

        $params["begincompagination"] = (int)$begincompagination;
        $params["endcompagination"] = (int)$filas;

        $lista = DB::connection(session('base'))->select($sql, $params);
        $cabecera = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Orden', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Posición', 'numero' => '1');
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'pagina'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function create(Request $request) {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Categorymenu';
        $categorymenu = null;
        $cboPosition         = array('V'=>'Vertical','H' => 'Horizontal');
        $formData            = array('categorymenu.store');
        $formData            = array('route' => $formData, 'class' => 'form', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar';
        return view($this->folderview.'.mant')->with(compact('categorymenu', 'formData', 'entidad', 'boton', 'listar','cboPosition'));
    }

    public function store(Request $request) {

        $reglas     = array(
            'name' => 'required|max:50',
            'order' => 'required|numeric',
            'position' => 'required',
        );

        $mensajes = array(
            'name.required' => 'Debe ingresar un nombre',
            'name.max' => 'El nombre no puede exceder 50 caracteres',
            'order.required' => 'Debe ingresar un orden',
            'order.numeric' => 'El orden debe ser un número',
            'position.required' => 'Debe seleccionar una posición',
        );

        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $categoriaopcionmenu                        = new Menuoptioncategory();
            $categoriaopcionmenu->name                  = $request->input('name');
            $categoriaopcionmenu->order                 = $request->input('order');
            $categoriaopcionmenu->icon                  = $request->input('icon');
            $categoriaopcionmenu->position                  = $request->input('position');
            $categoriaopcionmenu->menuoptioncategory_id = Libreria::obtenerParametro($request->input('menuoptioncategory_id'));
            $categoriaopcionmenu->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit(Request $request,$id) {
        $existe = Libreria::verificarExistencia($id, 'menuoptioncategory');
        if ($existe !== true) {
            return $existe;
        }
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $categorymenu        = Menuoptioncategory::find($id);
        $entidad             = 'Categorymenu';
        $cboPosition         = array('V'=>'Vertical','H' => 'Horizontal');
        $formData            = array('categorymenu.update', $id);
        $formData            = array('route' => $formData, 'method' => 'PUT', 'class' => 'form', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Modificar';

        return view($this->folderview.'.mant')->with(compact('categorymenu', 'formData', 'entidad', 'boton', 'listar','cboPosition'));
    }

    public function update(Request $request,$id) {
        $existe = Libreria::verificarExistencia($id, 'menuoptioncategory');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
            'name' => 'required|max:50',
            'order' => 'required|numeric',
            'position' => 'required',
        );

        $mensajes = array(
            'name.required' => 'Debe ingresar un nombre',
            'name.max' => 'El nombre no puede exceder 50 caracteres',
            'order.required' => 'Debe ingresar un orden',
            'order.numeric' => 'El orden debe ser un número',
            'position.required' => 'Debe seleccionar una posición',
        );

        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        
        $error = DB::transaction(function() use($request, $id){
            $categoriaopcionmenu                        = Menuoptioncategory::find($id);
            $categoriaopcionmenu->name                  = $request->input('name');
            $categoriaopcionmenu->order                 = $request->input('order');
            $categoriaopcionmenu->icon                  = $request->input('icon');
            $categoriaopcionmenu->position                  = $request->input('position');
            $categoriaopcionmenu->menuoptioncategory_id = Libreria::obtenerParametro($request->input('menuoptioncategory_id'));
            $categoriaopcionmenu->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'menuoptioncategory');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $menuoptioncategory = Menuoptioncategory::find($id);
            $menuoptioncategory->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'menuoptioncategory');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Menuoptioncategory::find($id);
        $mensaje = '<p class="text-inverse">¿Esta seguro de eliminar este Menu de opción <b class="text-danger">"'.$modelo->name.'"</b>?</p>';
        $entidad  = 'Categorymenu';
        $formData = array('route' => array('categorymenu.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
