<?php

namespace Database\Seeders;

use App\Actions\Order\PlaceBuyOrderAction;
use App\Actions\Order\PlaceSellOrderAction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
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
}
