<?php

namespace Database\Seeders\Orders;

use App\Models\Orders\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Генерируем 100 отзывов
        Review::factory()->count(50)->create();
    }
}
