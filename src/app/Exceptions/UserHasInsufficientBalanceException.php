<?php

namespace App\Exceptions;

use Exception;

class UserHasInsufficientBalanceException extends Exception
{
    public static function make()
    {
        return new static('user has insufficient balance');
    }
}
