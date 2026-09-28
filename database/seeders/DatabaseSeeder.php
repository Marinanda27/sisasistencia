<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call(BranchofficeSeeder::class);
        $this->call(UsertypeSeeder::class);
        $this->call(PersonSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(MenuoptioncategorySeeder::class);
        $this->call(MenuoptionSeeder::class);
        $this->call(PermissionSeeder::class);

    }
}
