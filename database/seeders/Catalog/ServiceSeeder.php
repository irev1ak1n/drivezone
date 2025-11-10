<?php

namespace Database\Seeders\Catalog;

use App\Models\Catalog\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::factory()->count(50)->create();
    }
}
