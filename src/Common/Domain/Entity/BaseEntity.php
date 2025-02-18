<?php

declare(strict_types=1);

namespace App\Common\Domain\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[\Doctrine\ORM\Mapping\Entity]
abstract class BaseEntity
{
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        protected Uuid $id,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
