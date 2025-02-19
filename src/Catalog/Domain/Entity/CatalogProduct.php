<?php

declare(strict_types=1);

namespace App\Catalog\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class CatalogProduct extends BaseEntity
{
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        protected Uuid $id,
        #[Column(type: 'string', length: 255)]
        private string $name,
        #[Column(type: 'text')]
        private string $description,
        #[Column(type: 'string', length: 255, unique: true)]
        private string $slug,
        #[Column(type: 'json_document', nullable: true, options: ['jsonb' => true])]
        private mixed $data = null,
    ) {
        parent::__construct($this->id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
