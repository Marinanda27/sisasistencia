
<div class="row">
    <div class="col-sm-12">
        <div class="card-box table-responsive">
            <div class="card mb-5 mb-xl-10">
               <div class="card-body py-5">
                  {!! Form::open(['route' => $ruta["searchsimple"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'row gx-4 gy-2 align-items-end', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
                  {!! Form::hidden('page', 1, array('id' => 'page')) !!}
                  {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
                  {!! Form::hidden('type', $type, array('id' => 'type')) !!}
                  {!! Form::hidden('valor', $valor, array('id' => 'valor')) !!}
                  <div class="col-auto">
                     {!! Form::label('name', 'Nombre:',['class' => 'fw-bold text-dark']) !!}
                     {!! Form::text('name', '', array('class' => 'form-control form-control-sm text-dark', 'id' => 'name')) !!}
                  </div>
                     <div class="col-auto">
                     {!! Form::label('dni', 'Dni:',['class' => 'fw-bold text-dark']) !!}
                     {!! Form::text('dni', '', array('class' => 'form-control form-control-sm text-dark', 'id' => 'dni')) !!}
                  </div>
                  <div class="col-auto">
                     {!! Form::label('filas', 'Filas a mostrar:',['class' => 'fw-bold text-dark'])!!}
                     {!! Form::selectRange('filas', 1, 30, 6, array('class' => 'form-select form-select-sm text-dark', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                  </div>
                  <div class="col-auto d-flex gap-2">
                     {!! Form::button('<i class="fa fa-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
                     {!! Form::button('<i class="fa fa-plus"></i> Nuevo', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}
                  </div>
                  {!! Form::close() !!}

                  <div class="mt-10">
                     <div id="listado{{ $entidad }}"></div>
                     {!! Form::button('Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancel', 'onclick' => 'cerrarModal();')) !!}
                     <table id="datatable" class="table table-striped table-bordered">
                     </table>
                  </div>
               </div>
              
            </div>
        </div>
    </div>
</div>
<script>
	$(document).ready(function () {
		buscar('{{ $entidad }}');
		configurarAnchoModal ('1000');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="name"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});
</script>
