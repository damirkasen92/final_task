<?php

namespace App\Service\Inventory;

interface SearchEngineInterface
{
    public function search(string $query, array $params): array;
}
