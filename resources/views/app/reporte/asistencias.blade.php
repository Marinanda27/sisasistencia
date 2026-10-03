<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0 pb-5 pt-5">
    <i class="ki-duotone ki-chart-line-star text-primary fs-2hx me-3"><span class="path1"></span><span class="path2"></span></i>
    Reporte de Asistencias y Tardanzas
</h1>

{{-- Información del horario --}}
@if($horario)
<div class="alert alert-primary d-flex align-items-center mb-5 py-3">
    <i class="ki-duotone ki-time fs-2hx text-primary me-3"><span class="path1"></span><span class="path2"></span></i>
    <div>
        <span class="fw-bold">Horario configurado:</span>
        Entrada a las <strong>{{ \Carbon\Carbon::createFromFormat('H:i:s', $horario->start_time)->format('H:i') }}</strong>
        con <strong>{{ $horario->grace_minutes }} min.</strong> de tolerancia.
        Hora límite puntual: <strong>{{ substr($horaLimite, 0, 5) }}</strong>
    </div>
</div>
@else
<div class="alert alert-warning d-flex align-items-center mb-5">
    <i class="ki-duotone ki-information-5 fs-2hx text-warning me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
    <div>
        <strong>Sin horario configurado.</strong>
        No se podrá calcular tardanzas. <a href="{{ route('schedule.index') }}" class="fw-bold">Configurar ahora</a>
    </div>
</div>
@endif

{{-- Filtros --}}
<div class="card mb-6 shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-5 text-dark">Filtros del reporte</span>
        </h3>
    </div>
    <div class="card-body py-4">
        <form method="GET" action="{{ route('reporte.asistencias') }}" class="row gx-4 gy-3 align-items-end">
            <div class="col-md-3 col-sm-6">
                <label class="fw-bold text-dark fs-7 mb-1">Nombre:</label>
                <input type="text" name="name" value="{{ $name }}" class="form-control form-control-sm" placeholder="Buscar por nombre...">
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="fw-bold text-dark fs-7 mb-1">Fecha desde:</label>
                <input type="date" name="begindate" value="{{ $begindate }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="fw-bold text-dark fs-7 mb-1">Fecha hasta:</label>
                <input type="date" name="enddate" value="{{ $enddate }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="fw-bold text-dark fs-7 mb-1">Estado:</label>
                <select name="filtro" class="form-select form-select-sm">
                    <option value="todos"    {{ $filtro == 'todos'    ? 'selected' : '' }}>Todos</option>
                    <option value="puntual"  {{ $filtro == 'puntual'  ? 'selected' : '' }}>✅ Puntual</option>
                    <option value="tardanza" {{ $filtro == 'tardanza' ? 'selected' : '' }}>⚠️ Tardanza</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="ki-duotone ki-magnifier fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                    Generar reporte
                </button>
                <button type="button" class="btn btn-light-success btn-sm" onclick="window.print()">
                    <i class="fa fa-print me-1"></i> Imprimir
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Resumen del período --}}
<div class="row g-4 mb-6">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center gap-4 py-5">
                <div class="symbol symbol-50px flex-shrink-0">
                    <div class="symbol-label bg-light-primary">
                        <i class="ki-duotone ki-document text-primary fs-2x"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div>
                    <div class="fs-1 fw-bolder text-dark">{{ $resumen['total_registros'] }}</div>
                    <div class="fw-semibold text-gray-500 fs-7">Total registros</div>
                    <span class="badge badge-light-primary fs-8">{{ $resumen['dias_con_registro'] }} días</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush shadow-sm border-0 h-100" style="border-left: 4px solid #50cd89 !important;">
            <div class="card-body d-flex align-items-center gap-4 py-5">
                <div class="symbol symbol-50px flex-shrink-0">
                    <div class="symbol-label bg-light-success">
                        <i class="ki-duotone ki-check-circle text-success fs-2x"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div>
                    <div class="fs-1 fw-bolder text-success">{{ $resumen['puntuales'] }}</div>
                    <div class="fw-semibold text-gray-500 fs-7">Puntuales</div>
                    @if($resumen['total_registros'] > 0)
                    <span class="badge badge-light-success fs-8">{{ number_format(($resumen['puntuales']/$resumen['total_registros'])*100, 1) }}%</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush shadow-sm border-0 h-100" style="border-left: 4px solid #ffc700 !important;">
            <div class="card-body d-flex align-items-center gap-4 py-5">
                <div class="symbol symbol-50px flex-shrink-0">
                    <div class="symbol-label bg-light-warning">
                        <i class="ki-duotone ki-time text-warning fs-2x"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div>
                    <div class="fs-1 fw-bolder text-warning">{{ $resumen['tardanzas'] }}</div>
                    <div class="fw-semibold text-gray-500 fs-7">Tardanzas</div>
                    @if($resumen['total_registros'] > 0)
                    <span class="badge badge-light-warning fs-8">{{ number_format(($resumen['tardanzas']/$resumen['total_registros'])*100, 1) }}%</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center gap-4 py-5">
                <div class="symbol symbol-50px flex-shrink-0">
                    <div class="symbol-label bg-light-info">
                        <i class="ki-duotone ki-profile-user text-info fs-2x"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    </div>
                </div>
                <div>
                    <div class="fs-1 fw-bolder text-info">{{ $resumen['total_personas'] }}</div>
                    <div class="fw-semibold text-gray-500 fs-7">Total personas</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Gráfica por día --}}
@if(count($datosPorDia) > 0)
<div class="card shadow-sm border-0 mb-6">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-5 text-dark">Puntualidad vs Tardanzas por día</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Del {{ date('d/m/Y', strtotime($begindate)) }} al {{ date('d/m/Y', strtotime($enddate)) }}</span>
        </h3>
    </div>
    <div class="card-body">
        <div id="chart_reporte" style="height: 300px;"></div>
    </div>
</div>
@endif

{{-- Tabla detallada --}}
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pt-5 d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <span class="card-label fw-bold fs-5 text-dark">Detalle de registros</span>
            <span class="text-muted mt-1 fw-semibold fs-7 ms-2">{{ count($lista) }} registros encontrados</span>
        </h3>
    </div>
    <div class="card-body py-4">
        @if(count($lista) == 0)
        <div class="text-center py-10">
            <i class="ki-duotone ki-search-list fs-5x text-gray-300 mb-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
            <h4 class="text-gray-500">No se encontraron registros</h4>
            <p class="text-muted">Ajusta los filtros y vuelve a intentarlo.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Hora de llegada</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lista as $i => $row)
                    <tr>
                        <td class="text-muted fw-semibold">{{ $i + 1 }}</td>
                        <td class="text-uppercase fw-semibold">{{ $row->nombre }}</td>
                        <td>{{ date('d/m/Y', strtotime($row->fecha)) }}</td>
                        <td class="fw-bold">{{ substr($row->hora, 0, 5) }}</td>
                        <td>
                            @if($row->estado === 'Puntual')
                                <span class="badge badge-light-success px-3 py-2">
                                    <i class="fa fa-check me-1"></i> Puntual
                                </span>
                            @elseif($row->estado === 'Tardanza')
                                <span class="badge badge-light-warning px-3 py-2">
                                    <i class="fa fa-clock-o me-1"></i> Tardanza
                                </span>
                            @else
                                <span class="badge badge-light-secondary px-3 py-2">{{ $row->estado }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="4" class="text-end">Total registros:</td>
                        <td><span class="badge badge-light-primary">{{ count($lista) }}</span></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>

@if(count($datosPorDia) > 0)
<script>
setTimeout(function() {
    if (typeof ApexCharts !== 'undefined') {
        var labels   = @json(array_map(fn($d) => date('d/m', strtotime($d->fecha)), $datosPorDia));
        var puntuales = @json(array_map(fn($d) => (int)$d->puntuales, $datosPorDia));
        var tardanzas = @json(array_map(fn($d) => (int)$d->tardanzas, $datosPorDia));

        var options = {
            series: [
                { name: 'Puntuales', data: puntuales },
                { name: 'Tardanzas', data: tardanzas }
            ],
            chart: { type: 'bar', height: 300, toolbar: { show: false }, stacked: false },
            colors: ['#50cd89', '#ffc700'],
            plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: labels,
                labels: { style: { colors: '#A1A5B7', fontSize: '12px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { labels: { style: { colors: '#A1A5B7' } } },
            legend: { position: 'top' },
            grid: { borderColor: '#f0f0f0' }
        };
        var chart = new ApexCharts(document.querySelector('#chart_reporte'), options);
        chart.render();
    }
}, 500);
</script>
@endif

<style>
@media print {
    .card-header form, button, .btn { display: none !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; }
}
</style>
