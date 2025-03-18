<?php

declare(strict_types=1);

namespace App\Attribute\Domain\Repository;

use App\Attribute\Domain\Entity\AttributeValue;
use App\Common\Domain\Repository\Repository;

/**
 * @extends Repository<AttributeValue>
 */
interface AttributeValueRepository extends Repository
{
}
