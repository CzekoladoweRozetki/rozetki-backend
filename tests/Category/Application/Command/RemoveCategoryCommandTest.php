<?php

declare(strict_types=1);

namespace App\Tests\Category\Application\Command;

use App\Category\Application\Command\RemoveCategory\RemoveCategoryCommand;
use App\Category\Domain\Repository\CategoryRepository;
use App\Common\Application\Command\CommandBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\Factory\CategoryFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\Category\Application\Command\RemoveCategory\RemoveCategoryCommand
 * @covers \App\Category\Application\Command\RemoveCategory\RemoveCategoryCommandHandler
 */
class RemoveCategoryCommandTest extends KernelTestCase
{
    use Factories;

    private CommandBus $commandBus;

    private CategoryRepository $categoryRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get(CommandBus::class);
        $this->categoryRepository = self::getContainer()->get(CategoryRepository::class);
    }

    public function testShouldRemoveCategory(): void
    {
        // Given
        $category = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);
        $categoryId = $category->getId();

        $command = new RemoveCategoryCommand(
            id: $categoryId, executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $removedCategory = $this->categoryRepository->findOneById($categoryId);
        self::assertNull($removedCategory);
    }

    public function testShouldRemoveCategoryWithChildren(): void
    {
        // Given
        $parent = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $child1 = CategoryFactory::createOne([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'parent' => $parent,
        ]);

        $child2 = CategoryFactory::createOne([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'parent' => $parent,
        ]);

        $command = new RemoveCategoryCommand(
            id: $parent->getId(), executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        self::assertNull($this->categoryRepository->findOneById($child1->getId()));
        self::assertNull($this->categoryRepository->findOneById($child2->getId()));
    }

    public function testShouldRemoveChildCategory(): void
    {
        // Given
        $parent = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $child = CategoryFactory::createOne([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'parent' => $parent,
        ]);

        $command = new RemoveCategoryCommand(
            id: $child->getId(), executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        self::assertNull($this->categoryRepository->findOneById($child->getId()));
        self::assertNotNull($this->categoryRepository->findOneById($parent->getId()));
    }

    public function testShouldDoNothingWhenCategoryNotFound(): void
    {
        // Given
        $nonExistentId = Uuid::v4();
        $existingCategory = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $command = new RemoveCategoryCommand(
            id: $nonExistentId, executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        self::assertNotNull($this->categoryRepository->findOneById($existingCategory->getId()));
    }
}
