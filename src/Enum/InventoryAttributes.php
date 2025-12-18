<?php

namespace App\Enum;

enum InventoryAttributes: string
{
    case EDIT = 'EDIT';
    case DELETE = 'DELETE';
    case MANAGE_ACCESS = 'MANAGE_ACCESS';

    case TAB_ITEMS = 'TAB_ITEMS';
    case TAB_DISCUSSION = 'TAB_DISCUSSION';
    case TAB_SETTINGS = 'TAB_SETTINGS';
    case TAB_CUSTOM_NUMBERS = 'TAB_CUSTOM_NUMBERS';
    case TAB_ACCESS = 'TAB_ACCESS';
    case TAB_STATISTICS = 'TAB_STATISTICS';
}
