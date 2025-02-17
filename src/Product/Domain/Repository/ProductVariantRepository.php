<?php

declare(strict_types=1);

namespace App\Product\Domain\Repository;

use App\Common\Domain\Repository\Repository;
use App\Product\Domain\Entity\ProductVariant;

/**
 * @template-extends Repository<ProductVariant>
 */
interface ProductVariantRepository extends Repository
{
    public function findOneBySlug(string $slug): ?ProductVariant;
}
