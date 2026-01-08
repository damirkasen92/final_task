<?php
namespace App\Service\Inventory;

use App\Entity\Inventory;
use App\Enum\IndexesEnum;
use Meilisearch\Client;

class InventoryIndexer
{
    public function __construct(
        private Client $client
    ) {
    }

    public function index(Inventory $inventory): void
    {
        $document = [
            'id' => $inventory->getId(),
            'title' => $inventory->getTitle(),
            'description' => $inventory->getDescription(),
            'user_id' => $inventory->getOwner()->getId()
        ];

        $this->client->index(IndexesEnum::inventories->value)->addDocuments([$document]);
    }

    public function deleteIndex(Inventory $inventory)
    {
        $this->client->index(IndexesEnum::inventories->value)->deleteDocument($inventory->getId());
    }
}
