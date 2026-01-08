<?php

namespace App\Service\Inventory;

use App\Enum\IndexesEnum;
use Meilisearch\Client;
use Meilisearch\Exceptions\ApiException;

class MeiliSearch implements SearchEngineInterface
{
    private const int SEARCH_LIMIT = 10;

    public function __construct(
        private Client $client,
    ) {
    }

    public function search(string $query, array $params = []): array
    {
        if (!$query) {
            return [];
        }

        try {
            $index = $this->client->getIndex(IndexesEnum::inventories->value);
        } catch (ApiException $e) {
            if ($e->errorCode === 'index_not_found') {
                $index = $this->client->createIndex(IndexesEnum::inventories->value, ['primaryKey' => 'id']);
            }
        }

        $index->updateSettings([
            'filterableAttributes' => ['user_id']
        ]);

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
