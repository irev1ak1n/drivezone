<?php

namespace Database\Factories\Orders;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Orders\Sale>
 */
class SaleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Catalog\Product::inRandomOrder()->first()?->id,
            'client_id' => \App\Models\Users\User::where('role', 'customer')->inRandomOrder()->first()?->id,
            'quantity' => $this->faker->numberBetween(1, 5),
            'sold_price' => $this->faker->randomFloat(2, 1000, 50000),
            'sold_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

}
