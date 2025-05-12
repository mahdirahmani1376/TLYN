<?php

namespace App\Models;

use App\Enums\OrderTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function matchedOrderType(): OrderTypeEnum
    {
        return $this->type == OrderTypeEnum::BUY ? OrderTypeEnum::SELL : OrderTypeEnum::BUY;
    }
}
