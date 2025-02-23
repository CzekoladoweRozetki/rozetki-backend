<?php

declare(strict_types=1);

namespace App\Category\Domain\Repository;

use App\Category\Domain\Entity\Category;
use App\Common\Domain\Repository\Repository;

/**
 * @extends Repository<Category>
 */
interface CategoryRepository extends Repository
{
    public function findByName(string $name): ?Category;

    public function findBySlug(?string $slug): ?Category;
}
