<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\PriceList\Infrastructure\Api\DTO\PriceListInputDTO;
use App\PriceList\Infrastructure\Api\Processor\PriceListPostProcessor;
use App\PriceList\Infrastructure\Api\Provider\PriceListSingleProvider;

#[ApiResource(
    operations: [
        new Post(input: PriceListInputDTO::class, processor: PriceListPostProcessor::class),
        new Get(provider: PriceListSingleProvider::class),
    ]
)]
class PriceList
{
    public function __construct(
        public string $id,
        public string $name,
        public string $currency,
    ) {
    }
}
