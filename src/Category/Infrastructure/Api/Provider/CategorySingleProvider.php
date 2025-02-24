<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Category\Application\Query\GetCategory\CategoryDTO;
use App\Category\Application\Query\GetCategory\GetCategoryQuery;
use App\Category\Infrastructure\Api\Resource\Category;
use App\Common\Application\Query\QueryBus;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProviderInterface<Category>
 */
class CategorySingleProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $query = new GetCategoryQuery(Uuid::fromString($uriVariables['id']));

        /** @var CategoryDTO|null $category */
        $category = $this->queryBus->query($query);

        if (!$category) {
            return null;
        }

        return new Category(
            id: $category->id->toString(),
            name: $category->name,
            slug: $category->slug,
            parent: $category->parent?->toString(),
        );
    }
}
