<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsertypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        DB::table('usertype')->insert(array(
            array(
                'name' => 'ADMINISTRADOR PRINCIPAL',
                'created_at' => now(),
                'updated_at' => now()
            )
        ));
    DB::table('usertype')->insert(array(
            array(
                'name' => 'ADMINISTRADOR',
                'created_at' => now(),
                'updated_at' => now()
            )
        ));
    }
}
