<?php

namespace App\Actions\Order;

use App\Actions\Commission\CalculateCommissionAction;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Models\Order;
use App\Models\Trade;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchOrderAction
{
    public function __construct(
        private CalculateCommissionAction $calculateCommissionAction
    )
    {
    }
    public function execute()
    {
        Order::getOrderBook()
            ->filter(function (Collection $orderGroup, int $key) {
                return $orderGroup->has(OrderTypeEnum::BUY->value, OrderTypeEnum::SELL->value);
            })
            ->each(function ($item) {
                $buyOrders = $item['buy'];
                $sellOrders = $item['sell'];

                $buyOrders->each(function (Order $buyOrder) use ($sellOrders) {
                    $sellOrders->each(function (Order $sellOrder, $sellOrderKey) use ($buyOrder, $sellOrders) {
                        if (empty($sellOrder->remaining_amount) || empty($buyOrder->remaining_amount)) {
                            return false;
                        }
                        return $this->matchBuyAndSellOrders($buyOrder, $sellOrder);
                    });
                });
            });

    }

    private function matchBuyAndSellOrders(Order $buyOrder, Order $sellOrder): bool
    {
        DB::beginTransaction();
        try {
            $this->makeTrade($buyOrder, $sellOrder);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            $this->log($e, $buyOrder, $sellOrder);
            DB::rollBack();
            return false;
        }

    }

    private function makeTrade(Order $buyOrder, Order $sellOrder)
    {
        $minQuantityToMatch = min($buyOrder->remaining_amount, $sellOrder->remaining_amount);

        $sellOrder->lockForUpdate();
        $buyOrder->lockForUpdate();

        $commission = $this->calculateCommissionAction->execute($minQuantityToMatch, $buyOrder->price->rial());

        $trade = Trade::create([
            'amount' => $minQuantityToMatch,
            'buy_order_id' => $buyOrder->id,
            'sell_order_id' => $sellOrder->id,
            'price' => $buyOrder->price,
            'total' => $buyOrder->price->rial() * $minQuantityToMatch,
            'commission' => $commission
        ]);

        $this->processBuyOrder($buyOrder, $trade, $minQuantityToMatch);
        $this->processSellOrder($sellOrder, $trade, $minQuantityToMatch);
    }

    private function processBuyOrder(Order $buyOrder, Trade $trade, $minQuantityToMatch)
    {
        $buyOrder->remaining_amount -= $minQuantityToMatch;
        $buyOrder->save();

        if ($buyOrder->remaining_amount == 0) {
            $this->markOrderAsFilled($buyOrder);
        }

        Transaction::create([
            'user_id' => $buyOrder->user_id,
            'type' => 'buy_order',
            'description' => "order filled for buy",
            'trade_id' => $trade->id,
            'amount' => $buyOrder->price->rial() * $trade->amount * -1
        ]);

        Transaction::create([
            'user_id' => $buyOrder->user_id,
            'type' => 'commission',
            'description' => "commission fee",
            'trade_id' => $trade->id,
            'amount' => $trade->commission * -1,
        ]);

        $wallet = $buyOrder->user->wallet;

        $wallet->gold_balance += $trade->amount;
        $wallet->rial_balance -= $trade->total;

        $wallet->save();

    }

    private function processSellOrder(Order $sellOrder, Trade $trade, $minQuantityToMatch)
    {
        $sellOrder->remaining_amount -= $minQuantityToMatch;
        $sellOrder->save();

        if ($sellOrder->remaining_amount == 0) {
            $this->markOrderAsFilled($sellOrder);
        }

        Transaction::create([
            'user_id' => $sellOrder->user_id,
            'type' => 'sell_order',
            'description' => "order filled for sell",
            'trade_id' => $trade->id,
            'amount' => $sellOrder->price->rial() * $trade->amount
        ]);

        Transaction::create([
            'user_id' => $sellOrder->user_id,
            'type' => 'commission',
            'description' => "commission fee",
            'trade_id' => $trade->id,
            'amount' => $trade->commission * -1,
        ]);

        $wallet = $sellOrder->user->wallet;
        $wallet->gold_balance -= $trade->amount;
        $wallet->rial_balance += $trade->total;
        $wallet->save();

    }

    private function markOrderAsFilled(Order $order)
    {
        $order->status = OrderStatusEnum::FILLED;
        $order->save();
    }

    private function log(\Exception $e, $buyOrder, $sellOrder): void
    {
        dump($e->getMessage());
        Log::error("Order match failed", [
            'error' => $e->getMessage(),
            'buy_order_id' => $buyOrder->id ?? null,
            'sell_order_id' => $sellOrder->id ?? null
        ]);
    }

}
