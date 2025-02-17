<?php

declare(strict_types=1);

namespace App\Product\Domain\Repository;

use App\Common\Domain\Repository\Repository;
use App\Product\Domain\Entity\Product;

/**
 * @template-extends Repository<Product>
 */
interface ProductRepository extends Repository
{
}
