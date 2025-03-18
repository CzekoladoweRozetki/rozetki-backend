<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Common\Application\Command\CommandBus;
use App\Common\Application\Query\QueryBus;
use App\Product\Application\Command\CreateProduct\CreateProductCommand;
use App\Product\Application\Command\CreateProduct\ProductVariantDTO;
use App\Product\Application\Query\GetProductById\GetProductByIdQuery;
use App\Product\Infrastructure\Api\DTO\ProductInputDTO;
use App\Product\Infrastructure\Api\Resource\Product;
use App\Product\Infrastructure\Api\Resource\ProductVariant;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<ProductInputDTO, Product>
 */
class ProductPostProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $variants = array_map(
            fn ($variant) => new ProductVariantDTO(
                $variant->name,
                $variant->description,
                attributeValues: array_map(
                    fn ($attributeValue) => Uuid::fromString($attributeValue),
                    $variant->attributeValues
                ),
            ),
            $data->variants
        );
        $command = new CreateProductCommand(
            Uuid::v4(),
            $data->name,
            $data->description,
            $variants,
            $data->categories,
            array_map(
                fn ($attributeValue) => Uuid::fromString($attributeValue),
                $data->attributeValues
            )
        );

        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }

        $query = new GetProductByIdQuery($command->id);
        $product = $this->queryBus->query($query);

        return new Product(
            $command->id->toString(),
            $data->name,
            $data->description,
            array_map(
                fn ($variant) => new ProductVariant(
                    $variant->id->toString(),
                    $variant->name,
                    $variant->description,
                    $variant->slug,
                ),
                $product->variants
            ),
            $data->categories,
        );
    }
}
