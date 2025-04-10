<?php

declare(strict_types=1);

namespace App\PriceList\Domain\Repository;

use App\Common\Domain\Repository\Repository;
use App\PriceList\Domain\Entity\Price;
use App\PriceList\Domain\Entity\PriceList;

/**
 * @extends Repository<Price>
 */
interface PriceRepository extends Repository
{
    public function findByPriceListAndProductId(PriceList $priceList, string $productId): ?Price;
}
