<?php

namespace App\Enum;

enum UserStatusEnum: int
{
    case Active = 1;

    case Inactive = 2;

    case Deleted = 3;
}
