<?php

namespace App\Actions\User;

use App\Models\User;

class RespondWithTokenAction
{
    public function __invoke(User $user): string
    {
        return $user->createToken('access')->plainTextToken;
    }
}
