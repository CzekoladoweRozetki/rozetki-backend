<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Api\DTO;

readonly class PriceListInputDTO
{
    public function __construct(
        public string $name,
        public string $currency,
    ) {
    }
}
