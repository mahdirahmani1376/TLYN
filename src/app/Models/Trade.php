<?php

namespace App\Models;

use App\Casts\PriceCast;
use App\ValueObjects\Price;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Price $price
 */
class Trade extends Model
{
    protected $fillable = [
        'buy_order_id',
        'sell_order_id',
        'price',
        'total',
        'amount',
        'commission',
    ];

    protected $casts = [
        'price' => PriceCast::class,
    ];
}
