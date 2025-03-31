<?php

declare(strict_types=1);

namespace App\PriceList\Application\Query\GetPriceListCommand;

use Symfony\Component\Uid\Uuid;

class PriceListDTO
{
    public function __construct(
        public Uuid $id,
        public string $name,
        public string $currency,
    ) {
    }
}
