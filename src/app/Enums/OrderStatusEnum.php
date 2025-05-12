<?php

namespace App\Enums;
enum OrderStatusEnum: string
{
    case FILLED = 'filled';
    case PARTIALLY_FILLED = 'partially_filled';
    case PENDING = 'pending';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
}
