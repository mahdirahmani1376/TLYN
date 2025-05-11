<?php

namespace App\Actions\User;

use App\Models\User;

class StoreUserAction
{
    public function __invoke(array $data): User
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => $data['password'],
            'name' => $data['name'],
        ]);

        return $user;
    }
}
