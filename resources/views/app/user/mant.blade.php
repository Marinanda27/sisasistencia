<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($user, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="row mb-6">
	{!! Form::label('branchoffice_id', 'Sede:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
	<div class="col-lg-8 col-md-8 col-sm-8">
		{!! Form::select('branchoffice_id', $cboBranchoffice, NULL, array('class' => 'form-select form-select-solid text-dark', 'id' => 'branchoffice_id')) !!}
	</div>
</div>

<div class="row mb-6">
	{!! Form::label('usertype_id', 'Tipo de usuario:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
	<div class="col-lg-8 col-md-8 col-sm-8">
		{!! Form::select('usertype_id', $cboUsertype, NULL, array('class' => 'form-select form-select-solid text-dark', 'id' => 'usertype_id')) !!}
	</div>
</div>
<div class="row mb-6 align-items-center">
	{!! Form::label('personname', 'Persona:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
	{!! Form::hidden('person_id', $person_id, array('id' => 'person_id')) !!}
	<div class="col-lg-8 col-md-8 col-sm-8">
      <div class="d-flex gap-2">
         {!! Form::text('personname', null, array('class' => 'form-control text-uppercase form-control-solid text-dark', 'id' => 'personname', 'placeholder' => 'Selecciona a la persona')) !!}
         <span class="input-group-btn">
               {!! Form::button('<i class="fa fa-search"></i>', array('class' => 'btn waves-effect waves-light btn-info', 'onclick' => 'modal (\''.URL::route('employee.indexsimple', array('listar'=>'SI','type'=>'reserve')).'\', \'Trabajadores'.' <button onclick="cerrarModal();" class="btn btn-warning btn-sm"> Cerrar</button>\', this);', 'title' => 'Seleccionar Trabajador')) !!}
         </span>
      </div>
	</div>
</div>

<div class="row mb-8">
	{!! Form::label('login', 'Usuario:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
	<div class="col-lg-8 col-md-8 col-sm-8">
		{!! Form::text('login', null, array('class' => 'form-control form-control-solid text-dark', 'id' => 'login', 'placeholder' => 'Ingrese login')) !!}
	</div>
</div>
<div class="row mb-6">
	{!! Form::label('password', 'Contraseña:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
	<div class="col-lg-8 col-md-8 col-sm-8">
		{!! Form::password('password', array('class' => 'form-control form-control-solid text-dark', 'id' => 'password', 'placeholder' => 'Ingrese contraseña')) !!}
	</div>
</div>

<div class="d-flex justify-content-center gap-4 mt-10">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success px-8', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning px-8', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>

{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('450');
	});

	function addemployee(elemento) {
		var employee_id = $('#txtEmployee' + elemento).val();
		var name = $('#txtNombre' + elemento).val();
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val(employee_id);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="personname"]').val(name);
		$.Notification.autoHideNotify('success', 'top right', 'Trabajador Agregado Correctamente','La accion se ejecutó sin errores');
		cerrarModal();
	}
</script>
