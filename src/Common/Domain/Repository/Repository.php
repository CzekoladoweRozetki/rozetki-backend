<?php

declare(strict_types=1);

namespace App\Common\Domain\Repository;

use App\Common\Domain\Entity\BaseEntity;
use Symfony\Component\Uid\Uuid;

/**
 * @template T of BaseEntity
 */
interface Repository
{
    /**
     * @param T ...$entities
     */
    public function save(...$entities): void;

    /**
     * @param T ...$entities
     */
    public function remove(...$entities): void;

    /**
     * @return T|null
     */
    public function findOneById(Uuid $id): mixed;
}
