<?php

namespace App\Actions\Commission;

use App\Models\Order;

class CalculateCommissionAction
{
    public function execute(Order $order)
    {
        $total = $order->price * $order->amount;
        $orderAmount = $order->amount;

        return $total * 0.02;
    }
}
