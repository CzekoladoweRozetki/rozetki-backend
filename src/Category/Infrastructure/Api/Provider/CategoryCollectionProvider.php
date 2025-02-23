<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Category\Application\Query\GetCategories\CategoryDTO;
use App\Category\Application\Query\GetCategories\GetCategoriesQuery;
use App\Category\Infrastructure\Api\Resource\Category;
use App\Common\Application\Query\QueryBus;

/**
 * @implements ProviderInterface<Category>
 */
class CategoryCollectionProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $categories = $this->queryBus->query(new GetCategoriesQuery());

        return array_map(function (CategoryDTO $category) {
            return new Category(
                id: $category->id->toString(),
                name: $category->name,
                slug: $category->slug,
                parent: $category->parent?->toString(),
            );
        }, $categories);
    }
}
