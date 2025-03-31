<?php

declare(strict_types=1);

namespace App\PriceList\Application\Query\GetPriceListCommand;

use App\Common\Application\Query\Query;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Uid\Uuid;

readonly class GetPriceListQuery extends Query
{
    public function __construct(
        public Uuid $priceListId,
        public ExecutionContext $executionContext = ExecutionContext::Web,
    ) {
        parent::__construct($this->executionContext);
    }
}
