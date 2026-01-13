<?php

namespace App\Service\Item;

use App\Entity\Item;
use App\Enum\IndexesEnum;
use Meilisearch\Client;

class ItemIndexer
{
    public function __construct(
        private Client $client
    ) {
    }

    public function index(Item $item): void
    {
        $document = [
            'id' => $item->getId(),
            'custom_id' => $item->getCustomId(),
            'inventory_id' => $item->getInventory()->getId(),
            'user_id' => $item->getCreatedBy()->getId(),
        ];

        $this->client->index(IndexesEnum::items->value)->addDocuments([$document]);
    }

    public function deleteIndex(Item $item)
    {
        $this->client->index(IndexesEnum::items->value)->deleteDocument($item->getId());
    }
}
