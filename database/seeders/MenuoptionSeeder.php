<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuoptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {


		$menuoptioncategory_id = DB::table('menuoptioncategory')->where('name', '=', 'Usuarios')->first()->id;

		$datos = array(
				array(
					'name' => 'Categoría de opción de menu',
					'link'   => 'categorymenu'
				),
				array(
					'name' => 'Opción de menu',
					'link'   => 'menuoption'
				),
				array(
					'name' => 'Tipos de usuario',
					'link'   => 'usertype'
				),
				array(
					'name' => 'Usuario',
					'link'   => 'user'
				)
			);

		for ($i=0; $i < count($datos); $i++) {
			DB::table('menuoption')->insert(array(
					'name'                 => $datos[$i]['name'],
					'link'                   => $datos[$i]['link'],
					'order'                  => $i+1,
					'menuoptioncategory_id' => $menuoptioncategory_id,
					'created_at'             => now(),
					'updated_at'             => now()
				)
			);
		}

		$menuoptioncategory_id = DB::table('menuoptioncategory')->where('name', '=', 'Mantenimientos')->first()->id;

		$datos = array(
				array(
					'name' => 'Datos Empresa',
					'link'   => 'company'
				)
			);

		for ($i=0; $i < count($datos); $i++) {
			DB::table('menuoption')->insert(array(
					'name'                 => $datos[$i]['name'],
					'link'                   => $datos[$i]['link'],
					'order'                  => $i+1,
					'menuoptioncategory_id' => $menuoptioncategory_id,
					'created_at'             => now(),
					'updated_at'             => now()
				)
			);
		}

		$menuoptioncategory_id = DB::table('menuoptioncategory')->where('name', '=', 'Personas')->first()->id;

		$datos = array(
				array(
					'name' => 'Tipo trabajador',
					'link'   => 'workertype'
				),
				array(
					'name' => 'Personas',
					'link'   => 'person'
				),
				array(
					'name' => 'Trabajadores',
					'link'   => 'employee'
				)
			);

		for ($i=0; $i < count($datos); $i++) {
			DB::table('menuoption')->insert(array(
					'name'                 => $datos[$i]['name'],
					'link'                   => $datos[$i]['link'],
					'order'                  => $i+1,
					'menuoptioncategory_id' => $menuoptioncategory_id,
					'created_at'             => now(),
					'updated_at'             => now()
				)
			);
		}

    }
}
