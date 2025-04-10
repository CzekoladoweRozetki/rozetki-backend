<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Repository;

use App\Common\Infrastructure\Repository\DoctrineRepositoryTrait;
use App\PriceList\Domain\Entity\Price;
use App\PriceList\Domain\Entity\PriceList;
use App\PriceList\Domain\Repository\PriceRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Price>
 */
class DoctrinePriceRepository extends ServiceEntityRepository implements PriceRepository
{
    /**
     * @use DoctrineRepositoryTrait<Price>
     */
    use DoctrineRepositoryTrait;

    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Price::class);
        $this->entityManager = $this->getEntityManager();
    }

    public function findByPriceListAndProductId(PriceList $priceList, string $productId): ?Price
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p')
            ->from(Price::class, 'p')
            ->where('p.priceList = :priceList')
            ->andWhere('p.productId = :productId')
            ->setParameter('priceList', $priceList)
            ->setParameter('productId', $productId)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Price|null
     */
    public function findOneById(Uuid $id): mixed
    {
        return $this->findOneBy(['id' => $id]);
    }
}
