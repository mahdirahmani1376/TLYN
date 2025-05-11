<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedUsers();
    }

    private function seedUsers()
    {
        $ahmad = User::factory()->create([
            'name' => 'ahmad',
            'email' => 'ahmad@test.com',
            'password' => '123@test',
        ]);

        $reza = User::factory()->create([
            'name' => 'reza',
            'email' => 'reza@test.com',
            'password' => '123@test',
        ]);

        Wallet::create([
            'balance' => 15,
            'user_id' => $reza->id
        ]);

        Wallet::create([
            'balance' => 30,
            'user_id' => $ahmad->id
        ]);
    }
}
