<?php

declare(strict_types=1);

namespace App\Attribute\Domain\Repository;

use App\Attribute\Domain\Entity\AttributeValue;
use App\Common\Domain\Repository\Repository;
use Symfony\Component\Uid\Uuid;

/**
 * @extends Repository<AttributeValue>
 */
interface AttributeValueRepository extends Repository
{
    /**
     * @param array<int, Uuid> $ids
     *
     * @return AttributeValue[]
     */
    public function findByAttributeValueIds(array $ids): array;
}
