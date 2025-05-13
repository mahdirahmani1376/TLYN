<?php

namespace App\Actions\Order;

use App\Enums\OrderStatusEnum;
use App\Models\Order;

class CancelOrderAction
{
    public function execute(Order $order)
    {
        $order->update([
            'status' => OrderStatusEnum::CANCELLED
        ]);

        if ($order->isBuy()) {
            $this->cancelBuyOrder($order);
        } else {
            $this->cancelSellOrder($order);
        }
    }

    private function cancelBuyOrder(Order $order)
    {
        $rialAmount = $order->user->rial_balance + $order->remaining_amount * $order->price;

        $order->user->wallet->update([
            'rial_balance' => $rialAmount
        ]);
    }

    private function cancelSellOrder(Order $order)
    {
        $goldAmount = $order->user->gold_balance + ($order->remaining_amount);

        $order->user->wallet->update([
            'rial_balance' => $goldAmount
        ]);
    }
}
