<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'North', 'slug' => 'north', 'description' => 'Northern India Zone'],
            ['name' => 'South', 'slug' => 'south', 'description' => 'Southern India Zone'],
            ['name' => 'East',  'slug' => 'east',  'description' => 'Eastern India Zone'],
            ['name' => 'West',  'slug' => 'west',  'description' => 'Western India Zone'],
        ];

        foreach ($zones as $zone) {
            Zone::updateOrCreate(['slug' => $zone['slug']], $zone);
        }

        $this->command->info('✅ Zones seeded: North, South, East, West');
    }
}
