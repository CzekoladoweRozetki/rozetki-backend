<?php

declare(strict_types=1);

namespace App\Category\Application\Command\CreateCategory;

use App\Category\Domain\Entity\Category;
use App\Category\Domain\Event\CategoryCreatedEvent;
use App\Category\Domain\Exception\CategoryAlreadyExistsException;
use App\Category\Domain\Exception\CategoryNotFoundException;
use App\Category\Domain\Repository\CategoryRepository;
use App\Common\Application\Event\EventBus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsMessageHandler]
class CreateCategoryCommandHandler
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private SluggerInterface $slugger,
        private EventBus $eventBus,
    ) {
    }

    public function __invoke(CreateCategoryCommand $command): void
    {
        $category = $this->categoryRepository->findByName($command->name);

        if ($category) {
            throw new CategoryAlreadyExistsException();
        }

        $parent = null;
        if ($command->parent) {
            $parent = $this->categoryRepository->findOneById($command->parent);
            if (!$parent) {
                throw new CategoryNotFoundException();
            }
        }

        $slug = $command->slug
            ? $this->slugger->slug($command->slug)->lower()
            : $this->slugger->slug($command->name)->lower();
        $existingCategoryWithSlug = $this->categoryRepository->findBySlug($slug->toString());
        if ($existingCategoryWithSlug) {
            $slug = $this->slugger->slug($command->name.'-'.bin2hex(random_bytes(4)))->lower();
        }

        $category = new Category(
            $command->id,
            $command->name,
            $slug->toString(),
            $parent ?? null
        );
        $this->categoryRepository->save($category);

        $event = new CategoryCreatedEvent(
            $category->getId(),
            $category->getName(),
            $category->getSlug(),
            $category->getParent()?->getId()?->toString() ?? null
        );

        $this->eventBus->dispatch($event);
    }
}
