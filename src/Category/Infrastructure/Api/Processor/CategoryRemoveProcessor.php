<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Category\Application\Command\RemoveCategory\RemoveCategoryCommand;
use App\Category\Infrastructure\Api\Resource\Category;
use App\Common\Application\Command\CommandBus;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<Category, null>
 */
class CategoryRemoveProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->commandBus->dispatch(new RemoveCategoryCommand(Uuid::fromString($data->id)));

        return null;
    }
}
