<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Repository;

use App\Category\Domain\Entity\Category;
use App\Category\Domain\Repository\CategoryRepository;
use App\Common\Infrastructure\Repository\DoctrineRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class DoctrineCategoryRepository extends ServiceEntityRepository implements CategoryRepository
{
    /**
     * @use DoctrineRepositoryTrait<Category>
     */
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findByName(string $name): ?Category
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findBySlug(?string $slug): ?Category
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    public function findRootCategories(): array
    {
        return $this->findBy(['parent' => null]);
    }

    public function findAllCategories(): array
    {
        return $this->findAll();
    }

    public function findCategoriesByIds(array $ids): array
    {
        return $this->findBy(['id' => $ids]);
    }
}
