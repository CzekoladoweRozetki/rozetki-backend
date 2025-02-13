<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Common\Application\Command\CommandBus;
use App\Product\Application\Command\RemoveProduct\RemoveProductCommand;
use App\Product\Infrastructure\Api\Resource\Product;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<Product, null>
 */
class ProductDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->commandBus->dispatch(new RemoveProductCommand(Uuid::fromString($data->id)));

        return null;
    }
}
