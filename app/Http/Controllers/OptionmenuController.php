<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Librerias\Libreria;
use App\Models\Menuoption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class OptionmenuController extends Controller
{

    protected $folderview      = 'app.optionmenu';
    protected $tituloAdmin     = 'Opciones de menú';
    protected $tituloRegistrar = 'Registrar categoría';
    protected $tituloModificar = 'Modificar categoría';
    protected $tituloEliminar  = 'Eliminar categoría';
    protected $rutas           = array(
        'create' => 'optionmenu.create',
        'edit'   => 'optionmenu.edit',
        'delete' => 'optionmenu.eliminar',
        'search' => 'optionmenu.search',
        'index'  => 'optionmenu.index',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $entidad = 'Optionmenu';
        $title   = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboCategoryMenu = array('' => 'TODOS');
        $listCategory = DB::connection(session('base'))->select("SELECT id, name FROM menuoptioncategory WHERE deleted_at IS NULL");
        foreach ($listCategory as $key => $value) {
           $cboCategoryMenu = $cboCategoryMenu + array($value->id => $value->name);
        }
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboCategoryMenu'));
    }

    public function search(Request $request) {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Optionmenu';
        $name             = Libreria::getParam($request->input('name'));
        $categorymenu_id  = Libreria::getParam($request->input('categorymenu_id'));
        $sql = "SELECT COUNT(mo.id) as cantidad FROM menuoption mo
            LEFT JOIN menuoptioncategory mc ON mc.id = mo.menuoptioncategory_id
            WHERE mo.deleted_at IS NULL";
        $params = array();
        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND mo.name LIKE :filter1";
            $params['filter1'] = $filter;
        }

        if ($categorymenu_id != "" && !is_null($categorymenu_id)) {
            $sql .= " AND mo.menuoptioncategory_id = :categorymenu_id";
            $params['categorymenu_id'] = $categorymenu_id;
        }

        $sql .= " ORDER BY mo.id DESC";
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

        $sql = "SELECT mo.id,mo.name,mo.`order`, mc.name as categoria FROM menuoption mo
                LEFT JOIN menuoptioncategory mc ON mc.id = mo.menuoptioncategory_id
                WHERE mo.deleted_at IS NULL";

        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND mo.name LIKE :filter1";
            $params['filter1'] = $filter;
        }

        if ($categorymenu_id != "" && !is_null($categorymenu_id)) {
            $sql .= " AND mo.menuoptioncategory_id = :categorymenu_id";
            $params['categorymenu_id'] = $categorymenu_id;
        }
        
        $sql .= " ORDER BY mo.id DESC";
        $sql .= " LIMIT :begincompagination, :endcompagination ";

        $params["begincompagination"] = (int)$begincompagination;
        $params["endcompagination"] = (int)$filas;

        $lista = DB::connection(session('base'))->select($sql, $params);
        $cabecera = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Orden', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Categoria', 'numero' => '1');
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
        $listar       = Libreria::getParam($request->input('listar'), 'NO');
        $entidad      = 'Optionmenu';
        $cboCategoria = array('' => 'SELECCIONE');
        $listCategory = DB::connection(session('base'))->select("SELECT id, name FROM menuoptioncategory WHERE deleted_at IS NULL");
        foreach ($listCategory as $key => $value) {
           $cboCategoria = $cboCategoria + array($value->id => $value->name);
        }
        $optionmenu   = null;
        $formData     = array('optionmenu.store');
        $formData     = array('route' => $formData, 'class' => 'form', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton        = 'Registrar';
        return view($this->folderview.'.mant')->with(compact('optionmenu', 'formData', 'entidad', 'boton', 'cboCategoria', 'listar'));
    }

    public function store(Request $request) {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas = array(
            'name'                  => 'required|max:60',
            'order'                 => 'required|integer',
            'icon'                  => 'required',
            'link'                  => 'required',
           'menuoption_id'         => 'integer|exists:'.$request->session()->get('base').'.menuoption,id',
           'menuoptioncategory_id' => 'required|integer|exists:'.$request->session()->get('base').'.menuoptioncategory,id,deleted_at,NULL',
        );
        $mensajes = array(
            'name.required'         => 'Debe ingresar un nombre',
            'name.max'              => 'El nombre no puede ser mayor a 60 caracteres',
            'order.required'        => 'Debe ingresar un orden',
            'order.integer'         => 'El orden debe ser un número entero',
            'icon.required'         => 'Debe ingresar un icono',
            'link.required'         => 'Debe ingresar un link',
            'menuoption_id.integer' => 'El menu de opción seleccionado es inválido',
            'menuoption_id.exists'  => 'El menu de opción existe en la base de datos',
            'menuoptioncategory_id.required' => 'Debe seleccionar una categoría',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $opcionmenu                        = new Menuoption();
            $opcionmenu->name                  = $request->input('name');
            $opcionmenu->order                 = $request->input('order');
            $opcionmenu->icon                  = $request->input('icon');
            $opcionmenu->link                  = $request->input('link');
            $opcionmenu->menuoptioncategory_id = $request->input('menuoptioncategory_id');
            $opcionmenu->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit(Request $request,$id) {
        $existe = Libreria::verificarExistencia($id, 'menuoption');
        if ($existe !== true) {
            return $existe;
        }
        $listar       = Libreria::getParam($request->input('listar'), 'NO');
        $optionmenu   = Menuoption::find($id);
        $entidad      = 'Optionmenu';
        $cboCategoria = array('' => 'SELECCIONE');
        $listCategory = DB::connection(session('base'))->select("SELECT id, name FROM menuoptioncategory WHERE deleted_at IS NULL");
        foreach ($listCategory as $key => $value) {
           $cboCategoria = $cboCategoria + array($value->id => $value->name);
        }
        $formData     = array('optionmenu.update', $id);
        $formData     = array('route' => $formData, 'method' => 'PUT', 'class' => 'form', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton        = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('optionmenu', 'formData', 'entidad', 'boton', 'cboCategoria', 'listar'));
    }

    public function update(Request $request,$id) {
        $existe = Libreria::verificarExistencia($id, 'menuoption');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'name'                  => 'required|max:60',
            'order'                 => 'required|integer',
            'icon'                  => 'required',
            'link'                  => 'required',
           'menuoption_id'         => 'integer|exists:'.$request->session()->get('base').'.menuoption,id',
           'menuoptioncategory_id' => 'required|integer|exists:'.$request->session()->get('base').'.menuoptioncategory,id,deleted_at,NULL',
        );
        $mensajes = array(
            'name.required'         => 'Debe ingresar un nombre',
            'name.max'              => 'El nombre no puede ser mayor a 60 caracteres',
            'order.required'        => 'Debe ingresar un orden',
            'order.integer'         => 'El orden debe ser un número entero',
            'icon.required'         => 'Debe ingresar un icono',
            'link.required'         => 'Debe ingresar un link',
            'menuoption_id.integer' => 'El menu de opción seleccionado es inválido',
            'menuoption_id.exists'  => 'El menu de opción existe en la base de datos',
            'menuoptioncategory_id.required' => 'Debe seleccionar una categoría',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $opcionmenu                        = Menuoption::find($id);
            $opcionmenu->name                  = $request->input('name');
            $opcionmenu->order                 = $request->input('order');
            $opcionmenu->icon                  = $request->input('icon');
            $opcionmenu->link                  = $request->input('link');
            $opcionmenu->menuoptioncategory_id = $request->input('menuoptioncategory_id');
            $opcionmenu->save();
        });
        return is_null($error) ? "OK" : $error;
    }


    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'menuoption');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $menuoption = Menuoption::find($id);
            $menuoption->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'menuoption');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Menuoption::find($id);
        $mensaje = '<p class="text-inverse fs-5">¿Esta seguro de eliminar este Menu de opción <b class="text-danger">"'.$modelo->name.'"</b>?</p>';
        $entidad  = 'Optionmenu';
        $formData = array('route' => array('optionmenu.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
