<?php

declare(strict_types=1);

namespace App\PriceList\Application\Query\GetLowestPrice;

use App\Common\Application\Query\Query;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Uid\Uuid;

readonly class GetLowestPriceQuery extends Query
{
    public function __construct(
        public Uuid $priceListId,
        public string $productId,
        public int $daysToLookBack = 30,
        public ExecutionContext $context = ExecutionContext::Web,
    ) {
        parent::__construct($this->context);
    }
}
