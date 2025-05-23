<?php

declare(strict_types=1);

namespace App\Category\Domain\Repository;

use App\Category\Domain\Entity\Category;
use App\Common\Domain\Repository\Repository;

/**
 * @template-extends Repository<Category>
 */
interface CategoryRepository extends Repository
{
    public function findByName(string $name): ?Category;

    public function findBySlug(?string $slug): ?Category;

    /**
     * @return array<Category>
     */
    public function findRootCategories(): array;

    /**
     * @return array<Category>
     */
    public function findAllCategories(): array;

    /**
     * @param array<int, string> $ids
     *
     * @return array<Category>
     */
    public function findCategoriesByIds(array $ids): array;
}
