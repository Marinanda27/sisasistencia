@if(count($lista) == 0)
<div style="padding: 1rem">
    <h3 class="text-primary">No se encontraron resultados.</h3>
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
			<td>{{ $value->name }}</td>
            <td>{!! Form::button('<div class="fa fa-list"></div> Permisos', array('onclick' => 'modal(\''.URL::route($ruta["permisos"], array('SI', $value->id)).'\', \'Permisos: '.$value->name.'\', this);', 'class' => 'btn btn-dark btn-xs')) !!}</td>
			<td>{!! Form::button('<div class="fa fa-edit"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
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
