<?php
namespace App\Controller;

use App\Repository\InventoryRepository;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends BaseController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(InventoryRepository $inventoryRepository)
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(InventoryRepository $inventoryRepository)
    {
        dd($inventoryRepository->getWriteAccessInventories($this->getUser()->getId()));

        return $this->json(['status' => 'test']);
    }
}
