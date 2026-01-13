<?php

namespace App\Controller\Inventory;

use App\Controller\BaseController;
use App\Entity\Inventory;
use App\Form\InventoryConflictResolvingType;
use App\Form\InventoryConflictType;
use App\Service\Inventory\Conflict\ConflictService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InventoryConflictController extends BaseController
{
    #[Route('/inventory/{id}/conflict', name: 'inventory_conflict', methods: ['POST'])]
    public function conflictResolve(Request $request, Inventory $inventory, ConflictService $conflictService): Response
    {
        $form = $this->createForm(InventoryConflictResolvingType::class, $inventory)
            ->submit(
                $request->request->all('inventory_conflict'),
                false
            );

        if ($form->isValid()) {
            $conflictService->updateInventory();
            return $this->json($this->jsonSuccessData);
        }

        return $this->json([
            ...$this->jsonErrorData,
            'errors' => $this->getErrors($form),
        ]);
    }
}
