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
        return $this->render('home/index.html.twig');
    }

    #[Route('/test', name: 'test', methods: ['GET', 'POST'])]
    public function test(Request $request, InventoryRepository $inventoryRepository)
    {
        // dd($request->request->all());

        $form = $this->createForm(InventoryConflictType::class, null, [
            'currentData' => $inventoryRepository->find(13),
            'dbData' => $inventoryRepository->find(15),
        ]);

        return $this->render('inventory/merge.html.twig', [
            'form' => $form
        ]);
    }
}
