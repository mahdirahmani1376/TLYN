<?php

namespace App\Http\Controllers;

use App\Actions\Order\PlaceBuyOrderAction;
use App\Actions\Order\PlaceSellOrderAction;
use App\Http\Requests\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    public function index()
    {

    }

    public function buy(
        PlaceBuyOrderAction $placeBuyOrderAction,
        PlaceOrderRequest   $placeBuyOrderRequest,

    )
    {
        $order = $placeBuyOrderAction->execute(Auth::user(), $placeBuyOrderRequest);

        return Response::json(OrderResource::make($order));
    }

    public function sell(
        PlaceSellOrderAction $placeSellOrderAction,
        PlaceOrderRequest    $placeBuyOrderRequest,

    )
    {
        $order = $placeSellOrderAction->execute(Auth::user(), $placeSellOrderAction);

        return Response::json(OrderResource::make($order));
    }
}
