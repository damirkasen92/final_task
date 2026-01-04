<?php
namespace App\Controller\Inventory;

use App\Controller\BaseController;
use App\Entity\Inventory;
use App\Enum\InventoryAttributes;
use App\Enum\JsonStatuses;
use App\Form\InventoryFormType;
use App\Service\Google\GoogleStorageService;
use App\Service\Inventory\InventoryService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventorySettingsController extends BaseController
{
    #[Route('/inventory/{id}/settings', name: 'show_inventory_settings', methods: ['GET'])]
    public function showInventorySettings(Inventory $inventory, GoogleStorageService $googleStorageService)
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        return $this->render('inventory/includes/settings.html.twig', [
            'form'           => $this->createForm(
                InventoryFormType::class,
                $inventory,
                [
                    'update' => false,
                ]
            ),
            'inventory'      => $inventory,
            'inventoryImage' => $inventory->getImageUrl() ? $googleStorageService
                ->getFileUrl($inventory->getImageUrl()) : null,
        ]);
    }

    #[Route('/inventory/{id}/settings', name: 'update_inventory_settings', methods: ['POST'])]
    public function updateInventory(Request $request, Inventory $inventory, InventoryService $inventoryService): Response
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $form = $this->createForm(InventoryFormType::class, $inventory, [
            'default_image' => $inventory->getImageUrl(),
            'update'        => false,
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inventoryService->updateInventory();

            return $this->json($this->jsonSuccessData);
        }

        return $this->json([
            ...$this->jsonErrorData,
            'errors' => $this->getErrors($form),
        ], Response::HTTP_BAD_REQUEST);
    }
}
