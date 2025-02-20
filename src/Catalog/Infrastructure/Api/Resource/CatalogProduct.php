<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Catalog\Infrastructure\Api\Provider\CatalogProductCollectionProvider;
use App\Catalog\Infrastructure\Api\Provider\CatalogProductSingleProvider;

#[ApiResource(
    operations: [
        new Get(provider: CatalogProductSingleProvider::class),
        new GetCollection(
            provider: CatalogProductCollectionProvider::class,
            parameters: [
                'page' => new QueryParameter(
                    key: 'page',
                    schema: [
                        'type' => 'integer',
                        'default' => '1',
                    ],
                    description: 'The page of items to return',
                    required: false
                ),
                'itemsPerPage' => new QueryParameter(
                    key: 'itemsPerPage',
                    schema: [
                        'type' => 'integer',
                        'default' => '10',
                    ],
                    description: 'The number of items to return per page',
                    required: false
                ),
                'search' => new QueryParameter(
                    key: 'search',
                    schema: [
                        'type' => 'string',
                    ],
                    description: 'The search query',
                    required: false
                ),
            ]
        ),
    ]
)]
class CatalogProduct
{
    public function __construct(
        #[ApiProperty(identifier: true, description: 'The slug of the product')]
        public string $id,
        public string $name,
        public string $description,
    ) {
    }
}
