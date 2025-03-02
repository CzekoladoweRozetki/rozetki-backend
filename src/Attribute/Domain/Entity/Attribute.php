<?php

declare(strict_types=1);

namespace App\Attribute\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class Attribute extends BaseEntity
{
    /**
     * @param Collection<int, AttributeValue> $values
     * @param Collection<int, Attribute>      $children
     */
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        protected Uuid $id,
        #[Column(type: 'string', length: 255)]
        private string $name,
        #[ManyToOne(targetEntity: Attribute::class, inversedBy: 'children')]
        private ?Attribute $parent = null,
        #[OneToMany(targetEntity: Attribute::class, mappedBy: 'parent', cascade: [
            'persist',
            'remove',
        ], orphanRemoval: true)]
        private Collection $children = new ArrayCollection(),
        #[OneToMany(targetEntity: AttributeValue::class, mappedBy: 'attribute', cascade: [
            'persist',
            'remove',
        ], orphanRemoval: true)]
        private Collection $values = new ArrayCollection(),
    ) {
        parent::__construct($this->id);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParent(): ?Attribute
    {
        return $this->parent;
    }

    /**
     * @return Collection<int, Attribute>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @return Collection<int, AttributeValue>
     */
    public function getValues(): Collection
    {
        return $this->values;
    }

    public function addValue(mixed $value): void
    {
        $attributeValue = new AttributeValue(
            id: Uuid::v4(),
            attribute: $this,
            value: $value
        );

        $this->values->add($attributeValue);
    }
}
