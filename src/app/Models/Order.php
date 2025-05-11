<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
