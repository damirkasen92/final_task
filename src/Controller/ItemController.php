<?php
namespace App\Controller;

use App\Entity\Inventory;
use App\Entity\Item;
use App\Form\ItemType;
use App\Service\Item\ItemService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ItemController extends BaseController
{
    #[Route('/inventory/{id}/item/create', name: 'show_create_item', methods: ['GET'])]
    public function showCreateItem(Inventory $inventory)
    {
        $form = $this->createForm(ItemType::class, null, [
            'inventory' => $inventory->getId(),
        ]);

        return $this->render('item/create.html.twig', [
            'form'      => $form,
            'inventory' => $inventory,
        ]);
    }

    #[Route('/inventory/{id}/item/create', name: 'create_item', methods: ['POST'])]
    public function createItem(Request $request, Inventory $inventory, EntityManagerInterface $em)
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item, [
            'inventory' => $inventory->getId(),
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inventory->addItem($item);

            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('show_items', [
                'id' => $inventory->getId(),
            ]);
        }

        return $this->render('item/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/item/{id}/update', name: 'show_update_item', methods: ['GET'])]
    public function showUpdateItem(Request $request, Item $item)
    {
        $form = $this->createForm(ItemType::class, $item, [
            'inventory' => $item->getInventory(),
        ]);

        return $this->render('item/edit.html.twig', [
            'form' => $form,
            'item' => $item,
        ]);
    }

    #[Route('/item/{id}/update', name: 'update_item', methods: ['POST'])]
    public function updateItem(Request $request, Item $item, ItemService $itemService)
    {
        $form = $this->createForm(ItemType::class, $item, [
            'inventory' => $item->getInventory(),
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $itemService->updateItem();

            return $this->redirectToRoute('show_items', [
                'id' => $item->getInventory()->getId(),
            ]);
        }

        return $this->render('item/edit.html.twig', [
            'form'      => $form,
            'inventory' => $item->getInventory(),
            'item'      => $item,
        ]);
    }

    #[Route('/inventory/{id}/items', name: 'delete_items', methods: ['DELETE'])]
    public function deleteItems(Request $request, Inventory $inventory, ItemService $itemService)
    {
        $itemIds = $request->request->all('item_ids');
        $itemService->deleteItems($itemIds, $inventory);

        return $this->json($this->jsonSuccessData);
    }

    #[Route('/inventory/{id}/items', name: 'show_items', methods: ['GET'])]
    public function showItems(
        Inventory $inventory,
        ItemService $itemService
    ) {
        $itemsDto = $itemService->getItems($inventory);

        return $this->render('item/index.html.twig', [
            'inventory'      => $inventory,
            'pagination'     => $itemsDto->pagination,
            'itemSlots'      => $itemsDto->itemSlots,
            'itemFieldNames' => $itemsDto->itemFieldNames,
        ]);
    }
}
