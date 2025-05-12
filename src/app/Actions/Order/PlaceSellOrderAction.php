<?php

namespace App\Actions\Order;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Exceptions\UserHasInsufficientBalanceException;
use App\Models\Order;
use App\Models\User;

class PlaceSellOrderAction
{
    public function execute(User $user, array $data)
    {
        $this->validateUserBalanceToSell($user, $data);

        $order = Order::create([
            'user_id' => $user->id,
            'type' => OrderTypeEnum::SELL,
            'amount' => $data['amount'],
            'remaining_amount' => $data['amount'],
            'price' => $data['price'],
            'status' => OrderStatusEnum::PENDING
        ]);

        return $order;
    }

    private function validateUserBalanceToSell($user, $data): void
    {
        $totalOrderAmount = $data['amount'];

        $userBalance = $user->wallet->gold_balance;

        if ($userBalance < $totalOrderAmount) {
            throw UserHasInsufficientBalanceException::make();
        }
    }
}
