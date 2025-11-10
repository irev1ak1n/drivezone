<?php

namespace Database\Seeders;

use Database\Seeders\Catalog\BrandSeeder;
use Database\Seeders\Catalog\ProductSeeder;
use Database\Seeders\Catalog\ServiceSeeder;
use Database\Seeders\Orders\CartSeeder;
use Database\Seeders\Orders\PurchaseSeeder;
use Database\Seeders\Orders\ReviewSeeder;
use Database\Seeders\Orders\SaleSeeder;
use Database\Seeders\Users\ClientSeeder;
use Database\Seeders\Users\EmployeeSeeder;
use Database\Seeders\Users\UserSeeder;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // вызываем сидер пользователей
        $this->call([
            UserSeeder::class,
            ClientSeeder::class,
            EmployeeSeeder::class,
            ReviewSeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            ServiceSeeder::class,
            PurchaseSeeder::class,
            SaleSeeder::class,
            ReviewSeeder::class,
            CartSeeder::class,
        ]);;

    }
}
