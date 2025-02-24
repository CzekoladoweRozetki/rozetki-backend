<?php

declare(strict_types=1);

namespace App\Category\Application\Command\EditCommand;

use App\Category\Domain\Entity\Category;
use App\Category\Domain\Exception\CategoryNotFoundException;
use App\Category\Domain\Repository\CategoryRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EditCategoryCommandHandler
{
    public function __construct(
        public CategoryRepository $repository,
    ) {
    }

    public function __invoke(EditCategoryCommand $command): void
    {
        /** @var Category $category */
        $category = $this->repository->findOneById($command->id);

        $category->setName($command->name);
        $category->setSlug($command->slug);

        if ($command->parent) {
            $parent = $this->repository->findOneById($command->parent);
            if (!$parent) {
                throw new CategoryNotFoundException();
            }
            $category->setParent($parent);
        }

        $this->repository->save($category);
    }
}
