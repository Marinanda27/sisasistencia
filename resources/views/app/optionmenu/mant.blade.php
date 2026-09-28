<?php
$icono = 'fa fa-bank';
if ($optionmenu !== NULL) {
	$icono = $optionmenu->icon;
}
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($optionmenu, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="row mb-6">
	{!! Form::label('menuoptioncategory_id', 'Categoria:', array('class' => 'col-lg-3 col-md-3 col-sm-3 col-form-label fw-bold fs-6')) !!}
	<div class="col-lg-9 col-md-9 col-sm-9">
		{!! Form::select('menuoptioncategory_id', $cboCategoria, null, array('class' => 'form-select form-select-solid text-dark', 'id' => 'menuoptioncategory_id')) !!}
	</div>
</div>
<div class="row mb-6">
	{!! Form::label('name', 'Nombre:', array('class' => 'col-lg-3 col-md-3 col-sm-3 col-form-label fw-bold fs-6')) !!}
	<div class="col-lg-9 col-md-9 col-sm-9">
		{!! Form::text('name', null, array('class' => 'form-control form-control-solid text-dark', 'id' => 'name', 'placeholder' => 'Ingrese nombre')) !!}
	</div>
</div>
<div class="row mb-6">
	{!! Form::label('link', 'Link:', array('class' => 'col-lg-3 col-md-3 col-sm-3 col-form-label fw-bold fs-6')) !!}
	<div class="col-lg-9 col-md-9 col-sm-9">
		{!! Form::text('link', null, array('class' => 'form-control form-control-solid text-dark', 'id' => 'link', 'placeholder' => 'Ingrese link')) !!}
	</div>
</div>
<div class="row mb-6">
	{!! Form::label('order', 'Orden:', array('class' => 'col-lg-3 col-md-3 col-sm-3 col-form-label fw-bold fs-6')) !!}
	<div class="col-lg-9 col-md-9 col-sm-9">
		{!! Form::text('order', null, array('class' => 'form-control form-control-solid text-dark', 'id' => 'order', 'placeholder' => 'Ingrese orden')) !!}
	</div>
</div>
<div class="row mb-6">
	{!! Form::label('icon', 'Icono:', array('class' => 'col-lg-3 col-md-3 col-sm-3 col-form-label fw-bold fs-6')) !!}
	<div class="col-lg-9 col-md-9 col-sm-9">
		{!! Form::text('icon', $icono, array('class' => 'form-control form-control-solid text-dark', 'id' => 'icon', 'placeholder' => 'Ingrese icono')) !!}
	</div>
</div>
<div class="d-flex justify-content-center gap-2 mt-10">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('450');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	});
</script>
