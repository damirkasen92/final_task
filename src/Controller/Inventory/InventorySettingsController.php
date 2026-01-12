<?php
namespace App\Controller\Inventory;

use App\Controller\BaseController;
use App\Entity\Inventory;
use App\Enum\InventoryAttributes;
use App\Form\InventoryConflictType;
use App\Form\InventoryType;
use App\Repository\InventoryRepository;
use App\Service\FileStorage\FileStorageInterface;
use App\Service\Inventory\InventoryService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventorySettingsController extends BaseController
{
    #[Route('/inventory/{id}/settings', name: 'show_inventory_settings', methods: ['GET'])]
    public function showInventorySettings(Inventory $inventory, FileStorageInterface $fileStorage)
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $imageUrl = $inventory->getImageUrl();

        return $this->render('inventory/includes/settings.html.twig', [
            'form' => $this->createForm(
                InventoryType::class,
                $inventory,
                [
                    'update' => false,
                ]
            ),
            'inventory' => $inventory,
            'inventoryImage' => $imageUrl ? $fileStorage
                ->getFileUrl($imageUrl) : null,
        ]);
    }

    #[Route('/inventory/{id}/settings', name: 'update_inventory_settings', methods: ['POST'])]
    public function updateInventory(
        Request $request,
        Inventory $inventory,
        InventoryService $inventoryService,
        InventoryRepository $inventoryRepository,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $form = $this->createForm(InventoryType::class, $inventory, [
            'update' => false,
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $inventoryService->updateInventory($inventory);

            if ($result->getStatus() === 'conflict') {
                $html = $this->renderView('inventory/merge.html.twig', [
                    'form' => $this->createForm(InventoryConflictType::class, null, [
                        'currentData' => $inventory,
                        'dbData' => $inventoryRepository->find($inventory->getId()),
                    ])
                        ->createView(),
                ]);

                return $this->json([
                    'form' => $html
                ], Response::HTTP_CONFLICT);
            }


            return $this->json($this->jsonSuccessData);
        }

        return $this->json([
            ...$this->jsonErrorData,
            'errors' => $this->getErrors($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/inventory/{id}/settings/autosave', name: 'autosave_inventory_settings', methods: ['POST'])]
    public function autosave(Inventory $inventory, Request $request, InventoryService $inventoryService)
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $form = $this->createForm(InventoryType::class, $inventory)
            ->submit($request->request->all(), false);

        if ($form->isValid()) {
            $json = $inventoryService->handleAutosave(
                $inventory,
                $request->files->get('imageUrl')
            );

            return $this->json([...$this->jsonSuccessData, ...$json]);
        }

        return $this->json($this->jsonErrorData);
    }
}
