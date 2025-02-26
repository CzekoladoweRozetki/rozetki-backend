<?php

declare(strict_types=1);

namespace App\Category\Application\Command\RemoveCategory;

use App\Category\Domain\Repository\CategoryRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class RemoveCategoryCommandHandler
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }

    public function __invoke(RemoveCategoryCommand $command): void
    {
        $category = $this->categoryRepository->findOneById($command->id);

        if (!$category) {
            return;
        }

        $this->categoryRepository->remove($category);
    }
}
