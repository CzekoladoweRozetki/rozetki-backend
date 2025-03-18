<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use App\Product\Application\Command\CreateProduct\ProductVariantDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class Product extends BaseEntity
{
    /**
     * @param Collection<int, ProductVariant>   $variants
     * @param array<int, string>                $categories
     * @param Collection<int, ProductAttribute> $attributes
     */
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        protected Uuid $id,
        #[Column(type: 'string', length: 255, nullable: false)]
        private string $name,
        #[Column(type: 'text', nullable: false)]
        private string $description,
        #[OneToMany(targetEntity: ProductVariant::class, mappedBy: 'product', cascade: [
            'persist',
            'remove',
        ], fetch: 'EAGER', orphanRemoval: true)]
        private Collection $variants = new ArrayCollection(),
        /**
         * @var array<int, string>
         */
        private array $categories = [],
        #[OneToMany(targetEntity: ProductAttribute::class, mappedBy: 'product', cascade: [
            'persist',
            'remove',
        ], orphanRemoval: true)]
        private Collection $attributes = new ArrayCollection(),
    ) {
        parent::__construct($id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Collection<int, ProductVariant>
     */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    /**
     * @param array<int, ProductVariantDTO> $variants
     */
    public function addVariants(array $variants): void
    {
        foreach ($variants as $variant) {
            $productVariant = new ProductVariant(
                Uuid::v4(),
                $variant->name,
                $variant->slug,
                $variant->description,
                $this
            );
            $productVariant->addAttributeValues($variant->attributeValues);
            $this->variants->add(
                $productVariant
            );
        }
    }

    /**
     * @return array<int, string>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ProductAttribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * @param array<int, Uuid> $attributeValues
     */
    public function addAttributeValues(array $attributeValues): void
    {
        foreach ($attributeValues as $attributeValue) {
            $this->attributes->add(
                new ProductAttribute(
                    Uuid::v4(),
                    $this,
                    $attributeValue,
                )
            );
        }
    }
}
