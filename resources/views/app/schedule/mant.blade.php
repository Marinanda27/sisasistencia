<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($schedule, $formData) !!}
	{!! Form::hidden('listar', $listar, ['id' => 'listar']) !!}
	<div class="row mb-6">
		{!! Form::label('start_time', 'Hora de entrada:', ['class' => 'col-lg-4 col-md-4 col-sm-4 col-form-label fw-bold fs-6']) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::time('start_time', isset($schedule) ? $schedule->start_time : null, ['class' => 'form-control form-control-solid text-dark', 'id' => 'start_time', 'placeholder' => 'Ej: 08:00']) !!}
            <small class="text-muted">Hora exacta de inicio de jornada</small>
		</div>
	</div>
	<div class="row mb-6">
		{!! Form::label('grace_minutes', 'Minutos de tolerancia:', ['class' => 'col-lg-4 col-md-4 col-sm-4 col-form-label fw-bold fs-6']) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::number('grace_minutes', isset($schedule) ? $schedule->grace_minutes : 0, ['class' => 'form-control form-control-solid text-dark', 'id' => 'grace_minutes', 'min' => '0', 'max' => '120', 'placeholder' => 'Ej: 10']) !!}
            <small class="text-muted">Minutos de gracia antes de marcar tardanza (0 = sin tolerancia)</small>
		</div>
	</div>
	<div class="d-flex justify-content-center gap-2 mt-10">
      {!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, ['class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)']) !!}
      {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', ['class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();']) !!}
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('480');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
});
</script>
