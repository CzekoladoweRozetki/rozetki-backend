<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Repository;

use Symfony\Component\Uid\Uuid;

/**
 * @template T of object
 */
trait DoctrineRepositoryTrait
{
    /**
     * @param T ...$entities
     */
    public function save(...$entities): void
    {
        foreach ($entities as $entity) {
            $this->getEntityManager()->persist($entity);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param T ...$entities
     */
    public function remove(...$entities): void
    {
        foreach ($entities as $entity) {
            $this->getEntityManager()->remove($entity);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @return T|null
     */
    public function findOneById(Uuid $id): mixed
    {
        return $this->find($id);
    }
}
