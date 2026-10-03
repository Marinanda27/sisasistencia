<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0 pb-5 pt-5">{{ $title }}</h1>

<div class="card mb-5 mb-xl-10">
   <div class="card-body py-6">
      {!! Form::open(['route' => $ruta["search"], 'method' => 'POST', 'onsubmit' => 'return false;', 'class' => 'row gx-4 gy-2 align-items-end', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
      {!! Form::hidden('page', 1, ['id' => 'page']) !!}
      {!! Form::hidden('accion', 'listar', ['id' => 'accion']) !!}
      <div class="col-auto">
         {!! Form::label('filas', 'Filas a mostrar:', ['class' => 'fw-bold text-dark']) !!}
         {!! Form::selectRange('filas', 1, 30, 10, ['class' => 'form-select form-select-sm text-dark', 'onchange' => 'buscar(\''.$entidad.'\')']) !!}
      </div>
      <div class="col-auto d-flex gap-2">
         {!! Form::button('<i class="fa fa-search"></i> Buscar', ['class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')']) !!}
         {!! Form::button('<i class="fa fa-plus"></i> Nuevo Horario', ['class' => 'btn btn-info waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], ['listar'=>'SI']).'\', \''.$titulo_registrar.'\', this);']) !!}
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
	});
</script>
