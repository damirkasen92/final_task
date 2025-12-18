<?php

namespace App\Enum;

enum InventoryFieldTypes: string
{
    case SHORT_TEXT = 'SHORT_TEXT';
    case TEXT = 'TEXT';
    case NUMERIC = 'NUMERIC';
    case DOCUMENT_IMAGE = 'DOCUMENT_IMAGE';
    case BOOLEAN = 'BOOLEAN';
}
