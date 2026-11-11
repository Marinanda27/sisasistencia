@if(count($lista) == 0)
<div class="text-center py-10">
    <i class="ki-duotone ki-search-list fs-5x text-gray-300 mb-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
    <h4 class="text-gray-500">No se encontraron registros</h4>
    <p class="text-muted">Ajusta los filtros y vuelve a intentarlo.</p>
</div>
@else
{!! isset($paginacion) ? $paginacion : '' !!}
<div class="d-flex align-items-center mb-3">
    <span class="fw-bold text-dark fs-6 me-2">Detalle de registros</span>
    <span class="badge badge-light-primary fs-8">{{ isset($fin) ? $fin : count($lista) }} registros encontrados</span>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                @foreach($cabecera as $col)
                    <th @if((int)$col['numero'] > 1) colspan="{{ $col['numero'] }}" @endif>
                        {!! $col['valor'] !!}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <?php $contador = isset($inicio) ? $inicio + 1 : 1; ?>
            @foreach($lista as $value)
            <tr>
                <td class="text-muted fw-semibold">{{ $contador }}</td>
                <td class="text-uppercase fw-semibold">{{ $value->nombre }}</td>
                <td>
                    @if(!is_null($value->grade))
                        <span class="badge badge-light-info px-3 py-2">{{ $value->grade }}°</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @if(!empty($value->section))
                        <span class="badge badge-light-primary px-3 py-2">{{ $value->section }}</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>{{ date('d/m/Y', strtotime($value->dateregister)) }}</td>
                <td class="fw-bold">{{ date('H:i', strtotime($value->dateregister)) }}</td>
                <td>
                    @if(isset($value->estado_asistencia))
                        @if($value->estado_asistencia === 'Puntual')
                            <span class="badge badge-light-success px-3 py-2">
                                <i class="fa fa-check me-1"></i> Puntual
                            </span>
                        @elseif($value->estado_asistencia === 'Tardanza')
                            <span class="badge badge-light-warning px-3 py-2">
                                <i class="fa fa-clock-o me-1"></i> Tardanza
                            </span>
                        @else
                            <span class="badge badge-light-secondary px-3 py-2">{{ $value->estado_asistencia }}</span>
                        @endif
                    @endif
                </td>
            </tr>
            <?php $contador++; ?>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold bg-light">
                <td colspan="6" class="text-end">Total registros:</td>
                <td><span class="badge badge-light-primary">{{ count($lista) }}</span></td>
            </tr>
        </tfoot>
    </table>
</div>
{!! isset($paginacion) ? $paginacion : '' !!}
@endif
