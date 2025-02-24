<?php

declare(strict_types=1);

namespace App\Category\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Uid\Uuid;

#[Entity]
class Category extends BaseEntity
{
    /**
     * @param Collection<int, Category> $children
     */
    public function __construct(
        #[Id]
        #[Column(type: 'uuid')]
        protected Uuid $id,
        #[Column(type: 'string', length: 255, unique: true)]
        private string $name,
        #[Column(type: 'string', length: 255, unique: true)]
        private string $slug,
        #[ManyToOne(targetEntity: Category::class, inversedBy: 'children')]
        private ?Category $parent = null,
        #[OneToMany(targetEntity: Category::class, mappedBy: 'parent')]
        private Collection $children = new ArrayCollection(),
    ) {
        if ($this->isInvalidSlug($slug)) {
            throw new \InvalidArgumentException('Invalid slug');
        }
        parent::__construct($id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setParent(mixed $parent): void
    {
        $this->parent = $parent;
    }

    public function setSlug(string $slug): void
    {
        if ($this->isInvalidSlug($slug)) {
            throw new \InvalidArgumentException('Invalid slug');
        }
        $this->slug = $slug;
    }

    private function isInvalidSlug(string $slug): bool
    {
        return !preg_match('/^[a-z0-9-]+$/', $slug);
    }
}
