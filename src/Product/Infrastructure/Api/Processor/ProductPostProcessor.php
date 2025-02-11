<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Common\Application\Command\CommandBus;
use App\Product\Application\Command\CreateProduct\CreateProductCommand;
use App\Product\Infrastructure\Api\DTO\ProductInputDTO;
use App\Product\Infrastructure\Api\Resource\Product;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<ProductInputDTO, Product>
 */
class ProductPostProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new CreateProductCommand(
            Uuid::v4(),
            $data->name,
            $data->description
        );

        $this->commandBus->dispatch($command);

        return new Product(
            $command->id->toString(),
            $data->name,
            $data->description
        );
    }
}
