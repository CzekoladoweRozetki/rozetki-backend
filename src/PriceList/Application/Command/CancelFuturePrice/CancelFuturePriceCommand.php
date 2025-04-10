<?php

declare(strict_types=1);

namespace App\PriceList\Application\Command\CancelFuturePrice;

use App\Common\Application\Command\Command;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Uid\Uuid;

readonly class CancelFuturePriceCommand extends Command
{
    public function __construct(
        public Uuid $priceListId,
        public string $productId,
        public string $priceEventId,
        public ?string $reason = null,
        public ExecutionContext $context = ExecutionContext::Web,
    ) {
        parent::__construct($this->context);
    }
}
