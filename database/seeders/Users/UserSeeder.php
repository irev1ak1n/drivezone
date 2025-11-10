<?php

namespace Database\Seeders\Users;

use App\Models\Users\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 40 клиентов
        User::factory()->count(40)->create([
            'role' => 'customer',
        ]);

        // 10 сотрудников
        User::factory()->count(10)->create([
            'role' => 'employee',
        ]);

        // 2 менеджера
        User::factory()->count(3)->create([
            'role' => 'manager',
        ]);

        // 3 админа
        User::factory()->count(2)->create([
            'role' => 'admin',
        ]);

    }
}
