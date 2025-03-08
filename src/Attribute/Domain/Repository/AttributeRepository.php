<?php

declare(strict_types=1);

namespace App\Attribute\Domain\Repository;

use App\Attribute\Domain\Entity\Attribute;
use App\Common\Domain\Repository\Repository;

/**
 * @extends Repository<Attribute>
 */
interface AttributeRepository extends Repository
{
    /**
     * @return Attribute[]
     */
    public function findAttributes(): array;
}
