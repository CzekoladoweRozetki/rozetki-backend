<?php

declare(strict_types=1);

namespace App\PriceList\Domain\Repository;

use App\Common\Domain\Repository\Repository;
use App\PriceList\Domain\Entity\PriceList;

/**
 * @extends Repository<PriceList>
 */
interface PriceListRepository extends Repository
{
}
