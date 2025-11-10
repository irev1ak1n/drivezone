<?php

namespace Database\Factories\Orders;

use App\Models\Catalog\Product;
use App\Models\Orders\Cart;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        // случайно выбираем product (услуги подключим позже)
        $product = Product::inRandomOrder()->first();

        return [
            'user_id' => User::inRandomOrder()->first()?->id,
            'subject_id' => $product?->id,
            'subject_type' => 'product',
        ];
    }
}
