<?php
namespace App\Controller\Inventory;

use App\Controller\BaseController;
use App\Entity\Inventory;
use App\Enum\InventoryAttributes;
use App\Exception\InventoryServiceException;
use App\Form\ItemFieldType;
use App\Repository\ItemFieldRepository;
use App\Service\Inventory\InventoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventoryItemFieldController extends BaseController
{
    #[Route('/inventory/{id}/create/field', name: 'show_item_field', methods: ['GET'])]
    public function showItemField(Request $request, Inventory $inventory, ItemFieldRepository $itemFieldRepository)
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        return $this->render('inventory/includes/item_field.html.twig', [
            'form'       => $this->createForm(ItemFieldType::class),
            'inventory'  => $inventory,
        ]);
    }

    #[Route('/inventory/{id}/create/fields', name: 'show_item_fields', methods: ['GET'])]
    public function showItemFields(Inventory $inventory, ItemFieldRepository $itemFieldRepository) {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        return $this->render('inventory/includes/ui/item_fields.html.twig', [
            'inventory'  => $inventory,
            'itemFields' => $itemFieldRepository->findBy([
                'inventory' => $inventory,
            ]),
        ]);
    }

    #[Route('/inventory/{id}/create/field', name: 'create_item_field', methods: ['POST'])]
    public function createItemField(Request $request, Inventory $inventory, InventoryService $inventoryService)
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $form = $this->createForm(ItemFieldType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $inventoryService->createItemField($form->getData(), $inventory);
            } catch (InventoryServiceException $e) {
                return $this->json([
                     ...$this->jsonErrorData,
                    'errors' => $e->getMessage(),
                ]);
            }

            return $this->json($this->jsonSuccessData, Response::HTTP_CREATED);
        }

        return $this->json([
            ...$this->jsonErrorData,
            'errors' => $this->getErrors($form)
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/inventory/{id}/delete/fields', name: 'delete_item_fields', methods: ['DELETE'])]
    public function deleteItemFields(Request $request, ItemFieldRepository $itemFieldRepository, EntityManagerInterface $em) {
        $itemFieldIds = $request->request->all('itemFieldIds');

        $itemFields = $itemFieldRepository->findBy([
            'id' => $itemFieldIds
        ]);

        foreach ($itemFields as $itemField) {
            $em->remove($itemField);
        }

        $em->flush();

        return $this->json($this->jsonSuccessData);
    }

    #[Route('/inventory/{id}/reorder/field', name: 'inventory_field_reorder', methods: ['PATCH'])]
    public function reorderField(Request $request, Inventory $inventory, InventoryService $inventoryService)
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $order = $request->request->all('order');
        $inventoryService->setOrder($order, $inventory);

        return $this->json($this->jsonSuccessData);
    }
}
