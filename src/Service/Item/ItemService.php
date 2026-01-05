<?php
namespace App\Service\Item;

use App\Dto\ItemsDto;
use App\Entity\Inventory;
use App\Entity\Item;
use App\Enum\ItemFieldTypes;
use App\Repository\ItemFieldRepository;
use App\Repository\ItemRepository;
use App\Service\Google\GoogleStorageService;
use App\Service\Inventory\InventoryService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ItemService
{
    public function __construct(
        private EntityManagerInterface $em,
        private InventoryService $inventoryService,
        private ItemRepository $itemRepository,
        private ItemFieldRepository $itemFieldRepository,
        private PaginatorInterface $paginator,
        private GoogleStorageService $googleStorageService
    ) {
    }

    public function updateItem(): void
    {
        $this->em->flush();
    }

    public function deleteItems(array $itemIds, Inventory $inventory): void
    {
        $items = $this->itemRepository->findBy([
            'id'        => $itemIds,
            'inventory' => $inventory,
        ]);

        foreach ($items as $item) {
            $this->em->remove($item);
        }

        $this->em->flush();
    }

    public function getItems(Inventory $inventory, int $page = 1): ItemsDto
    {
        $pagination = $this->getPagination($inventory, $page);
        $itemFields = $this->getItemFields($inventory);
        $itemSlots  = $this->getItemSlots($itemFields);

        $this->setUrls($pagination, $itemSlots);

        return new ItemsDto(
            $pagination,
            $itemSlots,
            $this->getItemFieldNames($itemFields)
        );
    }

    private function getItemFields(Inventory $inventory): array
    {
        return $this->itemFieldRepository->findBy([
            'inventory'   => $inventory,
            'isDisplayed' => true,
        ], [
            'orderIndex' => 'asc',
        ]);
    }

    private function getPagination(Inventory $inventory, int $page): PaginationInterface
    {
        $queryBuilder = $this->itemRepository->createQueryBuilder('i')
            ->where('i.inventory = :inventory')
            ->setParameter('inventory', $inventory);

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            10
        );
    }

    private function getItemSlots(array $itemFields): array
    {
        return array_reduce($itemFields, function ($carry, $itemField) {
            $carry[$itemField->getSlot()] = $itemField->getSlot();

            return $carry;
        }, []);
    }

    private function getItemFieldNames(array $itemFields): array
    {
        return array_map(fn($itemField) => $itemField->getTitle(), $itemFields);
    }

    private function setUrls(PaginationInterface $pagination, array $itemSlots): void
    {
        /** @var Item $item */
        // maximum number of slots are constant, so it will be O(n)
        foreach ($pagination->getItems() as $item) {
            foreach ($itemSlots as $itemSlot) {
                $getter = 'get' . ucfirst($itemSlot);
                $setter = 'set' . ucfirst($itemSlot);

                if (
                    str_contains($itemSlot, ItemFieldTypes::link->value)
                    && $item->{$getter}() !== null
                ) {
                    $item->{$setter}(
                        $this->googleStorageService->getFileUrl(
                            $item->{$getter}()
                        )
                    );
                }
            }
        }
    }
}
