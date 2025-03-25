<?php

declare(strict_types=1);

namespace App\PriceList\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Uid\Uuid;

#[Entity]
class PriceChange extends BaseEntity
{
    use Timestampable;

    public function __construct(
        #[Id]
        #[Column(type: 'uuid')]
        protected Uuid $id,
        #[ManyToOne(targetEntity: PriceList::class, inversedBy: 'priceChanges')]
        private PriceList $pricelist,
        #[Column(type: 'integer')]
        private int $price,
        #[Column(type: 'datetime')]
        private ?\DateTime $startFrom,
        #[Column(type: 'datetime')]
        private ?\DateTime $endAt,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPricelist(): PriceList
    {
        return $this->pricelist;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getStartFrom(): ?\DateTime
    {
        return $this->startFrom;
    }

    public function getEndAt(): ?\DateTime
    {
        return $this->endAt;
    }
}
