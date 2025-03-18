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
class ProductVariantAttribute extends BaseEntity
{
    public function __construct(
        #[Id]
        #[Column(type: 'uuid')]
        protected Uuid $id,
        #[ManyToOne(targetEntity: ProductVariant::class, inversedBy: 'attributes')]
        private ProductVariant $productVariant,
        #[Column(type: 'uuid')]
        private Uuid $attributeValueId,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getProductVariant(): ProductVariant
    {
        return $this->productVariant;
    }

    public function getAttributeValueId(): Uuid
    {
        return $this->attributeValueId;
    }
}
