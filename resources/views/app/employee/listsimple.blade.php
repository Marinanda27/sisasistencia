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
                <td>{!! Form::hidden('txtEmployee'.$contador, $value->id, array('id' => 'txtEmployee'.$contador)) !!}{{ $contador }}</td>
                <td class="text-uppercase">{{ $value->employeename }}</td>
                <td>{{ $value->dni}}</td>
                <td class="text-uppercase">{{ $value->address }}</td>
                <td>{{ $value->cellnumber }}</td>
                <td>{{ $value->job }}</td>
                <td>{!! Form::button('<div class="fa fa-edit"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			    <td>{!! Form::hidden('txtNombre'.$contador, $value->employeename, array('id' => 'txtNombre'.$contador)) !!}{!! Form::button('<i class="fa fa-plus"></i> Agregar', array('onclick' => 'addemployee(\''.$contador.'\')', 'class' => 'btn btn-xs btn-primary')) !!}</td>
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
