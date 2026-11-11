<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0 pb-5 pt-5">
    <i class="ki-duotone ki-chart-line-star text-primary fs-2hx me-3"><span class="path1"></span><span class="path2"></span></i>
    Reporte de Asistencias
</h1>

{{-- Filtros del reporte --}}
<div class="card mb-6 shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-5 text-dark">Filtros del reporte</span>
        </h3>
    </div>
    <div class="card-body py-4">
        {!! Form::open(['route' => 'reporte.asistencias.post', 'method' => 'POST', 'onsubmit' => 'return false;', 'class' => 'row gx-4 gy-3 align-items-end', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusquedaAsistencia']) !!}
        {!! Form::hidden('page', 1, ['id' => 'pageAsistencia']) !!}

        <div class="col-xl col-md-4 col-sm-6">
            {!! Form::label('name', 'Nombre:', ['class' => 'fw-bold text-dark fs-7 mb-1']) !!}
            {!! Form::text('name', '', ['class' => 'form-control form-control-sm', 'id' => 'nameAsistencia', 'placeholder' => 'Buscar por nombre...']) !!}
        </div>

        <div class="col-xl col-md-4 col-sm-6">
            {!! Form::label('begindate', 'Fecha desde:', ['class' => 'fw-bold text-dark fs-7 mb-1']) !!}
            {!! Form::date('begindate', date('Y-m-01'), ['class' => 'form-control form-control-sm', 'id' => 'begindateAsistencia']) !!}
        </div>

        <div class="col-xl col-md-4 col-sm-6">
            {!! Form::label('enddate', 'Fecha hasta:', ['class' => 'fw-bold text-dark fs-7 mb-1']) !!}
            {!! Form::date('enddate', date('Y-m-t'), ['class' => 'form-control form-control-sm', 'id' => 'enddateAsistencia']) !!}
        </div>

        <div class="col-xl col-md-4 col-sm-6">
            {!! Form::label('grade', 'Grado:', ['class' => 'fw-bold text-dark fs-7 mb-1']) !!}
            {!! Form::select('grade', ['' => 'Todos', 1 => '1° grado', 2 => '2° grado', 3 => '3° grado', 4 => '4° grado', 5 => '5° grado'], '', ['class' => 'form-select form-select-sm', 'id' => 'gradeAsistencia']) !!}
        </div>

        <div class="col-xl col-md-4 col-sm-6">
            {!! Form::label('section', 'Sección:', ['class' => 'fw-bold text-dark fs-7 mb-1']) !!}
            {!! Form::select('section', ['' => 'Todas', 'A' => 'Sección A', 'B' => 'Sección B'], '', ['class' => 'form-select form-select-sm', 'id' => 'sectionAsistencia']) !!}
        </div>

        <div class="col-xl col-md-4 col-sm-6">
            {!! Form::label('filtro', 'Estado:', ['class' => 'fw-bold text-dark fs-7 mb-1']) !!}
            {!! Form::select('filtro', ['todos' => 'Todos', 'puntual' => '✅ Puntual', 'tardanza' => '⚠️ Tardanza'], 'todos', ['class' => 'form-select form-select-sm', 'id' => 'filtroAsistencia']) !!}
        </div>

        <div class="col-xl-auto col-md-4 col-sm-6">
            {!! Form::label('filas', 'Filas:', ['class' => 'fw-bold text-dark fs-7 mb-1']) !!}
            {!! Form::selectRange('filas', 5, 50, 10, ['class' => 'form-select form-select-sm', 'id' => 'filasAsistencia', 'onchange' => 'buscarAsistencia()']) !!}
        </div>

        <div class="col-xl-auto col-12 d-flex gap-2 align-items-end">
            {!! Form::button('<i class="ki-duotone ki-magnifier fs-5 me-1"><span class="path1"></span><span class="path2"></span></i> Generar reporte',
                ['class' => 'btn btn-primary btn-sm text-nowrap', 'id' => 'btnBuscarAsistencia', 'onclick' => 'buscarAsistencia()']) !!}
        </div>

        {!! Form::close() !!}
    </div>
</div>

{{-- Resultados --}}
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-5 text-dark">Detalle de registros</span>
        </h3>
    </div>
    <div class="card-body py-4">
        <div id="listadoAsistencia">
            <div class="text-center py-10">
                <i class="ki-duotone ki-search-list fs-5x text-gray-300 mb-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <h4 class="text-gray-500">No se encontraron registros</h4>
                <p class="text-muted">Ajusta los filtros y vuelve a intentarlo.</p>
            </div>
        </div>
    </div>
</div>

<script>
function buscarAsistencia() {
    var formData = {
        _token:    '{{ csrf_token() }}',
        page:      $('#pageAsistencia').val(),
        name:      $('#nameAsistencia').val(),
        begindate: $('#begindateAsistencia').val(),
        enddate:   $('#enddateAsistencia').val(),
        filtro:    $('#filtroAsistencia').val(),
        grade:     $('#gradeAsistencia').val(),
        section:   $('#sectionAsistencia').val(),
        filas:     $('#filasAsistencia').val(),
    };

    $('#listadoAsistencia').html(
        '<div class="text-center py-10"><span class="spinner-border text-primary" role="status"></span><p class="mt-3 text-muted">Cargando...</p></div>'
    );

    $.ajax({
        url:  '{{ route("reporte.asistencias.post") }}',
        type: 'POST',
        data: formData,
        success: function(response) {
            $('#listadoAsistencia').html(response);
        },
        error: function(xhr) {
            $('#listadoAsistencia').html(
                '<div class="alert alert-danger">Error al cargar los datos: ' + xhr.statusText + '</div>'
            );
        }
    });
}

$(document).ready(function () {
    buscarAsistencia();

    $('#nameAsistencia').keyup(function(e) {
        if ((window.event ? e.keyCode : e.which) == 13) {
            buscarAsistencia();
        }
    });

    $('#filtroAsistencia, #gradeAsistencia, #sectionAsistencia').on('change', function() {
        buscarAsistencia();
    });
});
</script>
