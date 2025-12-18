<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: [
    'en' => '/inventory',
    'ru' => '/ru/inventory',
])]
class InventoryController extends BaseController
{
    #[Route('/items', name: 'inventory_items')]
    public function items(): Response
    {
        return $this->render('inventory/items.html.twig');
    }

    #[Route('/')]
    public function index(): Response
    {
        return $this->render('inventory/index.html.twig');
    }

    public function createInventory(int $id): Response
    {

    }
}
