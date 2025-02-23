<?php

declare(strict_types=1);

namespace App\Category\Application\Query\GetCategories;

use App\Category\Domain\Entity\Category;
use App\Category\Domain\Repository\CategoryRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetCategoriesQueryHandler
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }

    /**
     * @return array<CategoryDTO>
     */
    public function __invoke(GetCategoriesQuery $query): array
    {
        $categories = $this->categoryRepository->findAllCategories();

        return array_map(function (Category $category) {
            return new CategoryDTO(
                id: $category->getId(),
                name: $category->getName(),
                slug: $category->getSlug(),
                parent: $category->getParent()?->getId(),
                children: array_map(function (Category $category) {
                    return new CategoryDTO(
                        id: $category->getId(),
                        name: $category->getName(),
                        slug: $category->getSlug(),
                        parent: $category->getParent()?->getId(),
                    );
                }, $category->getChildren()->toArray())
            );
        }, $categories);
    }
}
