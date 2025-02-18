<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class ProductVariant extends BaseEntity
{
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        protected Uuid $id,
        #[Column(type: 'string')]
        private string $name,
        #[Column(type: 'string', length: 255, unique: true)]
        private string $slug,
        #[Column(type: 'text')]
        private string $description,
        #[ManyToOne(targetEntity: Product::class, fetch: 'EAGER', inversedBy: 'variants')]
        private Product $product,
    ) {
        parent::__construct($id);
        // validate that slug is composed of only alphanumeric characters and dashes
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            throw new \InvalidArgumentException('Invalid slug');
        }
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

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
