<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class UserLoginAction
{
    public function __invoke(array $data): User
    {
        $user = Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        if (!$user) {
            throw new AuthenticationException;
        }

        return Auth::user();
    }
}
