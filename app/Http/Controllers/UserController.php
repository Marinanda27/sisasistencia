<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Librerias\Libreria;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    protected $folderview      = 'app.user';
    protected $tituloAdmin     = 'Usuarios';
    protected $tituloRegistrar = 'Registrar usuario';
    protected $tituloModificar = 'Modificar usuario';
    protected $tituloEliminar  = 'Eliminar usuario';
    protected $rutas           = array(
        'create' => 'user.create',
        'edit'   => 'user.edit',
        'delete' => 'user.eliminar',
        'search' => 'user.search',
        'index'  => 'user.index',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $entidad          = 'User';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;

        $user             = Auth::user();
        $cboUsertype = array('' => 'TODOS');
        $listUsertype = DB::connection(session('base'))->select('SELECT id,name FROM usertype WHERE deleted_at is null AND id <> 1');
        foreach ($listUsertype as $key => $value) {
            $cboUsertype = $cboUsertype + array($value->id => $value->name);
        }
        $cboBranchoffice  = array();
        $listBranchoffice = DB::connection(session('base'))->select('SELECT id,name FROM branchoffice WHERE deleted_at is null');
        if ($user->usertype_id < 3) {
            $cboBranchoffice = array('' => 'TODOS');
            foreach ($listBranchoffice as $key => $value) {
                $cboBranchoffice = $cboBranchoffice + array($value->id => $value->name);
            }
        }

        $branchoffice_id = $user->branchoffice_id;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboBranchoffice', 'user', 'cboUsertype'));
    }

    public function search(Request $request)
    {
        $pagina          = $request->input('page');
        $filas           = $request->input('filas');
        $entidad         = 'User';
        $name            = Libreria::getParam($request->input('name'));
        $login           = Libreria::getParam($request->input('login'));
        $branchoffice_id = Libreria::getParam($request->input('branchoffice_id'));
        $usertype_id     = Libreria::getParam($request->input('usertype_id'));

        $params = array();
        $sql = "SELECT COUNT(us.id) as cantidad FROM user us
                LEFT JOIN person p ON p.id = us.person_id
                LEFT JOIN branchoffice b ON b.id = us.branchoffice_id
                LEFT JOIN usertype ut ON ut.id = us.usertype_id
                WHERE us.deleted_at IS NULL AND b.deleted_at IS NULL
                AND p.deleted_at IS NULL";

        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND CONCAT_WS(' ' ,p.firstname,p.lastname) LIKE :filter1 OR CONCAT_WS(' ' ,p.lastname,p.firstname) LIKE :filter2 OR us.login LIKE :filter3";
            $params['filter1'] = $filter;
            $params['filter2'] = $filter;
            $params['filter3'] = $filter;
        }
        if ($login != "" && !is_null($login)) {
            $sql .= " AND us.login LIKE :login1";
            $params['login1'] = $login;
        }

        if ($branchoffice_id != "" && !is_null($branchoffice_id)) {
            $sql .= " AND us.branchoffice_id = :branchoffice_id";
            $params['branchoffice_id'] = $branchoffice_id;
        }

        if ($usertype_id != "" && !is_null($usertype_id)) {
            $sql .= " AND us.usertype_id = :usertype_id";
            $params['usertype_id'] = $usertype_id;
        }

        $sql .= " AND us.id <> 1";

        $sql .= " ORDER BY us.id DESC";
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
        $sql = "SELECT us.id,us.branchoffice_id,ut.name as usertypename,us.login,us.`password`,CONCAT_WS(' ' ,p.firstname,p.lastname)as personname,b.name as branchname  FROM user us
                LEFT JOIN person p ON p.id = us.person_id
                LEFT JOIN branchoffice b ON b.id = us.branchoffice_id
                LEFT JOIN usertype ut ON ut.id = us.usertype_id
                WHERE us.deleted_at IS NULL AND b.deleted_at IS NULL
                AND p.deleted_at IS NULL";

        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND CONCAT_WS(' ' ,p.firstname,p.lastname) LIKE :filter1 OR CONCAT_WS(' ' ,p.lastname,p.firstname) LIKE :filter2 OR us.login LIKE :filter3";
            $params['filter1'] = $filter;
            $params['filter2'] = $filter;
            $params['filter3'] = $filter;
        }
        if ($login != "" && !is_null($login)) {
            $sql .= " AND us.login LIKE :login1";
            $params['login1'] = $login;
        }

        if ($branchoffice_id != "" && !is_null($branchoffice_id)) {
            $sql .= " AND us.branchoffice_id = :branchoffice_id";
            $params['branchoffice_id'] = $branchoffice_id;
        }

        if ($usertype_id != "" && !is_null($usertype_id)) {
            $sql .= " AND us.usertype_id = :usertype_id";
            $params['usertype_id'] = $usertype_id;
        }

        $sql .= " AND us.id <> 1";

        $sql .= " ORDER BY us.id DESC";
        $sql .= " LIMIT :begincompagination, :endcompagination ";

        $params["begincompagination"] = (int)$begincompagination;
        $params["endcompagination"] = (int)$filas;

        $lista = DB::connection(session('base'))->select($sql, $params);
        $cabecera = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Login', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo de usuario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Personal', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Sede', 'numero' => '1');
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

    public function create(Request $request)
    {
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad = 'User';
        $user = null;
        $formData       = array('user.store');
        $formData       = array('route' => $formData, 'class' => 'form', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar';

        $person_id = null;
        $cboUsertype = array('' => 'SELECCIONE');
        $listUsertype = DB::connection(session('base'))->select('SELECT id,name FROM usertype WHERE deleted_at is null AND id <> 1');
        foreach ($listUsertype as $key => $value) {
            $cboUsertype = $cboUsertype + array($value->id => $value->name);
        }

        $cboBranchoffice  = array();
        $listBranchoffice = DB::connection(session('base'))->select('SELECT id,name FROM branchoffice WHERE deleted_at is null');
        foreach ($listBranchoffice as $key => $value) {
            $cboBranchoffice = array('' => 'SELECCIONE');
            $cboBranchoffice = $cboBranchoffice + array($value->id => $value->name);
        }


        return view($this->folderview . '.mant')->with(compact('user', 'formData', 'entidad', 'boton', 'listar', 'cboUsertype', 'cboBranchoffice','person_id'));
    }

    public function store(Request $request) {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas = array(
            'login'       => 'required|max:20|unique:'.$request->session()->get('base').'.user,login,NULL,id,deleted_at,NULL',
            'password'    => 'required|max:20',
            'branchoffice_id' => 'required|integer|exists:'.$request->session()->get('base').'.branchoffice,id,deleted_at,NULL',
            'usertype_id' => 'required|integer|exists:'.$request->session()->get('base').'.usertype,id,deleted_at,NULL',
            'person_id'   => 'required|integer|exists:'.$request->session()->get('base').'.person,id,deleted_at,NULL',
            );
        $mensaje = array(
            'login.required' => 'Debe ingresar un login',
            'login.unique' => 'El login ya se encuentra registrado',
            'password.required' => 'Debe ingresar una contraseña',
            'usertype_id.required' => 'Debe seleccionar un tipo de usuario',
            'usertype_id.integer' => 'El tipo de usuario es incorrecto',
            'usertype_id.exists' => 'El tipo de usuario es incorrecto',
            'person_id.required' => 'Debe seleccionar un personal',
            'person_id.integer' => 'El personal es incorrecto',
            'person_id.exists' => 'El personal es incorrecto',
            'branchoffice_id.required' => 'Debe seleccionar una sede',
            );
        $validacion = Validator::make($request->all(),$reglas,$mensaje);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $usuario               = new User();
            $usuario->login        = $request->input('login');
            $usuario->password     = Hash::make($request->input('password'));
            $usuario->usertype_id  = $request->input('usertype_id');
            $usuario->person_id    = $request->input('person_id');
            $usuario->branchoffice_id = $request->input('branchoffice_id');
            $usuario->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'user');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'User';
        $user = DB::connection(session('base'))->selectOne("SELECT us.id,us.person_id,us.branchoffice_id,us.usertype_id,us.login,us.`password`,CONCAT_WS(' ' ,p.firstname,p.lastname)as personname,b.name as branchname  FROM user us
                LEFT JOIN person p ON p.id = us.person_id
                LEFT JOIN branchoffice b ON b.id = us.branchoffice_id
                WHERE us.deleted_at IS NULL AND b.deleted_at IS NULL
                AND p.deleted_at IS NULL AND us.id = :id", ['id' => $id]);
        $person_id = $user->person_id;
        $formData       = array('user.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';

        $cboUsertype = array('' => 'SELECCIONE');
        $listUsertype = DB::connection(session('base'))->select('SELECT id,name FROM usertype WHERE deleted_at is null AND id <> 1');
        foreach ($listUsertype as $key => $value) {
            $cboUsertype = $cboUsertype + array($value->id => $value->name);
        }

        $cboBranchoffice  = array();
        $listBranchoffice = DB::connection(session('base'))->select('SELECT id,name FROM branchoffice WHERE deleted_at is null');
        foreach ($listBranchoffice as $key => $value) {
            $cboBranchoffice = array('' => 'SELECCIONE');
            $cboBranchoffice = $cboBranchoffice + array($value->id => $value->name);
        }

        return view($this->folderview . '.mant')->with(compact('user', 'formData', 'entidad', 'boton', 'listar', 'cboUsertype', 'cboBranchoffice','person_id'));
    }

    public function update(Request $request,$id) {
        $existe = Libreria::verificarExistencia($id, 'user');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array('login'       => 'required|max:20|unique:'.$request->session()->get('base').'.user,login,'.$id.',id,deleted_at,NULL',
            'usertype_id' => 'required|integer|exists:'.$request->session()->get('base').'.usertype,id,deleted_at,NULL'
            );
        $mensaje = array(
            'login.required' => 'Debe ingresar un login',
            'login.unique' => 'El login ya se encuentra registrado',
            'usertype_id.required' => 'Debe seleccionar un tipo de usuario',
            'usertype_id.integer' => 'El tipo de usuario es incorrecto',
            'usertype_id.exists' => 'El tipo de usuario es incorrecto',
            );

        $validacion = Validator::make($request->all(),$reglas,$mensaje);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $usuario                 = User::find($id);
            $usuario->login          = $request->input('login');
            if ($request->input('password') != null && $request->input('password') != '') {
                $usuario->password = Hash::make($request->input('password'));
            }
            $usuario->person_id    = $request->input('person_id');
            $usuario->usertype_id = $request->input('usertype_id');
            $usuario->branchoffice_id = $request->input('branchoffice_id');
            $usuario->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id,$listarLuego) {
        $existe = Libreria::verificarExistencia($id, 'user');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = User::find($id);
        $mensaje = '<p class="text-inverse fs-5">¿Está seguro de eliminar este usuario <b class="text-danger">'.$modelo->login.'</b>?</p>';
        $entidad  = 'User';
        $formData = array('route' => array('user.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';

        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function destroy($id) {
        $existe = Libreria::verificarExistencia($id, 'user');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $usuario = User::find($id);
            $usuario->delete();
        });
        return is_null($error) ? "OK" : $error;
    }



    public function editpassword(Request $request, $id){
        $existe = Libreria::verificarExistencia($id, 'user');
        if ($existe !== true) {
            return $existe;
        }
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $entidad = 'UserPassword';
        $user = User::find($id);
        $formData = array('user.updatepassword', $id);
        $formData = array('route' => $formData, 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');

        $boton = 'Guardar Cambios';
        $personname = NULL;
        if(!is_null($user->person)){
            $personname = $user->person->firstname.' '.$user->person->lastname;
        }
        
        $nrodocument = '-';
        if(!is_null($user->person)){
           if(!is_null($user->person->nrodocument)){
                $nrodocument = $user->person->nrodocument;
           }
        }
        
        $birhdate = '-';
        if(!is_null($user->person)){
              if(!is_null($user->person->birthdate)){
                $birhdate = date('d/m/Y', strtotime($user->person->birthdate));
              }
        }

        $cellnumber = '-';
        if(!is_null($user->person)){
            if(!is_null($user->person->cellnumber)){
                $cellnumber = $user->person->cellnumber;
            }
        }

        $address = '-';
        if(!is_null($user->person)){
            if(!is_null($user->person->address)){
                $address = $user->person->address;
            }
        }

        $title = 'Configurar cuenta';
        $username = $user->login;
        return view($this->folderview . '.mantPassword')->with(compact('user', 'formData', 'entidad', 'boton', 'listar','personname','title','nrodocument','birhdate','cellnumber','address','username'));
    }


    public function updatepassword(Request $request, $id){
        $existe = Libreria::verificarExistencia($id, 'user');
        if ($existe !== true) {
            return $existe;
        }
        $mensajes = array(
            'currentpassword.required'   => 'Debe ingresar contraseña actual',
            'newpassword.required'         => 'Debe ingresar nueva contraseña',
            'confirmpassword.required'         => 'Debe confirmar contraseña'
            );
        $reglas = array(
            'currentpassword' => 'required',
            'newpassword' => 'required',
            'confirmpassword' => 'required'
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $usuario = User::find($id);
        if (!$usuario) {
            return json_encode(array('error' => array('El usuario no existe')));
        }

        if (!Hash::check($request->input('currentpassword'), $usuario->password)) {
            $error = array(
                'currentpassword' => array(
                    'La contraseña actual ingresada no es correcta'
                )
            );
            return json_encode($error);
        }

        if ($request->input('newpassword') != $request->input('confirmpassword')) {
            $error = array(
                    'confirmpassword' => array(
                        'Las contraseñas no coinciden'
                        )
                    );
                return json_encode($error);
        }
        $error = DB::transaction(function () use ($request, $id) {
            $usuario = User::find($id);
            if ($request->input('confirmpassword') != null && $request->input('confirmpassword') != '') {
                $usuario->password = Hash::make($request->input('confirmpassword'));
            }
            $usuario->save();
        });
        return is_null($error) ? "OK" : $error;
    }
}
