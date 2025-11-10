<?php

namespace Database\Seeders\Catalog;

use App\Models\Catalog\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['id' => 'toyota',      'name' => 'Toyota',      'country' => 'Japan',     'details' => 'Оригинальные детали Toyota'],
            ['id' => 'ford',        'name' => 'Ford',        'country' => 'USA',       'details' => 'Оригинальные детали Ford'],
            ['id' => 'bmw',         'name' => 'BMW',         'country' => 'Germany',   'details' => 'Производитель автомобилей и запчастей BMW'],
            ['id' => 'audi',        'name' => 'Audi',        'country' => 'Germany',   'details' => 'Оригинальные детали Audi'],
            ['id' => 'mercedes',    'name' => 'Mercedes-Benz','country' => 'Germany', 'details' => 'Оригинальные детали Mercedes-Benz'],
            ['id' => 'lada',        'name' => 'Lada',        'country' => 'Russia',    'details' => 'Автозапчасти АвтоВАЗ'],
            ['id' => 'kia',         'name' => 'Kia',         'country' => 'South Korea','details' => 'Оригинальные детали Kia'],
            ['id' => 'hyundai',     'name' => 'Hyundai',     'country' => 'South Korea','details' => 'Оригинальные детали Hyundai'],
            ['id' => 'nissan',      'name' => 'Nissan',      'country' => 'Japan',     'details' => 'Оригинальные детали Nissan'],
            ['id' => 'volkswagen',  'name' => 'Volkswagen',  'country' => 'Germany',   'details' => 'Оригинальные детали Volkswagen'],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['id' => $brand['id']], // проверяем по id
                $brand                  // если есть → обновим, если нет → создадим
            );
        }
    }
}
