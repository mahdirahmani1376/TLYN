<?php

namespace App\Http\Controllers;

use App\Actions\Order\PlaceBuyOrderAction;
use App\Actions\Order\PlaceSellOrderAction;
use App\Http\Requests\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    public function index()
    {
        dd(1);
        return Response::json(
            OrderResource::collection(
                Order::with([
                    'user' => [
                        'transactions',
                        'wallet'
                    ],
                    'buyTrade',
                    'sellTrade'
                ])
            )
        );
    }

    public function buy(
        PlaceBuyOrderAction $placeBuyOrderAction,
        PlaceOrderRequest   $placeBuyOrderRequest,

    )
    {
        $order = $placeBuyOrderAction->execute(Auth::user(), $placeBuyOrderRequest->validated());

        return Response::json(OrderResource::make($order));
    }

    public function sell(
        PlaceSellOrderAction $placeSellOrderAction,
        PlaceOrderRequest    $placeBuyOrderRequest,

    )
    {
        $order = $placeSellOrderAction->execute(Auth::user(), $placeBuyOrderRequest->validated());

        return Response::json(OrderResource::make($order));
    }
}
