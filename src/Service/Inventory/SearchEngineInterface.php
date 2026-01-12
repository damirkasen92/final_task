<?php

namespace App\Service\Inventory;

interface SearchEngineInterface
{
    public function search(string $query, string $indexName, array $params): array;
}
