<?php

namespace Database\Factories\Users;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Users\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),   // Иван / Мария
            'last_name' => $this->faker->lastName(),     // Петров / Смирнова
            'photo_url' => 'https://via.placeholder.com/150', // нормальная картинка-заглушка
            'gender' => $this->faker->randomElement(['male', 'female']),
            'email' => $this->faker->unique()->userName() .
                '@' . $this->faker->randomElement(['gmail.com', 'yahoo.com', 'mail.ru']),
            'email_verified_at' => now(),
            'phone_number' => '+7' . $this->faker->numerify('9#########'), // российский формат +79998887766
            'password' => Hash::make('password123456'),
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-16 years')->format('Y-m-d'),
            'role' => $this->faker->randomElement([
                'customer',
                'employee',
                'manager',
                'admin'
            ]),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate что email пользователя не подтверждён.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
