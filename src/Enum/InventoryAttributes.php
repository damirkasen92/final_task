<?php

namespace App\Enum;

enum InventoryAttributes: string
{
    case EDIT = 'INVENTORY_EDIT';
    case DELETE = 'INVENTORY_DELETE';
    case MANAGE_ACCESS = 'INVENTORY_MANAGE_ACCESS';
}
