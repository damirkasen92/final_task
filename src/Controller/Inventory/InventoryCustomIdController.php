<?php
namespace App\Controller\Inventory;

use App\Controller\BaseController;
use App\Entity\Inventory;
use App\Enum\InventoryAttributes;
use App\Exception\CustomIdGeneratorException;
use App\Form\CustomIdType;
use App\Service\Inventory\InventoryService;
use App\Service\Item\CustomIdGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventoryCustomIdController extends BaseController
{
    #[Route('/inventory/{id}/get/customId', name: 'get_custom_id', methods: ['GET'])]
    public function getCustomId(CustomIdGenerator $customIdGenerator, Inventory $inventory): Response
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $format = $inventory->getCustomIdFormat() ?? [];

        return $this->render('inventory/includes/custom_id.html.twig', [
            'form'      => $this->createForm(CustomIdType::class, [
                'elements' => $format,
            ]),
            'inventory' => $inventory,
            'customId'  => $customIdGenerator->generate($format),
        ]);
    }

    #[Route('/inventory/{id}/set/customId', name: 'set_custom_id', methods: ['POST'])]
    public function setCustomId(Request $request, CustomIdGenerator $customIdGenerator, Inventory $inventory, InventoryService $inventoryService): Response
    {
        $this->denyAccessUnlessGranted(InventoryAttributes::EDIT->value, $inventory);

        $form = $this->createForm(CustomIdType::class)
            ->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $inventoryService->saveCustomId($inventory, $form->getData());

                return $this->json([
                     ...$this->jsonSuccessData,
                    'customId' => $customIdGenerator->generate($inventory->getCustomIdFormat()),
                ]);
            }

            return $this->json([
                 ...$this->jsonErrorData,
                'error' => (string) $form->getErrors(true),
            ], Response::HTTP_BAD_REQUEST);
        } catch (CustomIdGeneratorException $e) {
            return $this->json([
                 ...$this->jsonErrorData,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
