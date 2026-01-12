<?php

namespace App\Service\Inventory;

use Meilisearch\Client;

class MeiliSearch implements SearchEngineInterface
{
    private const int SEARCH_LIMIT = 10;

    public function __construct(
        private Client $client,
    ) {
    }

    public function search(string $query, string $indexName, array $params = []): array
    {
        if (!$query) {
            return [];
        }

        $index = $this->client->getIndex($indexName);

        $results = $index->search(
            $query,
            [
                'limit' => self::SEARCH_LIMIT,
                ...$params
            ]
        )->getHits();

        return array_column($results, 'id');
    }
}
