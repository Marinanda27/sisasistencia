<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuoptioncategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('menuoptioncategory')->insert(array(
            array(
                'name'     => 'Movimientos',
                'order'      => 1,
                'icon'      => 'fa fa-bank',
                'position'      => 'V',
                'created_at' => now(),
                'updated_at' => now()
            ),
            array(
                'name'     => 'Personas',
                'order'      => 2,
                'icon'      => 'ion-person-stalker',
                'position'      => 'V',
                'created_at' => now(),
                'updated_at' => now()
            ),
            array(
                'name'     => 'Usuarios',
                'order'      => 3,
                'icon'      => 'ion-person',
                'position'      => 'V',
                'created_at' => now(),
                'updated_at' => now()
            ),
            array(
                'name'     => 'Mantenimientos',
                'order'      => 1,
                'icon'      => 'ion-ios7-paper',
                'position'      => 'V',
                'created_at' => now(),
                'updated_at' => now()
            )
        )
    );
    }
}
