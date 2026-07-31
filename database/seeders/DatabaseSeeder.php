<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ZoneSeeder::class,
            SuperAdminSeeder::class,
            ProductSeeder::class,
            DemoHierarchySeeder::class,
            DemoSalesSeeder::class,
        ]);
    }
}
