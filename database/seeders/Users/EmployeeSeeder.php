<?php

namespace Database\Seeders\Users;

use App\Models\Users\Employee;
use App\Models\Users\User;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // выбираем всех пользователей с ролью employee
        $employees = User::where('role', 'employee')->get();

        // выбираем случайного супервайзера (если есть)
        $supervisor = User::where('role', 'supervisor')->inRandomOrder()->first();

        foreach ($employees as $user) {
            Employee::factory()->create([
                'user_id' => $user->id,
                'supervisor_id' => $supervisor?->id, // безопасный null
            ]);
        }
    }
}
