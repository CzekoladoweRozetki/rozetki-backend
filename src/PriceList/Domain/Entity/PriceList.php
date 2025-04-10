<?php

declare(strict_types=1);

namespace App\PriceList\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @param Collection<int, Price> $prices
     */
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        protected Uuid $id,
        #[Column(type: 'string')]
        private string $name,
        #[Column(type: 'string')]
        #[Assert\Currency]
        #[Assert\NotBlank]
        private string $currency,
        #[OneToMany(targetEntity: Price::class, mappedBy: 'priceList', cascade: [
            'persist',
            'remove',
        ], orphanRemoval: true)]
        private Collection $prices = new ArrayCollection(),
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
     * @return Collection<int, Price>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function update(string $name, string $currency): void
    {
        $this->name = $name;
        $this->currency = $currency;
    }

    public function findPrice(string $productId): ?Price
    {
        foreach ($this->prices as $price) {
            if ($price->getProductId() === $productId) {
                return $price;
            }
        }

        return null;
    }

    public function getPrice(string $productId): Price
    {
        $price = $this->findPrice($productId);

        if (null === $price) {
            $price = new Price(Uuid::v4(), $this, $productId);
            $this->prices->add($price);
        }

        return $price;
    }

    public function schedulePrice(
        string $productId,
        float $amount,
        \DateTimeImmutable $effectiveFrom,
        ?string $reason = null,
    ): Price {
        $price = $this->getPrice($productId);
        $price->schedulePrice($amount, $effectiveFrom, $reason);

        return $price;
    }

    public function cancelScheduledPrice(
        string $productId,
        string $priceEventId,
        ?string $reason = null,
    ): void {
        $price = $this->findPrice($productId);

        if (null === $price) {
            throw new \InvalidArgumentException(sprintf('No price found for product ID %s', $productId));
        }

        $price->cancelScheduledPrice($priceEventId, $reason);
    }

    public function getCurrentPrice(string $productId): ?float
    {
        $price = $this->findPrice($productId);

        if (null === $price) {
            return null;
        }

        return $price->getCurrentPrice();
    }

    public function getLowestPrice(string $productId, int $daysToLookBack = 30): ?float
    {
        $price = $this->findPrice($productId);

        if (null === $price) {
            return null;
        }

        return $price->getLowestPrice($daysToLookBack);
    }

    /**
     * @return array{price: float, validFrom: \DateTimeImmutable}|null
     */
    public function getFuturePrice(string $productId): ?array
    {
        $price = $this->findPrice($productId);

        if (null === $price) {
            return null;
        }

        return $price->getFuturePrice();
    }
}
