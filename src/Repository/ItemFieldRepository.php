<?php
namespace App\Repository;

use App\Entity\Inventory;
use App\Entity\ItemField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ItemField>
 */
class ItemFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemField::class);
    }

    public function getMaxOrderIndex(Inventory $inventory): int
    {
        return (int) $this->createQueryBuilder('f')
            ->select('MAX(f.orderIndex)')
            ->where('f.inventory = :inventory')
            ->setParameter('inventory', $inventory->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
