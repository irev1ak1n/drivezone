<?php

namespace Database\Seeders\Orders;

use App\Models\Catalog\Product;
use App\Models\Catalog\Service;
use App\Models\Orders\Cart;
use App\Models\Users\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();

        // список заметок
        $notes = [
            'Срочно, нужно сегодня',
            'Добавить скидку по купону',
            'Оплата при получении',
            'Проверить совместимость с авто',
            'Заберу сам из сервиса',
            'Попросить заменить на аналог',
            'Установить сразу после покупки',
            'Связаться перед доставкой',
        ];

        foreach ($customers as $customer) {
            // случайно продукт или услуга
            if (rand(0, 1)) {
                Cart::create([
                    'id'           => substr(Str::uuid()->toString(), 0, 15),
                    'user_id'      => $customer->id,
                    'subject_id'   => Product::inRandomOrder()->first()->id,
                    'subject_type' => 'product',
                    'quantity'     => rand(1, 10),
                    'note'         => $notes[array_rand($notes)],
                ]);
            } else {
                Cart::create([
                    'id'           => substr(Str::uuid()->toString(), 0, 15),
                    'user_id'      => $customer->id,
                    'subject_id'   => Service::inRandomOrder()->first()->id,
                    'subject_type' => 'service',
                    'quantity'     => rand(1, 10),
                    'note'         => $notes[array_rand($notes)],
                ]);
            }
        }
    }
}
