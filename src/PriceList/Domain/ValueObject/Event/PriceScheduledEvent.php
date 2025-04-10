<?php

declare(strict_types=1);

namespace App\PriceList\Domain\ValueObject\Event;

class PriceScheduledEvent extends PriceEvent
{
    public function __construct(
        string $id,
        private float $amount,
        private \DateTimeImmutable $effectiveFrom,
        ?string $reason = null,
        ?\DateTimeImmutable $createdAt = null,
    ) {
        parent::__construct($id, $reason, $createdAt ?? new \DateTimeImmutable());
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getEffectiveFrom(): \DateTimeImmutable
    {
        return $this->effectiveFrom;
    }

    public function getType(): string
    {
        return 'price_scheduled';
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'amount' => $this->amount,
            'effectiveFrom' => $this->effectiveFrom->format('c'),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            (float) $data['amount'],
            new \DateTimeImmutable($data['effectiveFrom']),
            $data['reason'] ?? null,
            new \DateTimeImmutable($data['createdAt'])
        );
    }
}
