@if(count($lista) == 0)
<div style="padding: 1rem">
    <h3 class="text-blue">No se encontraron resultados.</h3>
</div>
@else
{!! isset($paginacion) ? $paginacion : '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td>{{ $contador }}</td>
			<td class="text-uppercase fw-semibold">{{ $value->nombre }}</td>
			<td>{{ date('d/m/Y', strtotime($value->dateregister)) }}</td>
			<td>
                <span class="fw-bold">{{ date('H:i', strtotime($value->dateregister)) }}</span>
            </td>
			<td>
                @if(isset($value->estado_asistencia))
                    @if($value->estado_asistencia === 'Puntual')
                        <span class="badge badge-light-success">
                            <i class="fa fa-check me-1"></i> Puntual
                        </span>
                    @elseif($value->estado_asistencia === 'Tardanza')
                        <span class="badge badge-light-warning">
                            <i class="fa fa-clock-o me-1"></i> Tardanza
                        </span>
                    @else
                        <span class="badge badge-light-secondary">{{ $value->estado_asistencia }}</span>
                    @endif
                @endif
            </td>
			<td>{!! Form::button('<div class="fa fa-trash"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</tfoot>
</table>
{!! isset($paginacion) ? $paginacion : '' !!}
@endif
