<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Category\Application\Command\CreateCategory\CreateCategoryCommand;
use App\Category\Application\Query\GetCategory\GetCategoryQuery;
use App\Category\Infrastructure\Api\DTO\CategoryInputDTO;
use App\Category\Infrastructure\Api\Resource\Category;
use App\Common\Application\Command\CommandBus;
use App\Common\Application\Query\QueryBus;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<CategoryInputDTO, Category>
 */
class CategoryPostProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new CreateCategoryCommand(
            Uuid::v4(),
            $data->name,
            $data->slug,
            $data->parent ? Uuid::fromString($data->parent) : null
        );

        try {
            $this->commandBus->dispatch($command);
        } catch (\Exception $e) {
            throw $e->getPrevious();
        }

        $query = new GetCategoryQuery($command->id);
        $category = $this->queryBus->query($query);

        return new Category(
            id: $command->id->toString(),
            name: $command->name,
            slug: $category->slug,
            parent: $category->parent ? $category->parent->toString() : null
        );
    }
}
