<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($branchoffice, $formData) !!}
<?php
$url = URL::route("branchoffice.store");
if ($branchoffice !== null) {
	$url = URL::route("branchoffice.update", array($branchoffice->id, 'listar'=>'SI'));
}
?>	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="row mb-6">
		{!! Form::label('name', 'Nombre Sede:', array('class' => 'col-lg-3 col-md-3 col-sm-3 col-form-label fw-bold fs-6')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('name', null, array('class' => 'form-control form-control-solid text-dark', 'id' => 'name', 'placeholder' => 'Ingrese nombre de la sede')) !!}
		</div>
	</div>
	
   <div class="d-flex justify-content-center gap-4 mt-10">
      {!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar','type' => 'submit')) !!}
      {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
   </div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('600');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
});


$(function(){
    $("#formMantenimientoBranchoffice").on("submit", function(e){
        var btn = $('#btnGuardar');
        btn.button('loading');
        e.preventDefault();
        var f = $(this);
        var formData = new FormData(document.getElementById("formMantenimientoBranchoffice"));
        formData.append("dato", "valor");
        $.ajax({
            url: "{{ $url }}",
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
	        processData: false
		}).done(function(res){
            var btn = $('#btnGuardar');
            btn.button('reset');
            if (res == 'OK') {
                cerrarModal();
				buscarCompaginado('', 'Accion realizada correctamente', '{!! $entidad !!}', 'OK');
            }else{
                mostrarErrores(res, 'formMantenimientoBranchoffice', '{!! $entidad !!}');
            }
		}).fail(function(jqXHR, textStatus, errorThrown){
            var btn = $('#btnGuardar');
            btn.button('reset');
		});
    });
});
</script>
