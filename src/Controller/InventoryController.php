<?php
namespace App\Controller;

use App\Dto\InventoryIdsDto;
use App\Entity\Inventory;
use App\Entity\User;
use App\Enum\InventoryAttributes;
use App\Form\InventoryFormType;
use App\Service\Inventory\InventoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('IS_AUTHENTICATED')]
class InventoryController extends BaseController
{
    #[Route('/inventories', name: 'inventories', methods: ['GET'])]
    public function inventories(): Response
    {
        return $this->render('inventory/index.html.twig', [
            'form' => $this->createForm(
                InventoryFormType::class
            ),
        ]);
    }

    #[Route('/inventories/list', name: 'inventories_list', methods: ['GET'])]
    public function inventoriesList(InventoryService $inventoryService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('inventory/inventories_list.html.twig', [
            'inventories' => $inventoryService
                ->getInventoriesWithImage($user->getId()),
        ]);
    }

    #[Route('/inventory/{id}', name: 'show_inventory', methods: ['GET'])]
    public function showInventory(Inventory $inventory)
    {
        return $this->render('inventory/inventory.html.twig', [
            'inventory' => $inventory,
        ]);
    }

    #[Route('/inventory/create', name: 'create_inventory', methods: ['POST'])]
    public function createInventory(Request $request, InventoryService $inventoryService): Response
    {
        $form = $this->createForm(InventoryFormType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inventory = $form->getData();
            $inventoryService->createInventory($inventory);

            return $this->json($this->jsonSuccessData, Response::HTTP_CREATED);
        }

        return $this->json([
             ...$this->jsonErrorData,
            'errors' => $this->getErrors($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/inventory/delete', name: 'delete_inventory', methods: ['DELETE'])]
    public function deleteInventory(Request $request, ValidatorInterface $validator, InventoryService $inventoryService): Response
    {
        $dto = InventoryIdsDto::fromRequest($request);
        $this->denyAccessUnlessGranted(InventoryAttributes::DELETE->value, $dto);

        if ($validator->validate($dto)->count() > 0) {
            return $this->json($this->jsonErrorData, Response::HTTP_BAD_REQUEST);
        }

        $inventoryService->deleteInventories($dto->ids);

        return $this->json($this->jsonSuccessData, Response::HTTP_NO_CONTENT);
    }
}
