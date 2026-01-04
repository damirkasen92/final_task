<?php

namespace App\Enum;

enum UserRoles: string
{
    case ADMIN = 'ROLE_ADMIN';
    case USER = 'ROLE_USER';
}
