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
     * @param array<int, string>|null $ids
     *
     * @return Attribute[]
     */
    public function findAttributes(?array $ids = null): array;
}
