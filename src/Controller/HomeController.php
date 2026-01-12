<?php
namespace App\Controller;

use App\Form\InventoryConflictType;
use App\Repository\InventoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends BaseController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(InventoryRepository $inventoryRepository)
    {
        return $this->render('home/index.html.twig', [
            'last_inventories' => $inventoryRepository->getLatestInventories(),
            'top_inventories' => $inventoryRepository->getTopInventories(),
        ]);
    }

    #[Route('/test', name: 'test', methods: ['GET', 'POST'])]
    public function test(Request $request, InventoryRepository $inventoryRepository)
    {
        return $this->render('home/index.html.twig');
    }
}
