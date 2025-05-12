<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'min_amount',
        'max_amount',
        'rate',
        'min_fee',
        'max_fee',
    ];
}
