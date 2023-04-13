<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $this->call([
            JurusanSeeder::class,
            ProdiSeeder::class,
            RoleSeeder::class,
            AccountSeeder::class,
            PanduanSeeder::class,
            EDSeeder::class,
            KSSeeder::class
        ]);
    }
}