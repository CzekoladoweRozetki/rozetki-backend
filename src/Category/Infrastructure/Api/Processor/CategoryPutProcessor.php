<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Category\Application\Command\EditCommand\EditCategoryCommand;
use App\Category\Infrastructure\Api\Resource\Category;
use App\Common\Application\Command\CommandBus;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<Category, Category>
 */
class CategoryPutProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new EditCategoryCommand(
            Uuid::fromString($uriVariables['id']),
            $data->name,
            $data->slug,
            $data->parent ? Uuid::fromString($data->parent) : null
        );

        try {
            $this->commandBus->dispatch($command);
        } catch (\Exception $e) {
            throw $e->getPrevious();
        }

        return new Category(
            id: $command->id->toString(),
            name: $command->name,
            slug: $command->slug,
            parent: $command->parent ? $command->parent->toString() : null
        );
    }
}
