<?php

namespace App\Repository;

use App\Entity\ModificationHistory;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModificationHistory>
 */
class ModificationHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModificationHistory::class);
    }

    /** @return ModificationHistory[] */
    public function findByProduct(Product $product): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.product = :product')
            ->setParameter('product', $product)
            ->orderBy('h.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
