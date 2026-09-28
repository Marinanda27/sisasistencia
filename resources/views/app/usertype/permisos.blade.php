
<style>
   .form-group {
      margin-bottom: 15px;
   }

   label{
      display: inline-block;
      max-width: 100%;
      margin-bottom: 5px;
      font-weight: 700;
   }

   .pull-right {
      float: right;
   }

   .permiso-label:hover  {
		color: blue;
		background-color: #e2e2e2;
        cursor: pointer;
	}

</style>

<?php
use App\Models\Menuoptioncategory;
use App\Models\Usertype;
use App\Models\Menuoption;

$user = Auth::user();
if ($user->id == 1) {
    $categoriasPadre = Menuoptioncategory::whereNull('menuoptioncategory_id')->orderBy('order', 'ASC')->get();
} else {
    $categoriasPadre = Menuoptioncategory::whereNull('menuoptioncategory_id')->orderBy('order', 'ASC')->get();
}

$asignados       = array();
$opciones        = Usertype::find($tipousuario->id)->menuoptions;
foreach ($opciones as $key => $value) {
	$asignados[] = $value->id;
}

function generarArbol($idcategoria, $nivel, $asignados){
	$sangria = '';
	for ($i=0; $i < pow(2, $nivel); $i++) {
		//$sangria .= '&nbsp;';
	}

    $user = Auth::user();
    if ($user->id == 1) {
        $categorias = Menuoptioncategory::where('menuoptioncategory_id', '=', $idcategoria)->orderBy('order', 'ASC')->get();
    } else {
        $categorias = Menuoptioncategory::where('menuoptioncategory_id', '=', $idcategoria)->orderBy('order', 'ASC')->get();
        //$categorias = Menuoptioncategory::where('menuoptioncategory_id', '=', $idcategoria)->where('restricted', '!=', 'Y')->orderBy('order', 'ASC')->get();
    }
	//$categorias = Menuoptioncategory::where('menuoptioncategory_id', '=', $idcategoria)->orderBy('order', 'ASC')->get();

    $opcionmenus = Menuoption::where('menuoptioncategory_id', '=', $idcategoria)->orderBy('order', 'ASC')->get();
?>
	<div class="">
<?php

	foreach ($opcionmenus as $key => $opcionmenu) {
		if (in_array($opcionmenu->id, $asignados)) {
			?>
			{!! $sangria !!}
			<div class="permiso-label">
			<?php
			if(strtoupper($opcionmenu->name) === 'SEPARADOR') {
			?>

				{!! Form::label('condicion'.$opcionmenu->id, '<< SEPARADOR >>', ['class' => 'permiso-label']) !!}
			<?php
			}else{
			?>

				{!! Form::label('condicion'.$opcionmenu->id, $opcionmenu->name, ['class' => 'permiso-label']) !!}

			<?php
			}
			?>
			{!! Form::checkbox('condicion[]', '', true, array('id' => 'condicion'.$opcionmenu->id,'class' => 'pull-right', 'onchange' => 'cambiarEstado(this, \''.'estado'.$opcionmenu->id.'\');')) !!}
			{!! Form::hidden('estado[]', 'H', array('id' => 'estado'.$opcionmenu->id)) !!}
			{!! Form::hidden('idopcionmenu[]', $opcionmenu->id, array('id' => 'idopcionmenu'.$opcionmenu->id)) !!}
			{!! '<br>' !!}
		</div>
		<?php
		}else{
		?>
			{!! $sangria !!}
			<div class="permiso-label">
		<?php
			if(strtoupper($opcionmenu->name) === 'SEPARADOR') {
				?>
				{!! Form::label('condicion'.$opcionmenu->id, '<< SEPARADOR >>', ['class' => 'permiso-label']) !!}
			<?php
			}else{
				?>
				{!! Form::label('condicion'.$opcionmenu->id, $opcionmenu->name, ['class' => 'permiso-label']) !!}
			<?php
			}
			?>
				{!! Form::checkbox('condicion[]', '', false, array('id' => 'condicion'.$opcionmenu->id,'class' => 'pull-right', 'onchange' => 'cambiarEstado(this, \''.'estado'.$opcionmenu->id.'\');')) !!}
				{!! Form::hidden('estado[]', 'I', array('id' => 'estado'.$opcionmenu->id)) !!}
				{!! Form::hidden('idopcionmenu[]', $opcionmenu->id, array('id' => 'idopcionmenu'.$opcionmenu->id)) !!}
				{!! '<br>' !!}
			</div>
			<?php
		}
	}

	foreach($categorias as $key => $categoria) {
	?>
	{!! $sangria !!}
		{!! "<b><u><span class='text-info'>".$categoria->name."</span></u></b>" !!}
		{!! '<br>' !!}
		<?php generarArbol($categoria->id, $nivel+1, $asignados); ?>
	<?php
	}
?>
<?php } ?>
{!! Form::open(array('route' => array('usertype.guardarpermisos', $tipousuario->id),'method' => 'POST', 'id' => 'formMantenimiento'.$entidad)) !!}
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="form-group table-hover">
		@foreach($categoriasPadre as $key => $categoria)
			{!! '<b><u><span class=\'text-info\'>'.$categoria->name.'<span></u></b><br>' !!}
			<?php generarArbol($categoria->id, 2, $asignados); ?>
		@endforeach
	</div>
	<div class="form-group text-center">
		{!! Form::button('Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
{!! Form::close() !!}

<script type="text/javascript">
$(document).ready(function() {
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
});
function cambiarEstado (elemento, id) {

	if (elemento.checked) {
		$('#'+id).val('H');
	} else{
		$('#'+id).val('I');
	};
}
</script>
