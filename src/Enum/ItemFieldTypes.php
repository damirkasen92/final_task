<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ItemFieldTypes: string implements TranslatableInterface
{
    case string = 'string';
    case text = 'text';
    case integer = 'integer';
    case link = 'link';
    case bool = 'bool';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans('item_fields.'.$this->name, locale: $locale);
    }
}
