<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0 pb-5 pt-5">{{ $title }}</h1>

{{-- Resumen del día --}}
@if(isset($resumenDia))
<div class="row g-4 mb-6">
    {{-- Total personas --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center gap-4 py-5">
                <div class="symbol symbol-50px flex-shrink-0">
                    <div class="symbol-label bg-light-primary">
                        <i class="ki-duotone ki-profile-user text-primary fs-2x"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    </div>
                </div>
                <div>
                    <div class="fs-1 fw-bolder text-dark">{{ $resumenDia['total'] }}</div>
                    <div class="fw-semibold text-gray-500 fs-7">Total registrados</div>
                </div>
            </div>
        </div>
    </div>
    {{-- Puntuales --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush shadow-sm border-0 h-100" style="border-left: 4px solid #50cd89 !important;">
            <div class="card-body d-flex align-items-center gap-4 py-5">
                <div class="symbol symbol-50px flex-shrink-0">
                    <div class="symbol-label bg-light-success">
                        <i class="ki-duotone ki-check-circle text-success fs-2x"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div>
                    <div class="fs-1 fw-bolder text-success">{{ $resumenDia['puntual'] }}</div>
                    <div class="fw-semibold text-gray-500 fs-7">Llegaron puntual</div>
                    @if(isset($resumenDia['horario']) && $resumenDia['horario'])
                    <span class="badge badge-light-success fs-8">≤ {{ substr($resumenDia['hora_limite'], 0, 5) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Tardanzas --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush shadow-sm border-0 h-100" style="border-left: 4px solid #ffc700 !important;">
            <div class="card-body d-flex align-items-center gap-4 py-5">
                <div class="symbol symbol-50px flex-shrink-0">
                    <div class="symbol-label bg-light-warning">
                        <i class="ki-duotone ki-time text-warning fs-2x"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div>
                    <div class="fs-1 fw-bolder text-warning">{{ $resumenDia['tardanza'] }}</div>
                    <div class="fw-semibold text-gray-500 fs-7">Con tardanza</div>
                    @if(isset($resumenDia['horario']) && $resumenDia['horario'])
                    <span class="badge badge-light-warning fs-8">> {{ substr($resumenDia['hora_limite'], 0, 5) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Ausentes --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush shadow-sm border-0 h-100" style="border-left: 4px solid #f1416c !important;">
            <div class="card-body d-flex align-items-center gap-4 py-5">
                <div class="symbol symbol-50px flex-shrink-0">
                    <div class="symbol-label bg-light-danger">
                        <i class="ki-duotone ki-cross-circle text-danger fs-2x"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div>
                    <div class="fs-1 fw-bolder text-danger">{{ $resumenDia['ausentes'] }}</div>
                    <div class="fw-semibold text-gray-500 fs-7">Ausentes hoy</div>
                    <span class="badge badge-light-danger fs-8">{{ $today }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@if(!isset($resumenDia['horario']) || !$resumenDia['horario'])
<div class="alert alert-warning d-flex align-items-center mb-5">
    <i class="ki-duotone ki-information-5 fs-2hx text-warning me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
    <div class="d-flex flex-column">
        <h4 class="mb-1 text-dark">Sin horario configurado</h4>
        <span>No hay un horario de entrada registrado. <a href="{{ route('schedule.index') }}" class="fw-bold">Configurar horario</a></span>
    </div>
</div>
@endif
@endif

<div class="card mb-5 mb-xl-10">
   <div class="card-body py-6">
      {!! Form::open(['route' => $ruta["search"], 'method' => 'POST', 'onsubmit' => 'return false;', 'class' => 'row gx-4 gy-2 align-items-end', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
      {!! Form::hidden('page', 1, ['id' => 'page']) !!}
      {!! Form::hidden('accion', 'listar', ['id' => 'accion']) !!}

      <div class="col-auto">
         {!! Form::label('name', 'Nombre:', ['class' => 'fw-bold text-dark']) !!}
         {!! Form::text('name', '', ['class' => 'form-control form-control-sm text-dark', 'id' => 'name', 'placeholder' => 'Buscar por nombre...']) !!}
      </div>

      <div class="col-auto">
         {!! Form::label('begindate', 'Fecha desde:', ['class' => 'fw-bold text-dark']) !!}
         {!! Form::date('begindate', date('Y-m-01'), ['class' => 'form-control form-control-sm text-dark', 'id' => 'begindate']) !!}
      </div>

      <div class="col-auto">
         {!! Form::label('enddate', 'Fecha hasta:', ['class' => 'fw-bold text-dark']) !!}
         {!! Form::date('enddate', date('Y-m-t'), ['class' => 'form-control form-control-sm text-dark', 'id' => 'enddate']) !!}
      </div>

      <div class="col-auto">
         {!! Form::label('filtro_tardanza', 'Estado:', ['class' => 'fw-bold text-dark']) !!}
         {!! Form::select('filtro_tardanza', ['todos' => 'Todos', 'puntual' => '✅ Puntual', 'tardanza' => '⚠️ Tardanza'], 'todos', ['class' => 'form-select form-select-sm text-dark', 'id' => 'filtro_tardanza']) !!}
      </div>

      <div class="col-auto">
         {!! Form::label('filas', 'Filas:', ['class' => 'fw-bold text-dark']) !!}
         {!! Form::selectRange('filas', 1, 30, 10, ['class' => 'form-select form-select-sm text-dark', 'onchange' => 'buscar(\''.$entidad.'\')']) !!}
      </div>

      <div class="col-auto d-flex gap-2">
         {!! Form::button('<i class="fa fa-search"></i> Buscar', ['class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')']) !!}
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
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="name"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
        // Disparar búsqueda al cambiar filtro de tardanza
        $('#filtro_tardanza').on('change', function(){
            buscar('{{ $entidad }}');
        });
	});
</script>
