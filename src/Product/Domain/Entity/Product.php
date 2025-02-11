<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class Product
{
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        private Uuid $id,
        #[Column(type: 'string', length: 255, nullable: false)]
        private string $name,
        #[Column(type: 'text', nullable: false)]
        private string $description,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
