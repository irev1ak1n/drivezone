<?php

namespace Database\Seeders\Orders;

use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Orders\Purchase::factory()->count(30)->create();
    }

}
