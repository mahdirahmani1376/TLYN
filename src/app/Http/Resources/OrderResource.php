<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'amount' => $this->amount,
            'remaining_amount' => $this->remaining_amount,
            'price' => $this->price->formatted(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'buy_trade' => TradeResource::make($this->whenLoaded('buyTrade')),
            'sell_trade' => TradeResource::make($this->whenLoaded('sellTrade')),
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
