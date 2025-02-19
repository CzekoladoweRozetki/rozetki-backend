<?php

declare(strict_types=1);

namespace App\Catalog\Domain\Repository;

use App\Catalog\Domain\Entity\CatalogProduct;
use App\Common\Domain\Repository\Repository;

/**
 * @extends Repository<CatalogProduct>
 */
interface CatalogProductRepository extends Repository
{
}
