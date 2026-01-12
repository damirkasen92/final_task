<?php

declare(strict_types=1);

namespace App\Service\Inventory;

use App\Dto\Result;
use App\Entity\Inventory;
use App\Repository\InventoryRepository;
use App\Service\FileStorage\FileStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventoryService
{
    private ?Inventory $inventory;

    public function __construct(
        private EntityManagerInterface $em,
        private InventoryRepository $inventoryRepository,
        private FileStorageInterface $fileStorage,
        private Security $security,
        private TranslatorInterface $translator,
        private SerializerInterface $serializer,
        private SearchEngineInterface $searchEngine
    ) {
    }

    public function getInventoriesWithImage(int $userId, string $query = '')
    {
        $criteria = [];

        if ($query) {
            $criteria = [
                'id' => $this->searchEngine->search($query, [
                    'filter' => 'user_id = ' . $userId
                ])
            ];
        }

        $inventories = $this->inventoryRepository->getInventories(
            $userId,
            $criteria
        );

        $this->setInventoriesImage($inventories);

        return $inventories;
    }

    public function getWriteAccessInventoriesWithImages(int $userId, string $query = '')
    {
        $searchIds = [];

        if ($query) {
            $ownerIds = $this->inventoryRepository->getWriteAccessOwnerIds($userId);
            $searchIds = $ownerIds ?
                $this->searchEngine->search($query, [
                    'filter' => 'user_id IN [' . implode(', ', $ownerIds) . ']'
                ])
                : [];
        }

        if (!$searchIds && $query)
            return [];

        $inventories = $this->inventoryRepository->getWriteAccessInventories(
            $userId,
            $searchIds
        );

        $this->setInventoriesImage($inventories);

        return $inventories;
    }

    private function setInventoriesImage(array $inventories): void
    {
        /** @var Inventory $inventory */
        foreach ($inventories as $inventory) {
            $imageName = $inventory->getImageUrl();

            if ($imageName) {
                $inventory->setImageUrl(
                    $this->fileStorage->getFileUrl($imageName)
                );
            }
        }
    }

    public function createInventory(Inventory $inventory): void
    {
        $inventory->setCustomIdFormat([
            [
                'type' => 'seq',
                'value' => '',
            ],
        ]);

        $this->em->persist($inventory);
        $this->em->flush();
    }

    public function updateInventory(?Inventory $inventory): Result
    {
        try {
            $this->em->flush();

            return Result::ok();
        } catch (OptimisticLockException $e) {
            if ($inventory)
                $this->em->detach($inventory);

            return Result::conflict(
                $this->translator->trans('conflict_resolve.title')
            );
        }
    }

    public function handleAutosave(Inventory $inventory, ?UploadedFile $file): array
    {
        $json = [];

        if ($file) {
            $fileStorageName = $this->fileStorage->upload($file);
            $inventory->setImageUrl($fileStorageName);
            $json['imageUrl'] = $this->fileStorage->getFileUrl($fileStorageName);
        }

        $this->em->flush();

        return $json;
    }

    public function deleteInventories(array $inventoryIds): void
    {
        $inventories = $this->inventoryRepository
            ->findBy(['id' => $inventoryIds]);

        foreach ($inventories as $inventory) {
            $this->em->remove($inventory);
        }

        $this->em->flush();
    }

    public function saveCustomId(Inventory $inventory, array $data)
    {
        $inventory->setCustomIdFormat($data['elements']);
        $this->em->flush();
    }
}
