<?php

namespace Tests\Feature;

use App\Actions\Order\MatchOrderAction;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Models\User;
use App\Models\Wallet;
use Database\Seeders\CommissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_less_than_seller(): void
    {
        $this->seed();

        app(MatchOrderAction::class)->execute();

        $this->assertDatabaseHas('orders', [
            'type' => OrderTypeEnum::BUY,
            'status' => OrderStatusEnum::FILLED,
            'amount' => 2.000
        ]);

        $this->assertDatabaseHas('orders', [
            'type' => OrderTypeEnum::BUY,
            'status' => OrderStatusEnum::FILLED,
            'amount' => 5.000
        ]);

        $this->assertDatabaseHas('orders', [
            'type' => OrderTypeEnum::SELL,
            'status' => OrderStatusEnum::PENDING,
            'remaining_amount' => 3.000
        ]);

        $this->assertDatabaseHas('wallets', [
            'gold_balance' => 2.000,
            'rial_balance' => 0
        ]);

        $this->assertDatabaseHas('wallets', [
            'gold_balance' => 5.000,
            'rial_balance' => 0
        ]);

        $this->assertDatabaseHas('wallets', [
            'gold_balance' => 3.000,
            'rial_balance' => 70000000
        ]);

    }

    public function test_seller_less_than_buyer()
    {
        $this->seed(CommissionSeeder::class);

        $seller = User::factory()->create([
            'name' => 'seller',
            'email' => 'seller@test.com',
        ]);

        $buyer1 = User::factory()->create([
            'name' => 'buyer_1',
            'email' => 'buyer_1@test.com',
        ]);

        $buyer2 = User::factory()->create([
            'name' => 'buyer_2',
            'email' => 'buyer_2@test.com',
        ]);

        Wallet::create([
            'user_id' => $seller->id,
            'gold_balance' => 1,
            'rial_balance' => 0
        ]);

        Wallet::create([
            'user_id' => $buyer1->id,
            'gold_balance' => 0,
            'rial_balance' => 1000
        ]);

        Wallet::create([
            'user_id' => $buyer2->id,
            'gold_balance' => 0,
            'rial_balance' => 1000
        ]);

        $this->actingAs($seller)->post(route('orders.sell'), [
            'amount' => 1,
            'price' => 1000
        ]);

        $this->actingAs($buyer1)->post(route('orders.buy'), [
            'amount' => 1,
            'price' => 1000
        ]);

        $this->actingAs($buyer2)->post(route('orders.buy'), [
            'amount' => 1,
            'price' => 1000
        ]);

        $this->app->make(MatchOrderAction::class)->execute();

        $this->assertDatabaseHas('orders', [
            'type' => OrderTypeEnum::BUY,
            'status' => OrderStatusEnum::FILLED,
            'amount' => 1
        ]);

        $this->assertDatabaseHas('orders', [
            'type' => OrderTypeEnum::BUY,
            'status' => OrderStatusEnum::PENDING,
            'amount' => 1
        ]);

        $this->assertDatabaseHas('orders', [
            'type' => OrderTypeEnum::SELL,
            'status' => OrderStatusEnum::FILLED,
            'remaining_amount' => 0
        ]);

        $this->assertDatabaseHas('wallets', [
            'gold_balance' => 0,
            'rial_balance' => 1000,
            'user_id' => $seller->id
        ]);

        $this->assertDatabaseHas('wallets', [
            'gold_balance' => 1.000,
            'rial_balance' => 0,
        ]);

        $this->assertDatabaseHas('wallets', [
            'gold_balance' => 0,
            'rial_balance' => 1000,
        ]);
    }
}
