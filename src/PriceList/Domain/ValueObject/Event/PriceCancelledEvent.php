<?php

declare(strict_types=1);

namespace App\PriceList\Domain\ValueObject\Event;

class PriceCancelledEvent extends PriceEvent
{
    public function __construct(
        string $id,
        private string $cancelledEventId,
        ?string $reason = null,
        ?\DateTimeImmutable $createdAt = null,
    ) {
        parent::__construct($id, $reason, $createdAt ?? new \DateTimeImmutable());
    }

    public function getCancelledEventId(): string
    {
        return $this->cancelledEventId;
    }

    public function getType(): string
    {
        return 'price_cancelled';
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'cancelledEventId' => $this->cancelledEventId,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['cancelledEventId'],
            $data['reason'] ?? null,
            new \DateTimeImmutable($data['createdAt'])
        );
    }
}
