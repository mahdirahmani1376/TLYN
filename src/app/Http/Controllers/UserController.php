<?php

namespace App\Http\Controllers;

use App\Actions\User\RespondWithTokenAction;
use App\Actions\User\StoreUserAction;
use App\Actions\User\UpdateUserAction;
use App\Actions\User\UserLoginAction;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {

        return response()->json(
            UserResource::collection(
                User::all()
            )
        );
    }

    public function register(
        StoreUserRequest       $request,
        StoreUserAction        $storeUserAction,
        RespondWithTokenAction $respondWithTokenAction
    )
    {
        $user = $storeUserAction($request->validated());

        return response()->json(
            ['token' => $respondWithTokenAction($user)]
        );

    }

    public function login(
        LoginUserRequest       $request,
        UserLoginAction        $userLoginAction,
        RespondWithTokenAction $respondWithTokenAction
    )
    {
        $user = $userLoginAction($request->validated());

        return response()->json(
            ['token' => $respondWithTokenAction($user)]
        );

    }

    public function show()
    {
        return response()->json(
            UserResource::make(
                Auth::user()
            )
        );
    }

    public function update(UpdateUserAction $updateUserAction, UpdateUserRequest $updateUserRequest)
    {
        $user = $updateUserAction(auth()->user(), $updateUserRequest->validated());

        return response()->json(
            UserResource::make(
                Auth::user()
            )
        );
    }
}
