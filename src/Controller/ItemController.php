<?php

namespace App\Controller;

use App\Entity\Inventory;
use App\Entity\Item;
use App\Entity\ItemFieldValue;
use App\Enum\ItemFieldTypes;
use App\Form\ItemFormType;
use App\Form\ItemType;
use App\Service\Inventory\InventoryService;
use App\Service\Item\ItemService;
use BcMath\Number;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
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
            'form' => $form,
            'inventory' => $inventory
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

            return $this->redirectToRoute('show_inventory', [
                'id' => $inventory->getId()
            ]);
        }

        return $this->render('item/create.html.twig', [
            'form' => $form,
        ]);
    }
}
