
<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0 pb-5 pt-5">{{ $title }}</h1>
<div class="card mb-5 mb-xl-10">
    <div class="card-body py-6">
        {!! Form::open(['route' => $ruta["search"], 'method' => 'POST','onsubmit' => 'return false;','autocomplete' => 'off','id' => 'formBusqueda'.$entidad,'class' => 'row gx-4 gy-2 align-items-end']) !!}
         {!! Form::hidden('page', 1, array('id' => 'page')) !!}
         {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}

        <!-- Sede -->
        <div class="col-auto">
            {!! Form::label('branchoffice_id', 'Sede:', ['class' => 'fw-bold text-dark']) !!}
            {!! Form::select('branchoffice_id', $cboBranchoffice, null, ['class' => 'form-select form-select-sm text-dark','onchange' => "buscar('$entidad')"]) !!}
        </div>

        <!-- Tipo usuario -->
        <div class="col-auto">
            {!! Form::label('usertype_id', 'Tipo usuario:', ['class' => 'fw-bold text-dark']) !!}
            {!! Form::select('usertype_id', $cboUsertype, null, ['class' => 'form-select form-select-sm text-dark','onchange' => "buscar('$entidad')"]) !!}
        </div>

        <!-- Nombre -->
        <div class="col-auto">
            {!! Form::label('login', 'Nombre:', ['class' => 'fw-bold text-dark']) !!}
            {!! Form::text('login', null, ['class' => 'form-control form-control-sm text-dark']) !!}
        </div>

        <!-- DNI -->
        <div class="col-auto">
            {!! Form::label('dni', 'DNI:', ['class' => 'fw-bold text-dark']) !!}
            {!! Form::text('dni', null, ['class' => 'form-control form-control-sm text-dark']) !!}
        </div>

        <!-- Filas -->
        <div class="col-auto">
            {!! Form::label('filas', 'Filas a mostrar:', ['class' => 'fw-bold text-dark']) !!}
            {!! Form::selectRange('filas', 1, 30, 10, ['class' => 'form-select form-select-sm text-dark','onchange' => "buscar('$entidad')"]) !!}
        </div>

        <!-- Botones -->
        <div class="col-auto d-flex gap-2">
            {!! Form::button('<i class="fa fa-search"></i> Buscar', ['class' => 'btn btn-success btn-sm','onclick' => "buscar('$entidad')"]) !!}

            {!! Form::button('<i class="fa fa-plus"></i> Nuevo', ['class' => 'btn btn-info btn-sm','onclick' => "modal('".URL::route($ruta["create"], ['listar'=>'SI'])."', '$titulo_registrar', this)"]) !!}
        </div>

        {!! Form::close() !!}

        <div class="mt-10">
            <div id="listado{{ $entidad }}"></div>
               <table id="datatable" class="table table-striped table-bordered">
               </table>
            </div>
         </div>
    </div>
</div>

<script>
	$(document).ready(function () {
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="login"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});
</script>