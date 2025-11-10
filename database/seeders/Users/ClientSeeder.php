<?php

namespace Database\Seeders;

namespace Database\Seeders\Users;

use App\Models\Users\Client;
use App\Models\Users\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Берём всех пользователей-«клиентов»
        $customers = User::where('role', 'customer')->get();

        foreach ($customers as $user) {
            Client::factory()->create([
                'user_id' => $user->id, // связываем с users
            ]);
        }
    }
}
