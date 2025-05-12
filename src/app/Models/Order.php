<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'status',
        'amount',
        'remaining_amount',
        'price',
    ];

    protected $casts = [
        'type' => OrderTypeEnum::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function buyTrade(): HasOne
    {
        return $this->hasOne(Trade::class, 'buy_order_id');
    }

    public function sellTrade(): HasOne
    {
        return $this->hasOne(Trade::class, 'sell_order_id');
    }
    public function matchedOrderType(): OrderTypeEnum
    {
        return $this->type == OrderTypeEnum::BUY ? OrderTypeEnum::SELL : OrderTypeEnum::BUY;
    }

    public function fillOrder($amount)
    {
        $status = $this->amount - $amount == 0 ? OrderStatusEnum::FILLED : OrderStatusEnum::PARTIALLY_FILLED;
    }

    public static function getBuyOrderGroupedBy()
    {
        return static::query()
            ->whereIn('status', [
                OrderStatusEnum::PENDING,
                OrderStatusEnum::PARTIALLY_FILLED
            ])
            ->where('type', OrderTypeEnum::BUY)
            ->get()
            ->groupBy('price');
    }

    public static function getSellOrderGroupedBy()
    {
        return static::query()
            ->whereIn('status', [
                OrderStatusEnum::PENDING,
                OrderStatusEnum::PARTIALLY_FILLED
            ])
            ->where('type', OrderTypeEnum::SELL)
            ->get()
            ->groupBy('price');
    }
}
