<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($employee, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<?php
$date = null; $entrydate = null; $entrydatepayroll = null;
if ($employee !== NULL) {
	$date = date('d/m/Y',strtotime($employee->birthdate));
	if ($employee->entrydate !== null) {
		$entrydate = date('d/m/Y',strtotime($employee->entrydate));
	}
	if ($employee->entrydatepayroll !== null) {
		$entrydatepayroll = date('d/m/Y',strtotime($employee->entrydatepayroll));
	}
}
?>
<style>
  .required-label::before {
      content: "*";
      color: red;
      margin-right: 4px;
   }


</style>

<div class="row">
   <div class="col-lg-6 col-md-6 col-sm-6">
      <div class="col-lg-12 col-md-12 col-sm-12">
         <h5 align="center"><b>Datos Principales</b></h5>
         <hr style="margin-left: 10%; border:1px solid #8a8a8a; margin-top:15px !important">
         <div class="row mb-6">
            {!! Form::label('workertype_id', 'Tipo de trabajador:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6 required-label')) !!}
            <div class="col-lg-8 col-md-8 col-sm-8">
               {!! Form::select('workertype_id', $cboWorkertype, NULL, array('class' => 'form-select form-select-solid text-dark', 'id' => 'workertype_id')) !!}
            </div>
         </div>

         <div class="row mb-6">
            {!! Form::label('dni', 'DNI:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6 required-label')) !!}
            <div class="col-lg-8 col-md-8 col-sm-8">
               {!! Form::text('dni', null, array('class' => 'form-control form-control-solid text-dark', 'id' => 'dni', 'placeholder' => 'Ingrese DNI', 'maxlength' => '8')) !!}
            </div>
         </div>
         <div class="row mb-6">
            {!! Form::label('lastname', 'Apellidos:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6 required-label')) !!}
            <div class="col-lg-8 col-md-8 col-sm-8">
               {!! Form::text('lastname', null, array('class' => 'form-control form-control-solid text-dark', 'id' => 'lastname', 'placeholder' => 'Ingrese apellidos')) !!}
            </div>
         </div>

         <div class="row mb-6">
            {!! Form::label('firstname', 'Nombre:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6 required-label')) !!}
            <div class="col-lg-8 col-md-8 col-sm-8">
               {!! Form::text('firstname', null, array('class' => 'form-control form-control-solid text-dark', 'id' => 'firstname', 'placeholder' => 'Ingrese nombre')) !!}
            </div>
         </div>

         <div class="row mb-6">
            {!! Form::label('cellnumber', 'Celular:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
            <div class="col-lg-8 col-md-8 col-sm-8">
               {!! Form::text('cellnumber', null, array('class' => 'form-control form-control-solid text-dark', 'id' => 'cellnumber', 'placeholder' => 'Ingrese celular')) !!}
            </div>
         </div>

      </div>
   </div>

   <div class="col-lg-6 col-md-6 col-sm-6">
      <div class="col-lg-12 col-md-12 col-sm-12">
         <h5 align="center"><b>Otros Datos</b></h5>
         <hr style="margin-left: 10%; border:1px solid #8a8a8a; margin-top:15px !important">

         <div class="row mb-6">
            {!! Form::label('address', 'Dirección:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
            <div class="col-lg-8 col-md-8 col-sm-8">
               {!! Form::text('address', null, array('class' => 'form-control form-control-solid text-dark', 'id' => 'address', 'placeholder' => 'Ingrese dirección')) !!}
            </div>
         </div>
         <div class="row mb-6">
            {!! Form::label('email', 'Email:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
            <div class="col-lg-8 col-md-8 col-sm-8">
               {!! Form::text('email', null, array('class' => 'form-control form-control-solid text-dark', 'id' => 'email', 'placeholder' => 'Ingrese email')) !!}
            </div>
         </div>
         <div class="row mb-6">
            {!! Form::label('birthdate', 'Fecha nacimiento:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
            <div class="col-lg-8 col-md-8 col-sm-8">
               <div class='input-group input-group-xs' id='divfechanacimiento'>
                  {!! Form::text('birthdate', $date, array('class' => 'form-control form-control-solid text-dark', 'id' => 'birthdate', 'placeholder' => 'Ingrese fecha de nacimiento')) !!}
                  <span class="input-group-btn">
                     <button class="btn btn-secondary calendar">
                        <i class="fa fa-calendar"></i>
                     </button>
                  </span>
               </div>
            </div>
         </div>
         <div class="row mb-6">
            {!! Form::label('status', 'Estado:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
            <div class="col-lg-8 col-md-8 col-sm-8">
               {!! Form::select('status', $cboStatus, NULL, array('class' => 'form-select form-select-solid text-dark', 'id' => 'status')) !!}
            </div>
         </div>
         <div class="row mb-6">
            {!! Form::label('observation', 'Observación:', array('class' => 'col-lg-4 col-form-label fw-bold fs-6')) !!}
            <div class="col-lg-8 col-md-8 col-sm-8">
               {!! Form::textarea('observation', null, array('style' => 'resize: none;', 'rows' => '3','class' => 'form-control form-control-solid text-dark', 'id' => 'observation', 'placeholder' => 'Ingrese observacion')) !!}
            </div>
         </div>

      </div>
   </div>
</div>
<br>
<div class="d-flex justify-content-between gap-4 mt-10">
      <p class="text-danger"><b>Campos Obligatorios (*)</b></p>
      <div>
         {!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
         {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar', 'onclick' => 'cerrarModal();')) !!}
      </div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
        configurarAnchoModal ('900');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="workertype_id"]').focus();
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="phonenumber"]').inputmask('Regex', { regex: "[0-9]+-[0-9]+" });
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="cellnumber"]').inputmask('Regex', { regex: "[*]?[#]?[0-9]+-[0-9]+" });
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="birthdate"]').inputmask("dd/mm/yyyy");

		jQuery('#birthdate').datepicker({
	        autoclose: true,
	        todayHighlight: true,
	        format: "dd/mm/yyyy",
	    });

	});
</script>
