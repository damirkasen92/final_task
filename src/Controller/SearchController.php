<?php
namespace App\Controller;

use App\Repository\InventoryRepository;
use Meilisearch\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends BaseController
{
    #[Route('/inventories/search', name: 'inventories_search')]
    public function search(Request $request, Client $client, InventoryRepository $inventoryRepository): JsonResponse
    {
        $query   = $request->query->get('q', '');
        $index   = $client->index('inventories');
        $results = $index->search($query, ['limit' => 10]);

        $inventories = $inventoryRepository->findBy([
            'id' => array_column($results->getHits(), 'id'),
        ]);

        dd($inventories);

        return $this->json();
    }

}
