<?php
namespace App\Repository;

use App\Entity\Inventory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inventory>
 */
class InventoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inventory::class);
    }

    public function getInventories(int $userId, array $criteria = [])
    {
        return $this->findBy([
            'owner' => $userId,
            ...$criteria,
        ]);
    }

    public function getWriteAccessInventories(int $userId, array $inventoryIds = [])
    {
        $qb = $this->createQueryBuilder('i');

        $qb->select('i')
            ->join('i.writers', 'u')
            ->where('u.id = :user_id')
            ->setParameter('user_id', $userId);

        if (\count($inventoryIds) > 0) {
            $qb
                ->andWhere($qb->expr()->in('i.id', ':ids'))
                ->setParameter('ids', $inventoryIds);
        }

        return $qb->getQuery()->getResult();
    }

    public function getWriteAccessOwnerIds(int $userId)
    {
        $qb = $this->createQueryBuilder('i');

        $qb->select('owner.id')
            ->distinct()
            ->join('i.owner', 'owner')
            ->join('i.writers', 'u')
            ->where('u.id = :user_id')
            ->setParameter('user_id', $userId);

        return $qb->getQuery()->getSingleColumnResult();
    }
}
