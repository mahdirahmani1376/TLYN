<?php

namespace Database\Seeders;

use App\Actions\Order\PlaceBuyOrderAction;
use App\Actions\Order\PlaceSellOrderAction;
use App\Models\Commission;
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
        $this->seedCommissions();
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

        $akbar = User::create([
            'name' => 'akbar',
            'email' => 'akbar@test.com',
            'password' => '123@test',
        ]);

        Wallet::create([
            'gold_balance' => 0,
            'rial_balance' => 20000000,
            'user_id' => $ahmad->id
        ]);

        Wallet::create([
            'gold_balance' => 0,
            'rial_balance' => 50000000,
            'user_id' => $reza->id
        ]);

        Wallet::create([
            'gold_balance' => 10,
            'rial_balance' => 0,
            'user_id' => $akbar->id
        ]);

        app(PlaceBuyOrderAction::class)->execute($ahmad, [
            'price' => 10000000,
            'amount' => 2
        ]);

        app(PlaceBuyOrderAction::class)->execute($reza, [
            'price' => 10000000,
            'amount' => 5
        ]);

        app(PlaceSellOrderAction::class)->execute($akbar, [
            'price' => 10000000,
            'amount' => 10
        ]);
    }

    private function seedCommissions()
    {
        Commission::create([
            'min_amount' => 0,
            'max_amount' => 1,
            'rate' => 2
        ]);

        Commission::create([
            'min_amount' => 1,
            'max_amount' => 10,
            'rate' => 1.5
        ]);

        Commission::create([
            'min_amount' => 10,
            'max_amount' => null,
            'rate' => 1
        ]);
    }
}
