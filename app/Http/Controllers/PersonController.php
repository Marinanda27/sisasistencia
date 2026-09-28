<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Auth;
use App\Models\Person;
use App\Models\Workertype;
use Illuminate\Support\Facades\Validator;

class PersonController extends Controller{
    protected $folderview      = 'app.person';
    protected $tituloAdmin     = 'Personas';
    protected $tituloRegistrar = 'Registrar Persona';
    protected $tituloModificar = 'Modificar Persona';
    protected $tituloEliminar  = 'Eliminar Persona';
    protected $rutas           = array('create' => 'person.create',
        'edit'   => 'person.edit',
        'delete' => 'person.eliminar',
        'search' => 'person.search',
        'index'  => 'person.index',
        'searchsimple' => 'person.searchsimple',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $entidad          = 'Customer';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
       
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function search(Request $request){
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Customer';
        $name = Libreria::getParam($request->input('name'));
        $dni = Libreria::getParam($request->input('dni'));
        $sql = "SELECT COUNT(pe.id) as cantidad FROM person pe
                WHERE pe.deleted_at IS NULL AND (pe.type ='P' OR pe.type2 ='P') AND pe.id <> 1";
        $params = array();
        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND CONCAT_WS(' ' ,pe.firstname,pe.lastname) LIKE :filter1 OR CONCAT_WS(' ' ,pe.lastname,pe.firstname) LIKE :filter2 OR pe.bussinesname LIKE :filter3";
            $params['filter1'] = $filter;
            $params['filter2'] = $filter;
            $params['filter3'] = $filter;
        }

        if($dni != "" && !is_null($dni)){
            $filter = '%' . $dni . '%';
            $sql .= " AND pe.dni LIKE :dni1";
            $params['dni1'] = $filter;
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
        $sql = "SELECT pe.id,CONCAT_WS(' ',pe.lastname,pe.firstname) as personname,pe.dni FROM person pe
                WHERE pe.deleted_at IS NULL AND (pe.type ='P' OR pe.type2 = 'P') AND pe.id <> 1";

        if ($name != "" && !is_null($name)) {
            $filter = '%' . $name . '%';
            $sql .= " AND CONCAT_WS(' ' ,pe.firstname,pe.lastname) LIKE :filter1 OR CONCAT_WS(' ' ,pe.lastname,pe.firstname) LIKE :filter2 OR pe.bussinesname LIKE :filter3";
            $params['filter1'] = $filter;
            $params['filter2'] = $filter;
            $params['filter3'] = $filter;
        }

        if($dni != "" && !is_null($dni)){
            $filter = '%' . $dni . '%';
            $sql .= " AND pe.dni LIKE :dni1";
            $params['dni1'] = $filter;
        }

        $sql .= " ORDER BY pe.id DESC";
        $sql .= " LIMIT :begincompagination, :endcompagination ";

        $params["begincompagination"] = (int)$begincompagination;
        $params["endcompagination"] = (int)$filas;

        $lista = DB::connection(session('base'))->select($sql, $params);
        $cabecera = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Persona', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
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
        $entidad         = 'Customer';
        $user = Auth::user();
        $customer        = null;
        $formData        = array('customer.store');
        $formData        = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton           = 'Registrar';
        $type2 = null;
        $cboSecontype = array('E'=>"EMPRESA","P"=>"PERSONA");
        return view($this->folderview.'.mant')->with(compact('customer', 'formData', 'entidad', 'boton', 'listar', 'type2','cboSecontype'));
    }

    public function store(Request $request){
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        if ($request->input('ruc') !== null && $request->input('ruc') !== '') {
            $person = Person::where('ruc','=',$request->input('ruc'))->orWhere('dni','=',$request->input('dni'))->where(function($query) {
                $query->where('type','=','S')->orWhere('type2','=','S');
            })->first();
            if ($person !== null) {
                $error = array(
                'ruc' => array(
                    'N° RUC / DNI ya esta siendo usado por otro proveedor'
                    ));
            return json_encode($error);
            }
        }
        $secondtype = $request->input('secondtype');
        if ($secondtype === 'E') {
            $reglas = array(
               'bussinesname'        => 'required|max:100',
               'ruc'                  => 'required',
            );

            $mensajes = array(
               'bussinesname.required'    => 'Debe ingresar razón social del proveedor',
               'ruc.required'          => 'Debe ingresar RUC del proveedor',
            );

        }else{
            $reglas = array(
               'lastname'        => 'required|max:100',
               'firstname'       => 'required|max:100',
               'dni'             => 'required|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/',
            );

            $mensajes = array(
               'lastname.required'         => 'Debe ingresar apellidos del proveedor',
               'firstname.required'        => 'Debe ingresar nombre del proveedor',
               'dni.required'              => 'Debe ingresar DNI del proveedor',
            );
        }
        

        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $error = DB::transaction(function() use($request){
            Date::setLocale('es');
            $supplier                = new Person();
            $person = Person::where('ruc','=',$request->input('ruc'))->orWhere('dni','=',$request->input('dni'))->where(function($query) {
                $query->where('type','=','S')->orWhere('type2','=','S');
            })->first();
            if ($person !== null) {
               $supplier = Person::find($person->id);
            }
            $secondtype = $request->input('secondtype');
            if ($secondtype === 'E') {
               $supplier->bussinesname    = strtoupper(Libreria::getParam($request->input('bussinesname')));
               $supplier->ruc           = $request->input('ruc');
            } else {
               $supplier->dni           = $request->input('dni');
               $supplier->firstname          = strtoupper(Libreria::getParam($request->input('firstname')));
               $supplier->lastname           = strtoupper(Libreria::getParam($request->input('lastname')));
            }
            $supplier->address       = Libreria::getParam(strtoupper($request->input('address')));
            $supplier->email         = Libreria::getParam($request->input('email'));
            $supplier->cellnumber    = Libreria::getParam($request->input('cellnumber'));
            $supplier->observation   = Libreria::getParam($request->input('observation'));

            if ($supplier->type === null) {
                $supplier->type = 'S';
            }elseif ($supplier->type2 === null) {
                $supplier->type2 = 'S';
            }
            $supplier->secondtype = $secondtype;
            $supplier->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function edit(Request $request, $id){
        $existe = Libreria::verificarExistencia($id, 'person');
        if ($existe !== true) {
            return $existe;
        }
        $listar          = Libreria::getParam($request->input('listar'), 'NO');
        $supplier        = Person::find($id);
        $type2           = $supplier->type2;
        $entidad         = 'Supplier';

        $cboWorkertype   = array('' => 'Seleccione') + Workertype::pluck('name', 'id')->all();

        $formData        = array('supplier.update', $id);
        $formData        = array('route' => $formData, 'method' => 'PUT', 'class' => 'form', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton           = 'Modificar';

        $cboSecontype = array('E'=>"EMPRESA","P"=>"PERSONA");

      
        return view($this->folderview.'.mant')->with(compact('supplier', 'formData', 'entidad', 'boton','listar', 'cboWorkertype','cboSecontype','type2'));
    }

    public function update(Request $request, $id){
        $existe = Libreria::verificarExistencia($id, 'person');
        if ($existe !== true) {
            return $existe;
        }
        $secondtype = $request->input('secondtype');
         if ($secondtype === 'E') { 
            $reglas = array(
               'bussinesname'        => 'required|max:100',
               'ruc'                  => 'required',
            );

            $mensajes = array(
               'bussinesname.required'    => 'Debe ingresar razón social del proveedor',
               'ruc.required'          => 'Debe ingresar RUC del proveedor',
            );
         }else{
            $reglas = array(
               'lastname'        => 'required|max:100',
               'firstname'       => 'required|max:100',
               'dni'             => 'required|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/',
            );

            $mensajes = array(
               'lastname.required'         => 'Debe ingresar apellidos del proveedor',
               'firstname.required'        => 'Debe ingresar nombre del proveedor',
               'dni.required'              => 'Debe ingresar DNI del proveedor',
            );
         }

        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $person = Person::where('dni','=',$request->input('dni'))->orWhere('ruc','=',$request->input('ruc'))
                  ->where(function($query) {
                     $query->where('type','=','S')->orWhere('type2','=','S');
                  })
                  ->where('id','<>',$id)
                  ->first();
        if ($person !== null) {
            $error = array(
                'secondtype' => array(
                    'El ruc / dni ya esta siendo usado por otro proveedor'
                  ));
            return json_encode($error);
        }


        $error = DB::transaction(function() use($request, $id){
            $supplier                = Person::find($id);
            $secondtype = $request->input('secondtype');
            if ($secondtype === 'E') {
               $supplier->bussinesname    = strtoupper(Libreria::getParam($request->input('bussinesname')));
               $supplier->ruc           = $request->input('ruc');
               $supplier->dni           = null;
               $supplier->firstname     = null;
               $supplier->lastname      = null;
            } else {
               $supplier->dni           = $request->input('dni');
               $supplier->firstname     = strtoupper(Libreria::getParam($request->input('firstname')));
               $supplier->lastname      = strtoupper(Libreria::getParam($request->input('lastname')));
               $supplier->bussinesname  = null;
               $supplier->ruc           = null;
            }
            $supplier->address       = Libreria::getParam(strtoupper($request->input('address')));

            $supplier->email         = Libreria::getParam($request->input('email'));
            $supplier->cellnumber    = Libreria::getParam($request->input('cellnumber'));
            $supplier->observation   = Libreria::getParam($request->input('observation'));
            $supplier->secondtype    = $request->input('secondtype');

            $supplier->save();
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

            // si existe embeddings eliminarlos
            
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
        $proveedorname = $modelo->firstname . ' ' . $modelo->lastname;
         if ($modelo->secondtype === 'E') {
            $proveedorname = $modelo->bussinesname;
         }
        $mensaje = '<p class="text-inverse fs-5">¿Esta seguro de eliminar al proveedor <strong class="text-danger">' . $proveedorname . '</strong>?</p>';
        $entidad  = 'Supplier';
        $formData = array('route' => array('supplier.destroy', $id), 'method' => 'DELETE', 'class' => 'form', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
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
