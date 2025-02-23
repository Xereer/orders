<?php

namespace App\Enum;

enum PrivilegeEnum: int
{
    case PUBLIC_ACCESS = 1;

    case ROLE_USER = 2;

    case ROLE_ADMIN = 3;
}
