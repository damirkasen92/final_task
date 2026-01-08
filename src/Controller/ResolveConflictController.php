<?php
namespace App\Controller;

use App\Entity\Inventory;
use App\Form\InventoryConflictType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ResolveConflictController extends BaseController
{
    #[Route('/resolve/inventory/{id}/conflict', name: 'resolve_inventory_conflict', methods: ['POST'])]
    public function resolveInventoryConflict(Request $request, Inventory $inventory, EntityManagerInterface $em)
    {
        $form = $this->createForm(InventoryConflictType::class, $inventory)
            ->submit($request->request->all(), false);

        dd($form->getErrors(true));

        if ($form->isValid()) {
            $em->flush();

            return $this->json($this->jsonSuccessData);
        }

        return $this->json($this->jsonErrorData);
    }

}
