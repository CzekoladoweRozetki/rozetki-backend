<?php

declare(strict_types=1);

namespace App\PriceList\Domain\Entity;

use App\Common\Domain\Entity\BaseEntity;
use App\PriceList\Domain\ValueObject\Event\PriceCancelledEvent;
use App\PriceList\Domain\ValueObject\Event\PriceEvent;
use App\PriceList\Domain\ValueObject\Event\PriceScheduledEvent;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class Price extends BaseEntity
{
    use Timestampable;

    /**
     * @var array<string, mixed>[]
     */
    #[Column(type: 'json', options: ['jsonb' => true])]
    private array $events = [];

    /**
     * @var PriceEvent[]|null
     */
    private ?array $deserializedEvents = null;

    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        protected Uuid $id,
        #[ManyToOne(targetEntity: PriceList::class, inversedBy: 'prices')]
        #[JoinColumn(nullable: false)]
        private PriceList $priceList,
        #[Column(type: 'string')]
        private string $productId,
    ) {
        parent::__construct($id);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPriceList(): PriceList
    {
        return $this->priceList;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return PriceEvent[]
     */
    public function getEvents(): array
    {
        if (null === $this->deserializedEvents) {
            $this->deserializeEvents();
        }

        return $this->deserializedEvents;
    }

    public function schedulePrice(
        float $amount,
        \DateTimeImmutable $effectiveFrom,
        ?string $reason = null,
    ): void {
        $now = new \DateTimeImmutable();

        // When scheduling a new price, if it's effective immediately or in the past,
        // we should deactivate any current prices
        if ($effectiveFrom <= $now) {
            $this->deactivateCurrentPrices($effectiveFrom, $reason);
        }

        $event = new PriceScheduledEvent(
            Uuid::v4()->toRfc4122(),
            $amount,
            $effectiveFrom,
            $reason,
            new \DateTimeImmutable()
        );

        $this->addEvent($event);
    }

    public function cancelScheduledPrice(
        string $priceEventId,
        ?string $reason = null,
    ): void {
        $events = $this->getEvents();
        $eventToCancel = null;

        // Find the price event to cancel
        foreach ($events as $event) {
            if ($event->getId() === $priceEventId && $event instanceof PriceScheduledEvent) {
                $eventToCancel = $event;
                break;
            }
        }

        if (null === $eventToCancel) {
            throw new \InvalidArgumentException('Scheduled price event not found');
        }

        $now = new \DateTimeImmutable();
        if ($eventToCancel->getEffectiveFrom() <= $now) {
            throw new \InvalidArgumentException('Cannot cancel a price that is already active');
        }

        // Create cancellation event
        $cancellationEvent = new PriceCancelledEvent(
            Uuid::v4()->toRfc4122(),
            $priceEventId,
            $reason,
            new \DateTimeImmutable()
        );

        $this->addEvent($cancellationEvent);
    }

    public function getCurrentPrice(): ?float
    {
        $now = new \DateTimeImmutable();

        // Get all active scheduled prices
        $activeScheduledPrices = $this->getActiveScheduledPrices($now);

        if (empty($activeScheduledPrices)) {
            return null;
        }

        // Get the most recent active price
        usort($activeScheduledPrices, function (PriceScheduledEvent $a, PriceScheduledEvent $b) {
            return $b->getEffectiveFrom() <=> $a->getEffectiveFrom();
        });

        return reset($activeScheduledPrices)->getAmount();
    }

    public function getLowestPrice(int $daysToLookBack = 30): ?float
    {
        $today = new \DateTimeImmutable();
        $startDate = $today->modify("-{$daysToLookBack} days");

        // Get all prices that were active during the period
        $activePrices = $this->getActivePricesInPeriod($startDate, $today);

        if (empty($activePrices)) {
            return null;
        }

        // Find the lowest price
        $lowestPrice = null;
        foreach ($activePrices as $price) {
            if (null === $lowestPrice || $price->getAmount() < $lowestPrice) {
                $lowestPrice = $price->getAmount();
            }
        }

        return $lowestPrice;
    }

    /**
     * @return array{price: float, validFrom: \DateTimeImmutable}|null
     */
    public function getFuturePrice(): ?array
    {
        $now = new \DateTimeImmutable();

        // Get all scheduled but not yet active prices
        $futurePrices = $this->getFutureScheduledPrices($now);

        if (empty($futurePrices)) {
            return null;
        }

        // Sort by effective date (ascending)
        usort($futurePrices, function (PriceScheduledEvent $a, PriceScheduledEvent $b) {
            return $a->getEffectiveFrom() <=> $b->getEffectiveFrom();
        });

        // Return the next scheduled price
        $nextPrice = reset($futurePrices);

        return [
            'price' => $nextPrice->getAmount(),
            'validFrom' => $nextPrice->getEffectiveFrom(),
        ];
    }

    /**
     * @return PriceScheduledEvent[]
     */
    private function getActiveScheduledPrices(\DateTimeImmutable $date): array
    {
        $events = $this->getEvents();
        $cancelledIds = $this->getCancelledEventIds();
        $scheduled = [];

        foreach ($events as $event) {
            if ($event instanceof PriceScheduledEvent
                && !in_array($event->getId(), $cancelledIds, true)
                && $event->getEffectiveFrom() <= $date) {
                $scheduled[] = $event;
            }
        }

        return $scheduled;
    }

    /**
     * @return PriceScheduledEvent[]
     */
    private function getFutureScheduledPrices(\DateTimeImmutable $date): array
    {
        $events = $this->getEvents();
        $cancelledIds = $this->getCancelledEventIds();
        $scheduled = [];

        foreach ($events as $event) {
            if ($event instanceof PriceScheduledEvent
                && !in_array($event->getId(), $cancelledIds, true)
                && $event->getEffectiveFrom() > $date) {
                $scheduled[] = $event;
            }
        }

        return $scheduled;
    }

    /**
     * @return PriceScheduledEvent[]
     */
    private function getActivePricesInPeriod(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        $events = $this->getEvents();
        $cancelledIds = $this->getCancelledEventIds();
        $activeInPeriod = [];

        foreach ($events as $event) {
            if ($event instanceof PriceScheduledEvent
                && !in_array($event->getId(), $cancelledIds, true)
                && $event->getEffectiveFrom() >= $startDate
                && $event->getEffectiveFrom() <= $endDate) {
                $activeInPeriod[] = $event;
            }
        }

        return $activeInPeriod;
    }

    /**
     * @return string[]
     */
    private function getCancelledEventIds(): array
    {
        $events = $this->getEvents();
        $cancelledIds = [];

        foreach ($events as $event) {
            if ($event instanceof PriceCancelledEvent) {
                $cancelledIds[] = $event->getCancelledEventId();
            }
        }

        return $cancelledIds;
    }

    private function addEvent(PriceEvent $event): void
    {
        $this->events[] = $event->jsonSerialize();

        // Update the deserialized events if they've already been loaded
        if (null !== $this->deserializedEvents) {
            $this->deserializedEvents[] = $event;
        }
    }

    private function deserializeEvents(): void
    {
        $this->deserializedEvents = [];

        foreach ($this->events as $eventData) {
            $type = $eventData['type'] ?? null;

            switch ($type) {
                case 'price_scheduled':
                    $this->deserializedEvents[] = PriceScheduledEvent::fromArray($eventData);
                    break;
                case 'price_cancelled':
                    $this->deserializedEvents[] = PriceCancelledEvent::fromArray($eventData);
                    break;
                default:
                    throw new \RuntimeException(sprintf('Unknown event type: %s', $type));
            }
        }
    }

    private function deactivateCurrentPrices(\DateTimeImmutable $effectiveFrom, ?string $reason): void
    {
        // We don't actually deactivate anything, as the new event will simply take precedence
        // This is left as a hook in case implementation details change
    }
}
