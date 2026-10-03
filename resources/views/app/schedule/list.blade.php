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
		<?php $contador = $inicio + 1; ?>
		@foreach ($lista as $key => $value)
		<tr>
			<td>{{ $contador }}</td>
			<td>
                <span class="badge badge-light-primary fs-6 fw-bold">
                    <i class="fa fa-clock-o me-1"></i>
                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $value->start_time)->format('H:i') }}
                </span>
            </td>
			<td>
                <span class="badge badge-light-warning fs-6 fw-bold">
                    {{ $value->grace_minutes }} min.
                </span>
            </td>
			<td>{{ date('d/m/Y H:i', strtotime($value->created_at)) }}</td>
			<td>{!! Form::button('<div class="fa fa-edit"></div> Editar', ['onclick' => 'modal (\''.URL::route($ruta["edit"], [$value->id, 'listar'=>'SI']).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning']) !!}</td>
			<td>{!! Form::button('<div class="fa fa-trash"></div> Eliminar', ['onclick' => 'modal (\''.URL::route($ruta["delete"], [$value->id, 'SI']).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger']) !!}</td>
		</tr>
		<?php $contador = $contador + 1; ?>
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
