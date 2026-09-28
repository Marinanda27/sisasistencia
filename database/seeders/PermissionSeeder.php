<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $usertype_id = DB::table('usertype')->where('name', '=', 'ADMINISTRADOR PRINCIPAL')->first()->id;
		$list          = DB::table('menuoption')->get();
		foreach ($list as $key => $value) {
			DB::table('permission')->insert(array(
				array(
					'usertype_id' => $usertype_id,
					'menuoption_id'  => $value->id,
					'created_at'     => now(),
					'updated_at'     => now()
					)
				));
		}


    }
}
