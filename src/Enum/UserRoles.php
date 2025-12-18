<?php

namespace App\Enum;

enum UserRoles: string
{
    case ADMIN = 'ROLE_ADMIN';
//    case CREATOR = 'ROLE_CREATOR';
    case USER = 'ROLE_USER';
}
