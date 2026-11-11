<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($person, $formData) !!}
	{!! Form::hidden('listar', $listar, ['id' => 'listar']) !!}
	<div class="row mb-6">
		{!! Form::label('firstname', 'Nombres:', ['class' => 'col-lg-4 col-md-4 col-sm-4 col-form-label fw-bold fs-6 required']) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::text('firstname', null, ['class' => 'form-control form-control-solid text-dark text-uppercase', 'id' => 'firstname', 'maxlength' => '100']) !!}
		</div>
	</div>
	<div class="row mb-6">
		{!! Form::label('lastname', 'Apellidos:', ['class' => 'col-lg-4 col-md-4 col-sm-4 col-form-label fw-bold fs-6 required']) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::text('lastname', null, ['class' => 'form-control form-control-solid text-dark text-uppercase', 'id' => 'lastname', 'maxlength' => '100']) !!}
		</div>
	</div>
	<div class="row mb-6">
		{!! Form::label('dni', 'DNI:', ['class' => 'col-lg-4 col-md-4 col-sm-4 col-form-label fw-bold fs-6']) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::text('dni', null, ['class' => 'form-control form-control-solid text-dark', 'id' => 'dni', 'maxlength' => '8', 'inputmode' => 'numeric', 'placeholder' => '8 dígitos']) !!}
		</div>
	</div>
	<div class="row mb-6">
		{!! Form::label('grade', 'Grado:', ['class' => 'col-lg-4 col-md-4 col-sm-4 col-form-label fw-bold fs-6']) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::select('grade', $cboGrade, $person->grade, ['class' => 'form-select form-select-solid text-dark', 'id' => 'grade']) !!}
		</div>
	</div>
	<div class="row mb-6">
		{!! Form::label('section', 'Sección:', ['class' => 'col-lg-4 col-md-4 col-sm-4 col-form-label fw-bold fs-6']) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::select('section', $cboSection, $person->section, ['class' => 'form-select form-select-solid text-dark', 'id' => 'section']) !!}
		</div>
	</div>
	<div class="d-flex justify-content-center gap-2 mt-10">
      {!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, ['class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)']) !!}
      {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', ['class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();']) !!}
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('520');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	$('#dni').on('input', function() { this.value = this.value.replace(/\D/g, '').slice(0, 8); });
});
</script>
