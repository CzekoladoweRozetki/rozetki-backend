<?php

declare(strict_types=1);

namespace App\PriceList\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity]
class PriceList extends BaseEntity
{
    use Timestampable;

    /**
     * @param Collection<int, PriceChange> $priceChanges
     */
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        protected Uuid $id,
        #[OneToMany(targetEntity: PriceChange::class, orphanRemoval: true, mappedBy: 'pricelist', cascade: [
            'persist',
            'remove',
        ])]
        private Collection $priceChanges,
        #[Column(type: 'string')]
        private string $name,
        #[Column(type: 'string')]
        #[Assert\Currency()]
        private string $currency,
    ) {
        parent::__construct($id);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return Collection<int, PriceChange>
     */
    public function getPriceChanges(): Collection
    {
        return $this->priceChanges;
    }
}
