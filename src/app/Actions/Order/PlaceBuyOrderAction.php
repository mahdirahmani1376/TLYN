<?php

namespace App\Actions\Order;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Exceptions\UserHasInsufficientBalanceException;
use App\Models\Order;
use App\Models\User;

class PlaceBuyOrderAction
{
    public function execute(User $user, array $data)
    {
        $this->validateUserBalanceToBuy($user, $data);

        $order = Order::create([
            'user_id' => $user->id,
            'type' => OrderTypeEnum::BUY,
            'amount' => $data['amount'],
            'remaining_amount' => $data['amount'],
            'price' => $data['price'],
            'status' => OrderStatusEnum::PENDING
        ]);

        $updatedRialBalance = $user->wallet->rial_balance - ($data['price'] * $data['amount']);
        $user->wallet->update([
            'rial_balance' => $updatedRialBalance
        ]);

        return $order;
    }

    private function validateUserBalanceToBuy($user, $data): void
    {
        $totalOrderAmount = $data['amount'] * $data['price'];

        $userBalance = $user->wallet->rial_balance;

        if ($userBalance < $totalOrderAmount) {
            throw UserHasInsufficientBalanceException::make();
        }
    }
}
