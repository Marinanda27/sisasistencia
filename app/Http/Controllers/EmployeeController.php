<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Models\Departamento;
use App\Models\Distrito;
use App\Models\Person;
use App\Models\Provincia;
use App\Models\Workertype;
use Illuminate\Support\Facades\Validator;
class EmployeeController extends Controller{
    protected $folderview      = 'app.employee';
    protected $tituloAdmin     = 'Trabajadores';
    protected $tituloRegistrar = 'Registrar trabajador';
    protected $tituloModificar = 'Modificar trabajador';
    protected $tituloEliminar  = 'Eliminar trabajador';
    protected $rutas           = array('create' => 'employee.create',
        'edit'   => 'employee.edit',
        'delete' => 'employee.eliminar',
        'search' => 'employee.search',
        'index'  => 'employee.index',
        'searchsimple' => 'employee.searchsimple',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $entidad          = 'Employee';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboWorkertype = array('' => 'TODOS');
        $listWorkertype = DB::connection(session('base'))->select("SELECT id, name FROM workertype WHERE deleted_at IS NULL ORDER BY id");

        foreach ($listWorkertype as $key => $value) {
            $cboWorkertype = $cboWorkertype + array($value->id => $value->name);
        }

        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','cboWorkertype'));
    }

    public function search(Request $request){
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Employee';
        $workertype_id = Libreria::getParam($request->input('workertype_id'));
        $name = Libreria::getParam($request->input('name'));
        $dni = Libreria::getParam($request->input('dni'));
        $sql = "SELECT COUNT(pe.id) as cantidad FROM person pe
                INNER JOIN workertype wt ON pe.workertype_id = wt.id
                WHERE pe.deleted_at IS NULL AND (pe.type ='E' OR pe.type2 ='E')";
        $params = array();
        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND CONCAT_WS(' ' ,pe.firstname,pe.lastname) LIKE :filter1 OR CONCAT_WS(' ' ,pe.lastname,pe.firstname) LIKE :filter2";
            $params['filter1'] = $filter;
            $params['filter2'] = $filter;
        }

        if($dni != "" && !is_null($dni)){
            $filter = '%' . $dni . '%';
            $sql .= " AND pe.dni = :dni";
            $params['dni'] = $dni;
        }

        if($workertype_id !="" && !is_null($workertype_id)){
            $sql .= " AND pe.workertype_id = :workertype_id";
            $params['workertype_id'] = $workertype_id;
        }

        $sql .= " ORDER BY pe.id DESC";
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
        $sql = "SELECT pe.id,CONCAT_WS(' ',pe.lastname,pe.firstname) as employeename, wt.name as job, pe.dni, pe.cellnumber,pe.address FROM person pe
                INNER JOIN workertype wt ON pe.workertype_id = wt.id
                WHERE pe.deleted_at IS NULL AND (pe.type ='E' OR pe.type2 = 'E')";

        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND CONCAT_WS(' ' ,pe.firstname,pe.lastname) LIKE :filter1 OR CONCAT_WS(' ' ,pe.lastname,pe.firstname) LIKE :filter2";
            $params['filter1'] = $filter;
            $params['filter2'] = $filter;
        }

        if($dni != "" && !is_null($dni)){
            $filter = '%' . $dni . '%';
            $sql .= " AND pe.dni = :dni";
            $params['dni'] = $dni;
        }

        if($workertype_id !="" && !is_null($workertype_id)){
            $sql .= " AND pe.workertype_id = :workertype_id";
            $params['workertype_id'] = $workertype_id;
        }

        $sql .= " ORDER BY pe.id DESC";
        $sql .= " LIMIT :begincompagination, :endcompagination ";

        $params["begincompagination"] = (int)$begincompagination;
        $params["endcompagination"] = (int)$filas;

        $lista = DB::connection(session('base'))->select($sql, $params);
        $cabecera = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Apellido y Nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Dirección', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro Celular', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ocupación', 'numero' => '1');
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

    public function create(Request $request){
        $listar          = Libreria::getParam($request->input('listar'), 'NO');
        $entidad         = 'Employee';
        $user = Auth::user();
        $cboWorkertype   = [''=>'SELECCIONE'] + Workertype::pluck('name', 'id')->all();
        $birthdate       = null;
        $employee        = null;
        $formData        = array('employee.store');
        $formData        = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton           = 'Registrar';
        $cboStatus = array('A'=>'Activo','I'=>'Inactivo');
        $type2 = null;
        return view($this->folderview.'.mant')->with(compact('employee', 'formData', 'entidad', 'boton', 'listar', 'birthdate', 'cboWorkertype','cboStatus','type2'));
    }

    public function store(Request $request){
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        if ($request->input('dni') !== null && $request->input('dni') !== '') {
            $person = Person::where('dni','=',$request->input('dni'))->where(function($query) {
                $query->where('type','=','E')->orWhere('type2','=','E');
            })->first();
            if ($person !== null) {
                $error = array(
                'dni' => array(
                    'N° de DNI pertenece a personal ya registrado'
                    ));
            return json_encode($error);
            }
        }

        $mensajes = array(
            'dni.required'               => 'Debe ingresar el DNI del personal',
            'firstname.required'         => 'Debe ingresar nombre del personal',
            'lastname.required'          => 'Debe ingresar apellidos del personal',
            'workertype_id.required'     => 'Debe seleccionar el tipo de trabajador'
            );
        $reglas = array(
                'lastname'        => 'required|max:100',
                'firstname'       => 'required|max:100',
                'dni'             => 'required|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/',
                'workertype_id'   => 'required|integer|exists:'.$request->session()->get('base').'.workertype,id,deleted_at,NULL'
                );

        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $error = DB::transaction(function() use($request){
            Date::setLocale('es');
            $employee                = new Person();
            $person = Person::where('dni','=',$request->input('dni'))->first();
            if ($person !== null) {
                $employee = Person::find($person->id);
            }
            $employee->firstname          = strtoupper(Libreria::getParam($request->input('firstname')));
            $employee->lastname           = strtoupper(Libreria::getParam($request->input('lastname')));
            $employee->workertype_id = $request->input('workertype_id');
            $employee->address       = Libreria::getParam(strtoupper($request->input('address')));
            $employee->dni           = $request->input('dni');
            if ($request->input('birthdate') !== null && $request->input('birthdate') !== '') {
                $employee->birthdate     = Date::createFromFormat('d/m/Y', $request->input('birthdate'))->format('Y-m-d');
            }
            $employee->email         = Libreria::getParam($request->input('email'));
            $employee->cellnumber    = Libreria::getParam($request->input('cellnumber'));
            $employee->observation   = Libreria::getParam($request->input('observation'));


            if ($employee->type === null) {
                $employee->type = 'E';
            }elseif ($employee->type2 === null) {
                $employee->type2 = 'E';
            }
            

            $employee->secondtype = 'P';
            $employee->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit(Request $request, $id){
        $existe = Libreria::verificarExistencia($id, 'person');
        if ($existe !== true) {
            return $existe;
        }
        $listar          = Libreria::getParam($request->input('listar'), 'NO');
        $employee        = Person::find($id);
        $type2 = $employee->type2;
        $entidad         = 'Employee';

        $cboWorkertype   = array('' => 'Seleccione') + Workertype::pluck('name', 'id')->all();

        $formData        = array('employee.update', $id);
        $formData        = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton           = 'Modificar';

        $cboStatus = array('A'=>'Activo','I'=>'Inactivo');
        $cboMachine = array('N'=>'NO','Y'=>'SI');

        return view($this->folderview.'.mant')->with(compact('employee', 'formData', 'entidad', 'boton','listar', 'cboWorkertype','cboStatus','cboMachine','type2'));
    }

    public function update(Request $request, $id){
        $existe = Libreria::verificarExistencia($id, 'person');
        if ($existe !== true) {
            return $existe;
        }
        $mensajes = array(
            'firstname.required'         => 'Debe ingresar nombre del personal',
            'lastname.required'          => 'Debe ingresar apellidos del personal',
            'workertype_id.required'     => 'Debe seleccionar el tipo de trabajador'
            );
        $validacion = Validator::make($request->all(),
            array(
                'lastname'        => 'required|max:100',
                'firstname'       => 'required|max:100',
                'dni'             => 'required|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/',
                'workertype_id'   => 'required|integer|exists:'.$request->session()->get('base').'.workertype,id,deleted_at,NULL'
                ), $mensajes);

        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $person = Person::where('dni','=',$request->input('dni'))->where('id','<>',$id)->first();
        if ($person !== null) {
            $error = array(
                'dni' => array(
                    'El dni ya esta siendo usado por otra persona'
                    ));
            return json_encode($error);
        }


        $error = DB::transaction(function() use($request, $id){
            $employee                = Person::find($id);
            $employee->firstname          = strtoupper(Libreria::getParam($request->input('firstname')));
            $employee->lastname           = strtoupper(Libreria::getParam($request->input('lastname')));
            $employee->workertype_id = $request->input('workertype_id');
            $employee->address       = Libreria::getParam(strtoupper($request->input('address')));
            $employee->dni           = $request->input('dni');
            if ($request->input('birthdate') !== null && $request->input('birthdate') !== '') {
                $employee->birthdate     = Date::createFromFormat('d/m/Y', $request->input('birthdate'))->format('Y-m-d');
            }
            $employee->email         = Libreria::getParam($request->input('email'));
            $employee->cellnumber    = Libreria::getParam($request->input('cellnumber'));
            $employee->observation   = Libreria::getParam($request->input('observation'));
            $employee->secondtype = 'P';
            $ismachine = $request->input('ismachine');
            if ($ismachine === 'Y') {
                $employee->type2 = 'M';
            }else{
                $employee->type2 = null;
            }
            $employee->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'person');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $person = Person::find($id);
            $person->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'person');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Person::find($id);
        $mensaje = '<p class="text-inverse fs-5">¿Esta seguro de eliminar al trabajador <strong class="text-danger">' . $modelo->firstname . ' ' . $modelo->lastname . '</strong>?</p>';
        $entidad  = 'Employee';
        $formData = array('route' => array('employee.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }


    public function indexsimple(Request $request)
    {
        $entidad          = 'Employee';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $type            = $request->input('type');
        $valor            = $request->input('valor');
        return view($this->folderview.'.adminsimple')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','type','valor'));
    }

    public function searchsimple(Request $request){
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Employee';
        $workertype_id = Libreria::getParam($request->input('workertype_id'));
        $name = Libreria::getParam($request->input('name'));
        $dni = Libreria::getParam($request->input('dni'));
        $sql = "SELECT COUNT(pe.id) as cantidad FROM person pe
                INNER JOIN workertype wt ON pe.workertype_id = wt.id
                WHERE pe.deleted_at IS NULL AND (pe.type ='E' OR pe.type2 ='E')";
        $params = array();
        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND CONCAT_WS(' ' ,pe.firstname,pe.lastname) LIKE :filter1 OR CONCAT_WS(' ' ,pe.lastname,pe.firstname) LIKE :filter2";
            $params['filter1'] = $filter;
            $params['filter2'] = $filter;
        }

        if($dni != "" && !is_null($dni)){
            $filter = '%' . $dni . '%';
            $sql .= " AND pe.dni = :dni";
            $params['dni'] = $dni;
        }

        $sql .= " ORDER BY pe.id DESC";
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
        $sql = "SELECT pe.id,CONCAT_WS(' ',pe.lastname,pe.firstname) as employeename, wt.name as job, pe.dni, pe.cellnumber,pe.address FROM person pe
                INNER JOIN workertype wt ON pe.workertype_id = wt.id
                WHERE pe.deleted_at IS NULL AND (pe.type ='E' OR pe.type2 = 'E')";

        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND CONCAT_WS(' ' ,pe.firstname,pe.lastname) LIKE :filter1 OR CONCAT_WS(' ' ,pe.lastname,pe.firstname) LIKE :filter2";
            $params['filter1'] = $filter;
            $params['filter2'] = $filter;
        }

        if($dni != "" && !is_null($dni)){
            $filter = '%' . $dni . '%';
            $sql .= " AND pe.dni = :dni";
            $params['dni'] = $dni;
        }


        $sql .= " ORDER BY pe.id DESC";
        $sql .= " LIMIT :begincompagination, :endcompagination ";

        $params["begincompagination"] = (int)$begincompagination;
        $params["endcompagination"] = (int)$filas;

        $lista = DB::connection(session('base'))->select($sql, $params);
        $cabecera = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Apellido y Nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Dirección', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro Celular', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ocupación', 'numero' => '1');
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
            return view($this->folderview . '.listsimple')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'pagina'));
        }
        return view($this->folderview . '.listsimple')->with(compact('lista', 'entidad'));
    }

}

?>
