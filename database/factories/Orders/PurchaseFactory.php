<?php

namespace Database\Factories\Orders;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Catalog\Product;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Orders\Purchase>
 */
class PurchaseFactory extends Factory
{

    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()?->id,
            'quantity' => $this->faker->numberBetween(1, 100),
            'purchase_price' => $this->faker->randomFloat(2, 500, 50000),
            'purchased_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

}
