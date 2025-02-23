<?php

declare(strict_types=1);

namespace App\Category\Application\Query\GetCategory;

use App\Category\Domain\Repository\CategoryRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetCategoryCommandHandler
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }

    public function __invoke(GetCategoryQuery $command): ?CategoryDTO
    {
        $category = $this->categoryRepository->findOneById($command->id);

        if (!$category) {
            return null;
        }

        return new CategoryDTO(
            id: $category->getId(),
            name: $category->getName(),
            slug: $category->getSlug(),
            parent: $category->getParent() ? $category->getParent()->getId() : null
        );
    }
}
