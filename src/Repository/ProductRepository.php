<?php

namespace App\Repository;

use App\Entity\Product;
use App\Enum\SubmissionStatus;
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
    public function search(string $query): array
    {
        $q = '%' . mb_strtolower(trim($query)) . '%';

        return $this->createQueryBuilder('p')
            ->join('p.productType', 't')
            ->where('p.status = :status')
            ->andWhere('LOWER(p.productName) LIKE :q OR LOWER(p.technicalName) LIKE :q OR LOWER(t.type) LIKE :q')
            ->setParameter('status', SubmissionStatus::Approved)
            ->setParameter('q', $q)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
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
