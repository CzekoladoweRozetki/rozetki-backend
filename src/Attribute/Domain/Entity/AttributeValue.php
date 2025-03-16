<?php

declare(strict_types=1);

namespace App\Attribute\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Gedmo\Mapping\Annotation\Slug;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class AttributeValue extends BaseEntity
{
    /**
     * @param Collection<int, AttributeValue> $childValues
     */
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        protected Uuid $id,
        #[ManyToOne(targetEntity: Attribute::class, inversedBy: 'values')]
        #[JoinColumn(name: 'attribute_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
        private Attribute $attribute,
        #[Column(type: 'string')]
        private string $value,
        #[OneToMany(targetEntity: AttributeValue::class, mappedBy: 'parentValue')]
        private Collection $childValues = new ArrayCollection(),
        #[ManyToOne(targetEntity: AttributeValue::class, inversedBy: 'childValues')]
        private ?AttributeValue $parentValue = null,
        #[Column(type: 'string')]
        #[Slug(fields: ['value'], unique: true)]
        private ?string $slug = null,
    ) {
        parent::__construct($id);
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function addChildValue(mixed $childValue): void
    {
        $value = new AttributeValue(
            id: Uuid::v4(),
            attribute: $this->attribute,
            value: $childValue,
            childValues: new ArrayCollection(),
            parentValue: $this
        );
        $this->childValues->add($childValue);
    }

    public function removeChildValue(mixed $childValue): void
    {
        $value = $this->childValues->findFirst(
            fn (int $key, AttributeValue $value) => $value->getValue() === $childValue
        );

        if (null === $value) {
            return;
        }

        $this->childValues->removeElement($childValue);
    }

    public function getParentValue(): ?AttributeValue
    {
        return $this->parentValue;
    }

    /**
     * @return Collection<int, AttributeValue>
     */
    public function getChildValues(): Collection
    {
        return $this->childValues;
    }
}
