<?php

namespace Database\Seeders\Catalog;

use App\Models\Catalog\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // создаём 50 товаров случайно
        Product::factory()->count(50)->create();
    }
}
