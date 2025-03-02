<?php

declare(strict_types=1);

namespace App\Attribute\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class AttributeValue extends BaseEntity
{
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        protected Uuid $id,
        #[ManyToOne(targetEntity: Attribute::class, inversedBy: 'values')]
        private Attribute $attribute,
        private mixed $value,
    ) {
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
