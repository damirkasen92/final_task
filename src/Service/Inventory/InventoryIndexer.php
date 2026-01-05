<?php
namespace App\Service\Inventory;

use App\Entity\Inventory;
use Meilisearch\Client;

class InventoryIndexer
{
    private const string INDEX_NAME = 'inventories';

    public function __construct(
        private Client $client
    ) {
    }

    public function index(Inventory $inventory): void
    {
        $document = [
            'id'          => $inventory->getId(),
            'title'       => $inventory->getTitle(),
            'description' => $inventory->getDescription(),
        ];

        $this->client->index(self::INDEX_NAME)->addDocuments([$document]);
    }

    public function deleteIndex(Inventory $inventory)
    {
        $this->client->index(self::INDEX_NAME)->deleteDocument($inventory->getId());
    }
}
