<?php

namespace Database\Factories\Users;

use App\Models\Users\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $streets = [
            'Артёма',
            'Университетская',
            'Постышева',
            'Щорса',
            'Куприна',
            'Челюскинцев',
            'Ильича',
            'Ленинский проспект',
            'Пушкина',
            'Нижнекурганская',
        ];

        $vehicles = [
            'BMW X5 2018',
            'BMW 320i 2019',
            'Mercedes-Benz E200 2019',
            'Mercedes-Benz GLE 2021',
            'Audi A6 2020',
            'Audi Q7 2022',
            'Toyota Camry 2020',
            'Toyota RAV4 2021',
            'Lada Vesta 2021',
            'Lada Granta 2019',
            'Kia Rio 2022',
            'Kia Sportage 2021',
            'Hyundai Tucson 2023',
            'Hyundai Solaris 2020',
            'Volkswagen Passat 2018',
            'Volkswagen Tiguan 2021',
            'Skoda Octavia 2019',
            'Skoda Kodiaq 2022',
            'Nissan Qashqai 2020',
            'Nissan X-Trail 2021',
            'Mazda 6 2020',
            'Mazda CX-5 2022',
            'Renault Logan 2019',
            'Renault Duster 2021',
        ];

        return [
            'address' => 'г. Донецк, ул. ' .
                $this->faker->randomElement($streets) .
                ', д. ' . $this->faker->numberBetween(10, 50) .
                '/кв. ' . $this->faker->numberBetween(12, 120),
            'vehicle_info' => $this->faker->randomElement($vehicles),
        ];
    }
}
