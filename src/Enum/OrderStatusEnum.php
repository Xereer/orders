<?php

namespace App\Enum;

enum OrderStatusEnum: int
{
    case Created = 1;

    case InWork = 2;

    case Fulfilled = 3;

    case Rejected = 4;

    case Deleted = 5;
}
