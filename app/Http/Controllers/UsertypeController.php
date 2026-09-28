<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Librerias\Libreria;
use App\Models\Permission;
use App\Models\Usertype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class UsertypeController extends Controller
{

    protected $folderview      = 'app.usertype';
    protected $tituloAdmin     = 'Tipos de usuario';
    protected $tituloRegistrar = 'Registrar tipo de usuario';
    protected $tituloModificar = 'Modificar tipo de usuario';
    protected $tituloEliminar  = 'Eliminar tipo de usuario';
    protected $rutas           = array(
        'create' => 'usertype.create',
        'edit'   => 'usertype.edit',
        'delete' => 'usertype.eliminar',
        'search' => 'usertype.search',
        'index'  => 'usertype.index',
        'permisos' => 'usertype.obtenerpermisos',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $entidad = 'Usertype';
        $title   = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function search(Request $request) {

        $pagina          = $request->input('page');
        $filas           = $request->input('filas');
        $entidad         = 'User';
        $name            = Libreria::getParam($request->input('name'));
        $params = array();
        $sql   = "SELECT COUNT(ut.id) as cantidad FROM usertype as ut
                  WHERE ut.deleted_at IS NULL";
        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND ut.name like :name1";
            $params['name1'] = $filter;
        }
        $sql .= " ORDER BY ut.id ASC";
        $sql .= " LIMIT 1 OFFSET 0";

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
        $sql   = "SELECT ut.id, ut.name FROM usertype as ut
                  WHERE ut.deleted_at IS NULL";
        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND ut.name like :name1";
            $params['name1'] = $filter;
        }
        $sql .= " ORDER BY ut.id ASC";
        $sql .= " LIMIT :begincompagination, :endcompagination ";

        $params["begincompagination"] = (int)$begincompagination;
        $params["endcompagination"] = (int)$filas;
        $lista = DB::connection(session('base'))->select($sql, $params);
        $cabecera = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
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

    public function create(Request $request) {
        $listar       = Libreria::getParam($request->input('listar'), 'NO');
        $entidad      = 'Usertype';
        $usertype  = null;
        $formData     = array('usertype.store');
        $formData     = array('route' => $formData, 'class' => 'form', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton        = 'Registrar';
        return view($this->folderview.'.mant')->with(compact('usertype', 'formData', 'entidad', 'boton','listar'));
    }

    public function store(Request $request) {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
            'name' => 'required|max:40'
        );
        $mensaje = array(
            'name.required' => 'Debe ingresar un nombre',
            'name.max'      => 'El nombre debe tener como máximo 40 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensaje);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $error = DB::transaction(function() use($request){
            $tipousuario       = new Usertype();
            $tipousuario->name = strtoupper($request->input('name'));
            $tipousuario->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit(Request $request,$id) {
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar       = Libreria::getParam($request->input('listar'), 'NO');
        $usertype     = Usertype::find($id);
        $entidad      = 'Usertype';
        $formData     = array('usertype.update', $id);
        $formData     = array('route' => $formData, 'method' => 'PUT', 'class' => 'form', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton        = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('usertype', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request,$id) {
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
            'name' => 'required|max:40'
        );
        $mensaje = array(
            'name.required' => 'Debe ingresar un nombre',
            'name.max'      => 'El nombre debe tener como máximo 40 caracteres'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensaje);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $tipousuario       = Usertype::find($id);
            $tipousuario->name = strtoupper($request->input('name'));
            $tipousuario->save();

        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id,$listarLuego) {
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Usertype::find($id);
        $mensaje = '<p class="text-inverse fs-5">¿Está seguro de eliminar este tipo de usuario <b class="text-danger">'.$modelo->name.'</b>?</p>';
        $entidad  = 'User';
        $formData = array('route' => array('usertype.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';

        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id) {
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $usertype = Usertype::find($id);
            $usertype->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function obtenerpermisos($listarParam, $id){
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        $entidad = 'Permiso';
        if (isset($listarParam)) {
            $listar = $listarParam;
        }
        $tipousuario = Usertype::find($id);
        return view($this->folderview.'.permisos')->with(compact('tipousuario', 'listar', 'entidad'));
    }

    public function guardarpermisos(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar        = Libreria::getParam($request->input('listar'), 'NO');
        $estados       = $request->input('estado');
        $idopcionmenus = $request->input('idopcionmenu');
        $cantAux       = count($estados);
        $respuesta     = true;
        $error         = DB::transaction(function() use ($id, $idopcionmenus, $estados, $cantAux)
        {
            Permission::where('usertype_id', '=', $id)->delete();
            for ($i=0; $i < $cantAux; $i++) {
                $exito = true;
                if($estados[$i] === 'H'){
                    $permiso = new Permission();
                    $permiso->usertype_id = $id;
                    $permiso->menuoption_id = $idopcionmenus[$i];
                    $permiso->save();
                }
            }
        });
        return is_null($error) ? "OK" : $error;
    }


}
