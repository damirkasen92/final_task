<?php
namespace App\Controller\Inventory;

use App\Controller\BaseController;
use App\Entity\Inventory;
use App\Entity\ItemField;
use App\Enum\InventoryAttributes;
use App\Form\ItemFieldType;
use App\Repository\ItemFieldRepository;
use App\Service\Inventory\ItemField\ItemFieldService;
use Src\Exception\ItemFieldServiceException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventoryItemFieldController extends BaseController
{
    #[Route('/inventory/{id}/create/field', name: 'show_item_field', methods: ['GET'])]
    public function showItemField(Request $request, Inventory $inventory)
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        return $this->render('inventory/includes/item_fields.html.twig', [
            'form' => $this->createForm(ItemFieldType::class),
            'editForm' => $this->createForm(ItemFieldType::class),
            'inventory' => $inventory,
        ]);
    }

    #[Route('/inventory/{id}/create/fields', name: 'show_item_fields', methods: ['GET'])]
    public function showItemFields(Inventory $inventory, ItemFieldRepository $itemFieldRepository)
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        return $this->render('inventory/includes/ui/item_fields_table.html.twig', [
            'inventory' => $inventory,
            'itemFields' => $itemFieldRepository->findBy([
                'inventory' => $inventory,
            ], [
                'orderIndex' => 'asc',
            ]),
        ]);
    }

    #[Route('/inventory/{id}/edit/field/{itemFieldId}', name: 'edit_item_fields', methods: ['POST'])]
    public function editItemFields(
        Inventory $inventory,
        #[MapEntity(id: 'itemFieldId')]
        ItemField $itemField,
        Request $request,
        ItemFieldService $itemFieldService
    ) {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $form = $this->createForm(ItemFieldType::class, $itemField)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $itemFieldService->updateItemField($itemField, $inventory);
                return $this->json($this->jsonSuccessData);
            } catch (ItemFieldServiceException $e) {
                return $this->json([
                    ...$this->jsonErrorData,
                    'errors' => $e->getMessage(),
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->json([
            ...$this->jsonErrorData,
            'errors' => $this->getErrors($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/inventory/{id}/create/field', name: 'create_item_field', methods: ['POST'])]
    public function createItemField(Request $request, Inventory $inventory, ItemFieldService $itemFieldService)
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $form = $this->createForm(ItemFieldType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $itemFieldService->createItemField($form->getData(), $inventory);
                return $this->json($this->jsonSuccessData, Response::HTTP_CREATED);
            } catch (ItemFieldServiceException $e) {
                return $this->json([
                    ...$this->jsonErrorData,
                    'errors' => $e->getMessage(),
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->json([
            ...$this->jsonErrorData,
            'errors' => $this->getErrors($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/inventory/{id}/delete/fields', name: 'delete_item_fields', methods: ['DELETE'])]
    public function deleteItemFields(Request $request, Inventory $inventory, ItemFieldService $itemFieldService)
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $itemFieldIds = $request->request->all('itemFieldIds');
        $itemFieldService->deleteItemFields($itemFieldIds);

        return $this->json($this->jsonSuccessData);
    }

    #[Route('/inventory/{id}/reorder/field', name: 'inventory_field_reorder', methods: ['PATCH'])]
    public function reorderField(Request $request, Inventory $inventory, ItemFieldService $itemFieldService)
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $order = $request->request->all('order');
        $itemFieldService->setOrder($order, $inventory);

        return $this->json($this->jsonSuccessData);
    }
}
