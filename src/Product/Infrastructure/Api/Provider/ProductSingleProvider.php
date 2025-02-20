<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Common\Application\Query\QueryBus;
use App\Product\Application\Query\DTO\ProductDTO;
use App\Product\Application\Query\GetProductById\GetProductByIdQuery;
use App\Product\Infrastructure\Api\Resource\Product;
use App\Product\Infrastructure\Api\Resource\ProductVariant;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProviderInterface<Product>
 */
class ProductSingleProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $query = new GetProductByIdQuery(
            Uuid::fromString($uriVariables['id'])
        );

        /**
         * @var ProductDTO|null $product
         */
        $product = $this->queryBus->query($query);

        if (null === $product) {
            return null;
        }

        $variants = array_map(
            fn ($variant) => new ProductVariant(
                $variant->id->toString(),
                $variant->name,
                $variant->description,
                $variant->slug
            ),
            $product->variants
        );

        return new Product(
            $product->id->toString(),
            $product->name,
            $product->description,
            $variants
        );
    }
}
