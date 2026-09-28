<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        DB::table('user')->insert([
            'login' => 'admin',
            'password' => Hash::make('123456'),
            'state' => 'H',
            'usertype_id' => 1,
            'person_id' => 1,
            'branchoffice_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
