<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <!-- Total Personas -->
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush h-xl-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column justify-content-between pt-5 pb-5">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <div class="symbol-label bg-light-primary">
                            <i class="ki-duotone ki-profile-user text-primary fs-2x"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                    </div>
                    <span class="fw-bold text-gray-800 fs-5">Total Registrados</span>
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <span class="fs-1 fw-bold text-dark">{{ $totalPersonas }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Asistencias de hoy -->
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush h-xl-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column justify-content-between pt-5 pb-5">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <div class="symbol-label bg-light-success">
                            <i class="ki-duotone ki-check-circle text-success fs-2x"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <span class="fw-bold text-gray-800 fs-5">Asistencias (Hoy)</span>
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <span class="fs-1 fw-bold text-dark">{{ $asistenciasHoy }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Ausentes Hoy -->
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush h-xl-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column justify-content-between pt-5 pb-5">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <div class="symbol-label bg-light-danger">
                            <i class="ki-duotone ki-cross-circle text-danger fs-2x"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <span class="fw-bold text-gray-800 fs-5">Ausentes (Hoy)</span>
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <span class="fs-1 fw-bold text-dark">{{ $ausentesHoy }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Registros Nuevos -->
    <div class="col-sm-6 col-xl-3">
        <div class="card card-flush h-xl-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column justify-content-between pt-5 pb-5">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <div class="symbol-label bg-light-info">
                            <i class="ki-duotone ki-user-tick text-info fs-2x"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </div>
                    </div>
                    <span class="fw-bold text-gray-800 fs-5">Nuevos (Esta Sem.)</span>
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <span class="fs-1 fw-bold text-dark">{{ $registrosNuevosSemana }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fila de la Gráfica -->
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-12">
        <div class="card card-flush shadow-sm border-0">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark fs-4">Asistencias - Últimos 7 Días</span>
                </h3>
            </div>
            <div class="card-body">
                <div id="chart_asistencias_7_dias" style="height: 350px"></div>
            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(function() {
        if (typeof ApexCharts !== 'undefined') {
            var options = {
                series: [{
                    name: 'Asistencias',
                    data: @json($datos7Dias)
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit'
                },
                dataLabels: {
                    enabled: true,
                    background: {
                        enabled: true,
                        foreColor: '#fff',
                        borderRadius: 2,
                        padding: 4,
                        opacity: 0.9,
                        borderWidth: 0
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: @json($labels7Dias),
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#A1A5B7',
                            fontSize: '12px'
                        }
                    },
                    crosshairs: {
                        position: 'front',
                        stroke: {
                            color: '#009EF7',
                            width: 1,
                            dashArray: 3
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#A1A5B7',
                            fontSize: '12px'
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0,
                        stops: [0, 90, 100]
                    }
                },
                colors: ['#009EF7'], // Primary color
                markers: {
                    size: 5,
                    colors: '#ffffff',
                    strokeColors: '#009EF7',
                    strokeWidth: 3,
                    strokeOpacity: 0.9,
                    strokeDashArray: 0,
                    fillOpacity: 1,
                    discrete: [],
                    shape: "circle",
                    radius: 2,
                    offsetX: 0,
                    offsetY: 0,
                    onClick: undefined,
                    onDblClick: undefined,
                    showNullDataPoints: true,
                    hover: {
                        size: 8,
                        sizeOffset: 3
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart_asistencias_7_dias"), options);
            chart.render();
        } else {
            console.error('ApexCharts library is not loaded');
            document.querySelector("#chart_asistencias_7_dias").innerHTML = '<div class="alert alert-warning">La librería de gráficas no está disponible.</div>';
        }
    }, 500); // Pequeño retraso para asegurar que el DOM cargado por AJAX esté listo
</script>