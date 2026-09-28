<?php

namespace App\Librerias;

use App\Models\Menuoption;
use DateTime;
use Validator;

use Illuminate\Support\Facades\DB;

/**
* Libreria de clases
*/
class Libreria
{

	public function generarPaginacion($lista, $pagina, $filas, $entidad){
		$cantidadTotal = count($lista);
		if ($filas > $cantidadTotal) {
			$filas = $cantidadTotal;
		}
		$cantidad = $cantidadTotal * 1.0;
		$division = $cantidad / $filas;
		$div = ceil($division);
		if ($pagina > $div) {
			$pagina = (int) $div;
		}

		$inicio = ($pagina - 1) * $filas;
		$fin    = ($pagina * $filas);
		if ($fin > $cantidadTotal) {
			$fin = $cantidadTotal;
		}

		$cadenaPagina  = "";
		$puntosDelante = "";
		$puntosDetras  = "";
		$cadenaPagina .= "<ul class=\"pagination pagination-sm\">";
		$cadenaPagina .= "<li class=\"active\"><a href=\"#\">TOTAL DE REGISTROS " . $cantidadTotal . "</a></li>";

		for ($i=1; $i <= $div ; $i++) {
			if ($i == 1) {
				if ($i == $pagina) {
					$cadenaPagina .= "<li class=\"active\"><a>" . $i . "</a></li>";
				} else {
					$cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ", '', '".$entidad."')\">" . $i . "</a></li>";
				}
			}
			if ($i == $div && $i != 1) {
				if ($i == $pagina) {
					$cadenaPagina .= "<li class=\"active\"><a>" . $i . "</a></li>";
				} else {
					$cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
				}
			}
			if ($i != 1 && $i != $div) {
				if ($i == $pagina) {
					$cadenaPagina .= "<li class=\"active\"><a>" . $i . "</a></li>";
				} else {
					if ($i == ($pagina - 1) || $i == ($pagina - 2)) {
						$cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
					}
					if ($i == ($pagina + 1) || $i == ($pagina + 2)) {
						$cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
					}
				}
			}
			if ($i > 1 && $i < ($pagina - 2)) {
				if ($puntosDelante == '') {
					$puntosDelante =  "<li class=\"disabled\"><a href=\"#\">...</a></li>";
					$cadenaPagina .= $puntosDelante;
				}
			}
			if ($i < $div && $i > ($pagina + 2)) {
				if ($puntosDetras == '') {
					$puntosDetras = "<li class=\"disabled\"><a href=\"#\">...</a></li>";
					$cadenaPagina .= $puntosDetras;
				}
			}
		}
		$cadenaPagina .= "</ul>";
		$paginacion = array(
			'cadenapaginacion' => $cadenaPagina,
			'inicio'           => $inicio,
			'fin'              => $fin,
			'nuevapagina'      => $pagina,
			);
		return $paginacion;
	}

	public function generarPaginacion3($cantidad, $pagina, $filas, $entidad){
		$cantidadTotal = $cantidad;
		if ($filas > $cantidadTotal) {
			$filas = $cantidadTotal;
		}
		$cantidad = $cantidadTotal * 1.0;
		$division = $cantidad / $filas;
		$div = ceil($division);
		if ($pagina > $div) {
			$pagina = (int) $div;
		}

		$inicio = ($pagina - 1) * $filas;
		$fin    = ($pagina * $filas);
		if ($fin > $cantidadTotal) {
			$fin = $cantidadTotal;
		}

		$cadenaPagina  = "";
		$puntosDelante = "";
		$puntosDetras  = "";
		$cadenaPagina .= "<ul class=\"pagination pagination-sm\">";
		$cadenaPagina .= "<li class=\"active\"><a href=\"#\">TOTAL DE REGISTROS " . $cantidadTotal . "</a></li>";

		for ($i=1; $i <= $div ; $i++) {
			if ($i == 1) {
				if ($i == $pagina) {
					$cadenaPagina .= "<li class=\"active\"><a>" . $i . "</a></li>";
				} else {
					$cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ", '', '".$entidad."')\">" . $i . "</a></li>";
				}
			}
			if ($i == $div && $i != 1) {
				if ($i == $pagina) {
					$cadenaPagina .= "<li class=\"active\"><a>" . $i . "</a></li>";
				} else {
					$cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
				}
			}
			if ($i != 1 && $i != $div) {
				if ($i == $pagina) {
					$cadenaPagina .= "<li class=\"active\"><a>" . $i . "</a></li>";
				} else {
					if ($i == ($pagina - 1) || $i == ($pagina - 2)) {
						$cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
					}
					if ($i == ($pagina + 1) || $i == ($pagina + 2)) {
						$cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
					}
				}
			}
			if ($i > 1 && $i < ($pagina - 2)) {
				if ($puntosDelante == '') {
					$puntosDelante =  "<li class=\"disabled\"><a href=\"#\">...</a></li>";
					$cadenaPagina .= $puntosDelante;
				}
			}
			if ($i < $div && $i > ($pagina + 2)) {
				if ($puntosDetras == '') {
					$puntosDetras = "<li class=\"disabled\"><a href=\"#\">...</a></li>";
					$cadenaPagina .= $puntosDetras;
				}
			}
		}
		$cadenaPagina .= "</ul>";
		$paginacion = array(
			'cadenapaginacion' => $cadenaPagina,
			'inicio'           => $inicio,
			'fin'              => $fin,
			'nuevapagina'      => $pagina,
			);
		return $paginacion;
	}

   public function generarPaginacion2($cantidad, $pagina, $filas, $entidad){
      $cantidadTotal = $cantidad;
		if ($filas > $cantidadTotal) {
			$filas = $cantidadTotal;
		}

      $cantidad = $cantidadTotal * 1.0;
		$division = $cantidad / $filas;
		$div = ceil($division);
		if ($pagina > $div) {
			$pagina = (int) $div;
		}

      $inicio = ($pagina - 1) * $filas;
		$fin    = ($pagina * $filas);
		if ($fin > $cantidadTotal) {
			$fin = $cantidadTotal;
		}
      $cadenaPagina  = "";
		$puntosDelante = "";
		$puntosDetras  = "";
      $cadenaPagina  = '<div class="d-flex align-items-center justify-content-between gap-3 flex-wrap my-3">';

    // 🔵 TOTAL DE REGISTROS (pill)
    // $cadenaPagina .= '
    //     <span class="badge badge-primary fs-6 px-6 py-3">
    //         TOTAL DE REGISTROS '.$cantidadTotal.'
    //     </span>
    // ';
    $cadenaPagina .= '
        <span class="text-dark fs-7">
            Mostrando ' . ($inicio + 1) . ' al ' . $fin . ' de ' . $cantidadTotal . ' registros
        </span>
    ';

    // 🔢 PAGINACIÓN
    $cadenaPagina .= '<ul class="pagination mb-0 gap-1">';

// 🔵 FLECHA ANTERIOR
if ($pagina > 1) {
    $cadenaPagina .= '
        <li class="page-item">
            <a class="page-link rounded px-3" href="javascript:void(0);"
               onclick="buscarCompaginado('.($pagina - 1).', \'\', \''.$entidad.'\')"
               aria-label="Anterior">
                <i class="ki-duotone ki-arrow-left fs-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </a>
        </li>';
} else {
    $cadenaPagina .= '
        <li class="page-item disabled">
            <span class="page-link rounded px-3">
                <i class="ki-duotone ki-arrow-left fs-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </span>
        </li>';
}

// 🔢 BOTONES DE PÁGINA (tu lógica existente del for)
for ($i = 1; $i <= $div; $i++) {
    if ($i == 1) {
        if ($i == $pagina) {
            $cadenaPagina .= '<li class="page-item active"><span class="page-link rounded">'.$i.'</span></li>';
        } else {
            $cadenaPagina .= '<li class="page-item"><a class="page-link rounded" href="javascript:void(0);" onclick="buscarCompaginado('.$i.', \'\', \''.$entidad.'\')">'.$i.'</a></li>';
        }
    }

    if ($i == $div && $i != 1) {
        if ($i == $pagina) {
            $cadenaPagina .= '<li class="page-item active"><span class="page-link rounded">'.$i.'</span></li>';
        } else {
            $cadenaPagina .= '<li class="page-item"><a class="page-link rounded" href="javascript:void(0);" onclick="buscarCompaginado('.$i.', \'\', \''.$entidad.'\')">'.$i.'</a></li>';
        }
    }

    if ($i != 1 && $i != $div) {
        if ($i == $pagina) {
            $cadenaPagina .= '<li class="page-item active"><span class="page-link rounded">'.$i.'</span></li>';
        } else {
            if ($i == ($pagina - 1) || $i == ($pagina - 2)) {
                $cadenaPagina .= '<li class="page-item"><a class="page-link rounded" href="javascript:void(0);" onclick="buscarCompaginado('.$i.', \'\', \''.$entidad.'\')">'.$i.'</a></li>';
            }
            if ($i == ($pagina + 1) || $i == ($pagina + 2)) {
                $cadenaPagina .= '<li class="page-item"><a class="page-link rounded" href="javascript:void(0);" onclick="buscarCompaginado('.$i.', \'\', \''.$entidad.'\')">'.$i.'</a></li>';
            }
        }
    }

    if ($i > 1 && $i < ($pagina - 2)) {
        if ($puntosDelante == '') {
            $puntosDelante = '<li class="page-item disabled"><span class="page-link rounded">...</span></li>';
            $cadenaPagina .= $puntosDelante;
        }
    }

    if ($i < $div && $i > ($pagina + 2)) {
        if ($puntosDetras == '') {
            $puntosDetras = '<li class="page-item disabled"><span class="page-link rounded">...</span></li>';
            $cadenaPagina .= $puntosDetras;
        }
    }
}

// 🔵 FLECHA SIGUIENTE
if ($pagina < $div) {
    $cadenaPagina .= '
        <li class="page-item">
            <a class="page-link rounded px-3" href="javascript:void(0);"
               onclick="buscarCompaginado('.($pagina + 1).', \'\', \''.$entidad.'\')"
               aria-label="Siguiente">
                <i class="ki-duotone ki-arrow-right fs-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </a>
        </li>';
} else {
    $cadenaPagina .= '
        <li class="page-item disabled">
            <span class="page-link rounded px-3">
                <i class="ki-duotone ki-arrow-right fs-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </span>
        </li>';
}

$cadenaPagina .= '</ul></div>';

   $paginacion = array(
      'cadenapaginacion' => $cadenaPagina,
      'inicio'           => $inicio,
      'fin'              => $fin,
      'nuevapagina'      => $pagina,
   );
      return $paginacion;
   }


	public function generarTitulo($ur2l)
	{
		$url          = substr($_SERVER['REQUEST_URI'], 1);
		$separados    = explode('/', $url);
		unset($separados[0]);
		$url          = implode('/', $separados);
		$opcionmenu   = Menuoption::where('link', '=', $url)->first();

		if (is_null($opcionmenu)) {
			$separados = explode('/', $url);
			unset($separados[0]);
			$url       = implode('/', $separados);
			$opcionmenu   = Menuoption::where('link', '=', $url)->first();
		}

		$titulo       = $opcionmenu->nombre;
		$primeraletra = mb_strtoupper(mb_substr($titulo, 0, 1));
		$restotexto   = mb_strtolower(mb_substr($titulo, 1, strlen($titulo)));
		$titulo       = $primeraletra.$restotexto;

		$padre        = $opcionmenu->menuoptioncategory;
		$nombrepadre  = $padre->nombre;
		$primeraletra = mb_strtoupper(mb_substr($nombrepadre, 0, 1));
		$restotexto   = mb_strtolower(mb_substr($nombrepadre, 1, strlen($nombrepadre)));
		$nombrepadre  = $primeraletra.$restotexto;
		$titulo       = $nombrepadre.' / '.$titulo;

		while ($padre->categoriaPadre !== null) {
			$padre        = $padre->categoriaPadre;
			$nombrepadre  = $padre->nombre;
			$primeraletra = mb_strtoupper(mb_substr($nombrepadre, 0, 1));
			$restotexto   = mb_strtolower(mb_substr($nombrepadre, 1, strlen($nombrepadre)));
			$nombrepadre  = $primeraletra.$restotexto;
			$titulo       = $nombrepadre.' / '.$titulo;
		}
		return $_SERVER['REQUEST_URI'];
	}

	static function generateBinnacle($binnacleaction_id,$user_id,$usertype_id,$binnacle_id,$commentary)
	{
		$list = DB::connection(session('base'))->select("SELECT hourstoapproval,`level`,usertype_id,usertypesupervisor_id FROM binnacleactionsupervision WHERE (deleted_at IS NULL) AND binnacleaction_id = :binnacleaction_id ORDER BY `level` ASC",["binnacleaction_id"=>$binnacleaction_id]);
		$ind = 0;
		foreach ($list as $key => $value) {
			if ($value->usertype_id == $usertype_id) {
				$binnaclesupervisor = new Binnaclesupervisor();
				$binnaclesupervisor->hourstoapproval = $value->hourstoapproval;
				$binnaclesupervisor->level = $value->level;
				$binnaclesupervisor->commentary = $commentary;
				$binnaclesupervisor->status = 'P';
				$binnaclesupervisor->binnacle_id = $binnacle_id;
				$binnaclesupervisor->usertype_id = $usertype_id;
				$binnaclesupervisor->user_id = $user_id;
				$binnaclesupervisor->save();
				$ind = 1;
				break;
			}
		}

		if ($ind == 0) {
			$objSupervisor = DB::connection(session('base'))->selectOne("SELECT id FROM binnaclesupervisor WHERE (deleted_at IS NULL) AND (level = 1) AND (binnacle_id = :binnacle_id) LIMIT 1",['binnacle_id'=>$binnacle_id]);
			if ($objSupervisor === null) {
				$binnacle = Binnacle::find($binnacle_id);
				$binnacle->status = 'A';
				$binnacle->save();
			}
		}
	}

	static function formatearFecha($fecha)
	{
		$date            = DateTime::createFromFormat('Y-m-d', $fecha);
		$fechanacimiento = $date->format('d/m/Y');
		return $fechanacimiento;
	}

	static function mismodiames($fecha, $meses){
		$datos     = explode('-', $fecha);
		$dia       = $datos[2];
		$mes       = $datos[1];
		$anio      = $datos[0];

		$nuevodia  = $dia;
		$nuevomes  = (int)$mes + $meses;
		$nuevoanio = $anio;

		if($nuevomes > 12 ){
			if ($nuevomes > 12 && $nuevomes <= 24) {
				$nuevoanio = (int)$anio + 1;
			}
			if ($nuevomes > 24 && $nuevomes <= 36) {
				$nuevoanio = (int)$anio + 2;
			}
			if ($nuevomes > 36 && $nuevomes <= 48) {
				$nuevoanio = (int)$anio + 3;
			}
			if ($nuevomes > 48 && $nuevomes <= 60) {
				$nuevoanio = (int)$anio + 4;
			}
			if ($nuevomes > 60 && $nuevomes <= 72) {
				$nuevoanio = (int)$anio + 5;
			}
			if ($nuevomes > 72 && $nuevomes <= 84) {
				$nuevoanio = (int)$anio + 6;
			}
			if ($nuevomes > 84 && $nuevomes <= 96) {
				$nuevoanio = (int)$anio + 7;
			}
			if ($nuevomes > 96 && $nuevomes <= 108) {
				$nuevoanio = (int)$anio + 8;
			}
			if ($nuevomes > 108 && $nuevomes <= 120) {
				$nuevoanio = (int)$anio + 9;
			}
			if ($nuevomes > 120 && $nuevomes <= 132) {
				$nuevoanio = (int)$anio + 10;
			}
			if ($nuevomes > 132 && $nuevomes <= 144) {
				$nuevoanio = (int)$anio + 11;
			}
			if ($nuevomes > 144 && $nuevomes <= 156) {
				$nuevoanio = (int)$anio + 12;
			}
			if ($nuevomes > 156 && $nuevomes <= 168) {
				$nuevoanio = (int)$anio + 13;
			}
			if ($nuevomes > 168 && $nuevomes <= 180) {
				$nuevoanio = (int)$anio + 14;
			}
			$nuevomes  = fmod($nuevomes, 12);
			if ($nuevomes == 0) {
				$nuevomes = 12;
			}
		}

		$nuevomes = (string)$nuevomes;
		$nuevomes = (strlen($nuevomes) === 2) ? $nuevomes : '0'.$nuevomes ;


		$nuevodia   = (string)$nuevodia;
		$nuevodia   = (strlen($nuevodia) === 2) ? $nuevodia : '0'.$nuevodia ;
		$nuevafecha = implode('-', array($nuevoanio, $nuevomes, $nuevodia));
		if(self::validateDate($nuevafecha, 'Y-m-d') === true){
			return $nuevafecha;
		}

		$nuevodia   = (string)($nuevodia - 1);
		$nuevodia   = (strlen($nuevodia) === 2) ? $nuevodia : '0'.$nuevodia ;
		$nuevafecha = implode('-', array($nuevoanio, $nuevomes, $nuevodia));
		if(self::validateDate($nuevafecha, 'Y-m-d') === true){
			return $nuevafecha;
		}

		$nuevodia   = (string)($nuevodia - 2);
		$nuevodia   = (strlen($nuevodia) === 2) ? $nuevodia : '0'.$nuevodia ;
		$nuevafecha = implode('-', array($nuevoanio, $nuevomes, $nuevodia));
		if(self::validateDate($nuevafecha, 'Y-m-d') === true){
			return $nuevafecha;
		}

		$nuevodia   = (string)($nuevodia - 3);
		$nuevodia   = (strlen($nuevodia) === 2) ? $nuevodia : '0'.$nuevodia ;
		$nuevafecha = implode('-', array($nuevoanio, $nuevomes, $nuevodia));
		if(self::validateDate($nuevafecha, 'Y-m-d') === true){
			return $nuevafecha;
		}
	}
	static function get_client_ip() {
		$ip = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'])
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'])
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED'])
			$ip = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR'])
			$ip = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED'])
			$ip = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'])
			$ip = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			// $ip = $_SERVER['REMOTE_ADDR'];
		else
			$ip = 'UNKNOWN';
		return $ip;
	}

	static function dias_transcurridos($fecha_i,$fecha_f)
	{
		$dias = (strtotime($fecha_f) - strtotime($fecha_i))/86400;
		$dias = floor($dias);
		return $dias;
	}

	static function completarCeros($numero, $length) {
		$resultado = "";
		$ceros     = "0";
		$lengthAux = strlen($numero);
		if ($lengthAux === $length) {
			$resultado = $numero;
		} else if ($lengthAux < $length) {
			for ($i = 0; $i < ($length - $lengthAux); $i++) {
				$resultado = $resultado.$ceros;
			}
			$resultado = $resultado.$numero;
		} else if ($lengthAux > $length) {
			$resultado = "NUMERO MAXIMO ALCANZADO";
		}
		return $resultado;
	}
	static function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

	static function generarComboproducto($id = NULL)
	{
		$productospadre = Producto::where(function ($query) use($id){
										if(!is_null($id)){
											$query->where('id', '<>', $id);
										}
									})->whereNull('producto_id')->get();
		$cboProducto = array('' => 'Seleccione');
		foreach ($productospadre as $key => $value) {
			$cboProducto = $cboProducto + array($value->id => $value->nombre);
			$cboProducto = self::productosHijos($value->id, $cboProducto, 2, $id);
		}
		return $cboProducto;
	}

	static function productosHijos($producto_id, $cboProducto, $nivel, $id = NULL)
	{
		$productoshijo = Producto::where(function ($query) use($id){
										if(!is_null($id)){
											$query->where('id', '<>', $id);
										}
									})->where('producto_id', '=', $producto_id)->get();
		foreach ($productoshijo as $key => $value) {
			$nombre = '&nbsp;';
			for ($i=0; $i < $nivel; $i++) {
				$nombre = $nombre.$nombre;
			}
			$cboProducto = $cboProducto + array($value->id => $nombre.$value->nombre);
			$cboProducto = self::productosHijos($value->id, $cboProducto, $nivel + 1, $id);
		}
		return $cboProducto;
	}

	static function generarCombopresentacion($idpadre)
	{
		$productospadre  = Producto::where('producto_id', '=', $idpadre)->get();
		$cboPresentacion = array();
		$cboPresentacion = self::presentacionporproducto($idpadre, $cboPresentacion);

		foreach ($productospadre as $key => $value) {
			$cboPresentacion = self::presentacionporproducto($value->id, $cboPresentacion);
			$cboPresentacion = self::presentacionesproductosHijos($value->id, $cboPresentacion);
		}
		return $cboPresentacion;
	}

	static function presentacionesproductosHijos($producto_id, $cboPresentacion)
	{
		$productoshijo = Producto::where('producto_id', '=', $producto_id)->get();
		foreach ($productoshijo as $key => $value) {
			$cboPresentacion = self::presentacionporproducto($value->id, $cboPresentacion);
			$cboPresentacion = self::presentacionesproductosHijos($value->id, $cboPresentacion);
		}
		return $cboPresentacion;
	}

	static function presentacionporproducto($idproducto, $cboPresentacion)
	{
		$presentaciones = Presentacion::where('producto_id', '=', $idproducto)->get();
		$producto = Producto::find($idproducto);
		foreach ($presentaciones as $key => $value) {
			$cboPresentacion[] = array('id' => $value->id, 'nombre' => $producto->nombre.' - '.$value->nombre);
		}
		return $cboPresentacion;
	}

	public function generararbolproducto($idproducto, $cadena = '')
	{
		$producto = Producto::find($idproducto);
		$cadena = $producto->nombre.' - '.$cadena;
		if (!is_null($producto->producto_id)) {
			$padre = $producto->productopadre;
			$cadena = $padre->nombre.' - '.$cadena;
			if (!is_null($padre->producto_id)) {
				$cadena = $this->generararbolproducto($padre->producto_id, $cadena);
			}
		}
		return $cadena;
	}

	public function generararbolproducto2($idproducto, $cadena = '')
	{
		$producto = Producto::find($idproducto);
		if (!is_null($producto->producto_id)) {
			$cadena = $producto->nombre.' '.$cadena;
			$padre = $producto->productopadre;
			if (!is_null($padre->producto_id)) {
				$cadena = $padre->nombre.' '.$cadena;
				$cadena = $this->generararbolproducto2($padre->producto_id, $cadena);
			}else{
				$cadena = $cadena;
			}
		}else{
			$cadena = $cadena;
		}
		return $cadena;
	}

	public function productoprincipal_id($idproducto)
	{
		$producto = Producto::find($idproducto);
		$producto_id = $producto->id;
		if (!is_null($producto->producto_id)) {
			$padre = $producto->productopadre;
			$producto_id = $padre->id;
			if (!is_null($padre->producto_id)) {
				$producto_id = $this->productoprincipal_id($padre->producto_id);
			}
		}
		return $producto_id;
	}

	static function formatoFecha($fecha, $formatoorigen, $formatodestino)
	{
		if (is_null(self::getParam($fecha))) {
			return NULL;
		}
		$date = DateTime::createFromFormat($formatoorigen, $fecha);
		return $date->format($formatodestino);
	}

	static function obtenerParametro($value = NULL)
	{
		return (!is_null($value) && trim($value) !== '') ? $value : NULL ;
	}

	public static function verificarExistencia($id, $tabla)
	{
		$reglas = array(
			'id' => 'required|integer|exists:'.session('base').'.'.$tabla.',id,deleted_at,NULL'
			);
		$validacion = Validator::make(array('id' => $id), $reglas);
		if ($validacion->fails()) {
			$cadena = '<blockquote><p class="text-danger">Registro no existe en la base de datos. No manipular ID</p></blockquote>';
			$cadena .= '<button class="btn btn-warning btn-sm" id="btnCerrarexiste"><i class="fa fa-times fa-lg"></i> Cerrar</button>';
			$cadena .= "<script type=\"text/javascript\">
							$(document).ready(function() {
								$('#btnCerrarexiste').attr('onclick','cerrarModal(' + (contadorModal - 1) + ');').unbind('click');
							});
						</script>";
			return $cadena;
		}else{
			return true;
		}
	}

	public static function getParam($valor, $defecto = NULL)
	{
		return (!is_null($valor) && trim($valor) !== '') ? $valor : $defecto ;
	}

	public static function obtenerigvtipocambio($monto)
	{
		$total                    = (float)$monto;
		$valorigv                 = Configuracion::where('parametro', '=', 'igv')->first()->valor;
		$valor                    = round(($total/$valorigv), 2);
		$igv                      = round($total - $valor, 2);
		$parametros               = array();
		$parametros['valorigv']   = $valorigv;
		$parametros['valor']      = $valor;
		$parametros['igv']        = $igv;
		return $parametros;
	}

	public static function generarAbecedario()
	{
		$abecedario = array();
		foreach(range('A', 'Z') as $letter1) {
			$abecedario[] = $letter1;
		}
		foreach(range('A', 'Z') as $letter1) {
			foreach(range('A', 'Z') as $letter2) {
				$abecedario[] = $letter1 . $letter2;
			}
		}
		return $abecedario;
	}

	public static function reemplazarValor($valor, $igual, $retorno)
	{
		if ($valor === $igual) {
			return $retorno;
		}
		return $valor;
	}

	public static function get_format_diference_date($df) {

	    $str = '';
	    $str .= ($df->invert == 1) ? ' - ' : '';
	    if ($df->y > 0) {
	        // years
	        $str .= ($df->y > 1) ? $df->y . ' Años ' : $df->y . ' Año ';
	    } if ($df->m > 0) {
	        // month
	        $str .= ($df->m > 1) ? $df->m . ' Meses ' : $df->m . ' Mes ';
	    } if ($df->d > 0) {
	        // days
	        $str .= ($df->d > 1) ? $df->d . ' Dias ' : $df->d . ' Dia ';
	    } if ($df->h > 0) {
	        // hours
	        $str .= ($df->h > 1) ? $df->h . ' Horas ' : $df->h . ' Hora ';
	    } if ($df->i > 0) {
	        // minutes
	        $str .= ($df->i > 1) ? $df->i . ' Minutos ' : $df->i . ' Minuto ';
	    } if ($df->s > 0) {
	        // seconds
	        $str .= ($df->s > 1) ? $df->s . ' Segundos ' : $df->s . ' Segundo ';
	    }

	    return $str;
	}
}
