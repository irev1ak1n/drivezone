<?php

namespace Database\Seeders\Orders;

use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Orders\Sale::factory()->count(30)->create();
    }
}
