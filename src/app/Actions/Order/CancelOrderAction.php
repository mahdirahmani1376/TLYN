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
    }
}
