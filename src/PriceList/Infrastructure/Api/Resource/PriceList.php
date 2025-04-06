<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\PriceList\Infrastructure\Api\DTO\PriceListInputDTO;
use App\PriceList\Infrastructure\Api\Processor\PriceListDeleteProcessor;
use App\PriceList\Infrastructure\Api\Processor\PriceListPostProcessor;
use App\PriceList\Infrastructure\Api\Processor\PriceListPutProcessor;
use App\PriceList\Infrastructure\Api\Provider\PriceListSingleProvider;

#[ApiResource(
    operations: [
        new Post(input: PriceListInputDTO::class, processor: PriceListPostProcessor::class),
        new Get(provider: PriceListSingleProvider::class),
        new Put(
            provider: PriceListSingleProvider::class,
            processor: PriceListPutProcessor::class
        ),
        new Delete(provider: PriceListSingleProvider::class, processor: PriceListDeleteProcessor::class),
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
