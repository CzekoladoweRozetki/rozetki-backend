<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
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
     * @param Collection<int, ProductVariant> $variants
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
}
