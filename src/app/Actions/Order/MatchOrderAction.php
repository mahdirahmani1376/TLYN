<?php

namespace App\Actions\Order;

use App\Actions\Commission\CalculateCommissionAction;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Trade;
use App\Models\Transaction;
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
        $buyOrdersGroup = Order::getBuyOrdersGroupedByPrice();
        $sellOrdersGroup = Order::getSellOrdersGroupedByPrice();

        foreach ($buyOrdersGroup as $buyGroupKey => $buyGroup) {
            if (empty($sellOrdersGroup[$buyGroupKey])) {
                continue;
            }

            $matchedSellGroup = $sellOrdersGroup[$buyGroupKey];

            $this->matchBuyAndSellOrders($buyGroup, $matchedSellGroup);

        }
    }

    private function matchBuyAndSellOrders(mixed $buyGroup, mixed $matchedSellGroup): void
    {
        /** @var Order $buyOrder */
        foreach ($buyGroup as $buyOrder) {

            /** @var Order $sellOrder */
            foreach ($matchedSellGroup as $sellOrder) {

                if ($sellOrder->remaining_amount == 0) {
                    break;
                }

                $minQuantityToMatch = min($buyOrder->remaining_amount, $sellOrder->remaining_amount);

                DB::beginTransaction();

                try {
//                    $sellOrder->lockForUpdate();
//                    $buyOrder->lockForUpdate();

                    $commission = $this->calculateCommissionAction->execute($minQuantityToMatch, $buyOrder->price);

                    $trade = Trade::create([
                        'amount' => $minQuantityToMatch,
                        'buy_order_id' => $buyOrder->id,
                        'sell_order_id' => $sellOrder->id,
                        'price' => $buyOrder->price,
                        'total' => $buyOrder->price * $minQuantityToMatch,
                        'commission' => $commission
                    ]);

                    $buyOrder->remaining_amount -= $minQuantityToMatch;
                    $buyOrder->save();

                    $sellOrder->remaining_amount -= $minQuantityToMatch;
                    $sellOrder->save();

                    $this->processBuyOrder($buyOrder, $trade);
                    $this->processSellOrder($sellOrder, $trade);


                    if ($sellOrder->remaining_amount == 0) {
                        $this->markOrderAsFilled($sellOrder);
                    }

                    if ($buyOrder->remaining_amount == 0) {
                        $this->markOrderAsFilled($buyOrder);
                        DB::commit();
                        break;
                    }

                    DB::commit();

                } catch (\Exception $e) {
                    dump($e->getMessage());
                    Log::error("Order match failed", [
                        'error' => $e->getMessage(),
                        'buy_order_id' => $buyOrder->id ?? null,
                        'sell_order_id' => $sellOrder->id ?? null
                    ]);
                    DB::rollBack();
                }

            }
        }

    }

    private function processBuyOrder(Order $buyOrder, Trade $trade)
    {
        Transaction::create([
            'user_id' => $buyOrder->user_id,
            'type' => 'buy_order',
            'description' => "order filled for buy",
            'trade_id' => $trade->id,
            'amount' => $buyOrder->price * $trade->amount * -1
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

    private function processSellOrder(Order $sellOrder, Trade $trade)
    {
        Transaction::create([
            'user_id' => $sellOrder->user_id,
            'type' => 'sell_order',
            'description' => "order filled for sell",
            'trade_id' => $trade->id,
            'amount' => $sellOrder->price * $trade->amount
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

}
