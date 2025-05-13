<?php

namespace App\Enums;
enum OrderStatusEnum: string
{
    case FILLED = 'filled';
    case PENDING = 'pending';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
}
