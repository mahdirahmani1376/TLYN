<?php

namespace App\Jobs;

use App\Actions\Commission\CalculateCommissionAction;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Models\Order;
use App\Models\Trade;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(CalculateCommissionAction $calculateCommissionAction): void
    {
        DB::beginTransaction();

        $buyOrders = Order::query()
            ->whereIn('status', [
                OrderStatusEnum::PENDING,
                OrderStatusEnum::PARTIALLY_FILLED
            ])
            ->where('type', OrderTypeEnum::BUY)
            ->get()
            ->groupBy('price');

        $sellOrders = Order::query()
            ->whereIn('status', [
                OrderStatusEnum::PENDING,
                OrderStatusEnum::PARTIALLY_FILLED
            ])
            ->where('type', OrderTypeEnum::SELL)
            ->get()
            ->groupBy('price');


        dd($buyOrders->toArray(), $sellOrders->toArray());


        $matchingOrder = Order::firstWhere([
            'type' => $this->order->matchedOrderType()->value,
            'price' => $this->order->type
        ])->whereIn('type', [
            OrderStatusEnum::PARTIALLY_FILLED,
            OrderStatusEnum::PENDING
        ]);

        if (empty($matchingOrder)) {
            DB::rollBack();
            return;
        }

        try {
            $matchingOrder->lockForUpdate();

            $trade = Trade::create([
                'amount' => $matchingOrder->amount,
                'buy_order_id' => $this->order->id,
                'sell_order_id' => $matchingOrder->id,
                'price' => $matchingOrder->price,
                'total' => $matchingOrder->price * $matchingOrder->amount,
                'commission' => $calculateCommissionAction->execute($matchingOrder)
            ]);

            Transaction::create([
                'user_id' => $this->order->user_id,
                'type' => 'buy_order',
                'description' => "order filled for buy",
                'trade_id' => $trade->id
            ]);

            Transaction::create([
                'user_id' => $matchingOrder->user_id,
                'type' => 'sell_order',
                'description' => "order filled for sell",
                'trade_id' => $trade->id
            ]);

            Transaction::create([
                'user_id' => $this->order->user_id,
                'type' => 'commission',
                'description' => "commission fee",
                'trade_id' => $trade->id,
                'amount' => $trade->total * -1
            ]);

            Transaction::create([
                'user_id' => $matchingOrder->user_id,
                'type' => 'commission',
                'description' => "commission fee",
                'trade_id' => $trade->id,
                'amount' => $trade->total,
            ]);

            Transaction::create([
                'user_id' => $matchingOrder->user_id,
                'type' => 'commission',
                'description' => "commission fee",
                'trade_id' => $trade->id,
                'amount' => $trade->commission * -1
            ]);

            $matchingOrder->user->wallet->rial_balance += $trade->total;
            $matchingOrder->user->wallet->gold_balance -= $trade->total;

            $this->order->user->wallet->rial_balance -= $trade->total;
            $this->order->user->wallet->gold_balance += $trade->total;

            $remainingMatchOrder = $matchingOrder->amount - $this->order->amount;


            DB::commit();
        } catch (\Exception $e) {
            Log::error("match order job error", [
                'error' => $e->getMessage()
            ]);
            DB::rollBack();
        }

    }
}
