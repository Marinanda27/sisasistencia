<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchofficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
       DB::table('branchoffice')->insert(array(
            array(
                'name' => 'SEDE PRINCIPAL',
                'created_at' => now(),
                'updated_at' => now()
            )
        ));

    }
}
