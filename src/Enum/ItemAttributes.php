<?php

namespace App\Enum;

enum ItemAttributes: string
{
    case VIEW = 'VIEW';
    case ADD = 'ADD';
    case EDIT = 'EDIT';
    case DELETE = 'DELETE';
}
