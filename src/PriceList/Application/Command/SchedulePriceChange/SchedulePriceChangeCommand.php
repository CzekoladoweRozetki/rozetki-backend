<?php

declare(strict_types=1);

namespace App\PriceList\Application\Command\SchedulePriceChange;

use App\Common\Application\Command\Command;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Uid\Uuid;

readonly class SchedulePriceChangeCommand extends Command
{
    public function __construct(
        public Uuid $priceListId,
        public string $productId,
        public float $price,
        public ?\DateTimeImmutable $effectiveFrom = null,
        public ?string $reason = null,
        public ExecutionContext $context = ExecutionContext::Web,
    ) {
        parent::__construct($this->context);
    }
}
