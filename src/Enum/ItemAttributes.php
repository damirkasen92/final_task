<?php

namespace App\Enum;

enum ItemAttributes: string
{
    case VIEW = 'ITEM_VIEW';
    case ADD = 'ITEM_ADD';
    case EDIT = 'ITEM_EDIT';
    case DELETE = 'ITEM_DELETE';
}
