<?php

namespace App\Http\Controllers;

use App\Actions\Order\CancelOrderAction;
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
        return Response::json(
            OrderResource::collection(
                Order::with([
                    'buyTrade',
                    'sellTrade'
                ])->where('user_id', auth()->id())->get()
            )
        );
    }

    public function show(int $orderId)
    {
        return Response::json(
            OrderResource::collection(
                Order::with([
                    'buyTrade',
                    'sellTrade'
                ])
                    ->where('user_id', auth()->id())
                    ->where('id', $orderId)
                    ->get()
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

    public function cancel(int $orderId, CancelOrderAction $cancelOrderAction)
    {
        $order = Order::where([
            'id' => $orderId,
            'user_id' => auth()->id()
        ])->firstOrFail();

        $cancelOrderAction->execute($order);

        return Response::json([
            'message' => 'order cancelled successfully'
        ]);
    }
}
