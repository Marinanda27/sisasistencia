<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('person')->insert(array(
            array(
                'lastname' => 'Principal',
                'firstname' => 'Administrador',
                'created_at' => now(),
                'updated_at' => now()
            )
        ));
    }
}
