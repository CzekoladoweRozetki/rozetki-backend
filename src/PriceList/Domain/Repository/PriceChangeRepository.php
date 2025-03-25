<?php

declare(strict_types=1);

namespace App\PriceList\Domain\Repository;

use App\Common\Domain\Repository\Repository;
use App\PriceList\Domain\Entity\PriceChange;

/**
 * @extends Repository<PriceChange>
 */
interface PriceChangeRepository extends Repository
{
}
