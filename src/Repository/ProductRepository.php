<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findLastApproved(): ?Product
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', \App\Enum\SubmissionStatus::Approved)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return Product[] */
    public function findApprovedByCategory(array $typeNames): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.productType', 't')
            ->where('p.status = :status')
            ->andWhere('t.type IN (:types)')
            ->setParameter('status', \App\Enum\SubmissionStatus::Approved)
            ->setParameter('types', $typeNames)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
