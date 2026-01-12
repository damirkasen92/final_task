<?php
namespace App\Controller\Inventory;

use App\Controller\BaseController;
use App\Entity\Inventory;
use App\Enum\InventoryAttributes;
use App\Enum\JsonStatuses;
use App\Form\InventoryType;
use App\Service\Inventory\InventoryService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventoryAccessController extends BaseController
{
    #[Route('/inventory/{id}/access', name: 'show_access_settings', methods: ['GET'])]
    public function showAccessSettings(Inventory $inventory): Response
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $form = $this->createForm(InventoryType::class, $inventory, [
            'update' => true,
            'create' => false,
        ]);

        return $this->render('inventory/includes/access.html.twig', [
            'form' => $form,
            'inventory' => $inventory,
        ]);
    }

    #[Route('/inventory/{id}/access', name: 'update_access_settings', methods: ['POST'])]
    public function updateAccessSettings(Inventory $inventory, Request $request, InventoryService $inventoryService): Response
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $form = $this->createForm(InventoryType::class, $inventory, [
            'update' => true,
            'create' => false,
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inventoryService->updateInventory($inventory);

            return $this->json($this->jsonSuccessData);
        }

        return $this->json([
            ...$this->jsonErrorData,
            'errors' => $this->getErrors($form),
        ], Response::HTTP_BAD_REQUEST);
    }
}
