<?php

declare(strict_types=1);

namespace App\PriceList\Domain\ValueObject\Event;

abstract class PriceEvent implements \JsonSerializable
{
    public function __construct(
        protected string $id,
        protected ?string $reason,
        protected \DateTimeImmutable $createdAt,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    abstract public function getType(): string;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->getType(),
            'reason' => $this->reason,
            'createdAt' => $this->createdAt->format('c'),
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        // This will be implemented by child classes
        throw new \RuntimeException('Must be implemented by child classes');
    }
}
