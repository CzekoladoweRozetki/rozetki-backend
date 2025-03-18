<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Uid\Uuid;

#[Entity]
class ProductAttribute extends BaseEntity
{
    public function __construct(
        #[Id]
        #[Column(type: 'uuid')]
        protected Uuid $id,
        #[ManyToOne(targetEntity: Product::class, inversedBy: 'attributes')]
        private Product $product,
        #[Column(type: 'uuid')]
        private Uuid $attributeValueId,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getAttributeValueId(): Uuid
    {
        return $this->attributeValueId;
    }
}
