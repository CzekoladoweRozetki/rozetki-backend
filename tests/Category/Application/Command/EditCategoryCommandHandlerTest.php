<?php

declare(strict_types=1);

namespace App\Tests\Category\Application\Command;

use App\Category\Application\Command\EditCommand\EditCategoryCommand;
use App\Category\Domain\Entity\Category;
use App\Category\Domain\Repository\CategoryRepository;
use App\Common\Application\Command\CommandBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\Factory\CategoryFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class EditCategoryCommandHandlerTest extends KernelTestCase
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

    public function testShouldEditCategory(): void
    {
        // Given
        $category = CategoryFactory::createOne([
            'name' => 'Old Name',
            'slug' => 'old-slug',
        ]);

        $command = new EditCategoryCommand(
            id: $category->getId(),
            name: 'New Name',
            slug: 'new-slug',
            executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $updatedCategory = $this->categoryRepository->findOneById($category->getId());
        self::assertInstanceOf(Category::class, $updatedCategory);
        self::assertEquals('New Name', $updatedCategory->getName());
        self::assertEquals('new-slug', $updatedCategory->getSlug());
        self::assertNull($updatedCategory->getParent());
    }

    public function testShouldEditCategoryWithParent(): void
    {
        // Given
        $parent = CategoryFactory::createOne([
            'name' => 'Parent',
            'slug' => 'parent',
        ]);

        $category = CategoryFactory::createOne([
            'name' => 'Old Name',
            'slug' => 'old-slug',
        ]);

        $command = new EditCategoryCommand(
            id: $category->getId(),
            name: 'New Name',
            slug: 'new-slug',
            parent: $parent->getId(), executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $updatedCategory = $this->categoryRepository->findOneById($category->getId());
        self::assertInstanceOf(Category::class, $updatedCategory);
        self::assertEquals('New Name', $updatedCategory->getName());
        self::assertEquals('new-slug', $updatedCategory->getSlug());
        self::assertTrue($parent->getId()->equals($updatedCategory->getParent()->getId()));
    }

    public function testShouldThrowExceptionWhenCategoryNotFound(): void
    {
        // Given
        $command = new EditCategoryCommand(
            id: Uuid::v4(),
            name: 'New Name',
            slug: 'new-slug', executionContext: ExecutionContext::Internal
        );

        // When & Then
        $this->expectException(HandlerFailedException::class);
        $this->commandBus->dispatch($command);
    }

    public function testShouldThrowExceptionWhenParentNotFound(): void
    {
        // Given
        $category = CategoryFactory::createOne([
            'name' => 'Old Name',
            'slug' => 'old-slug',
        ]);

        $command = new EditCategoryCommand(
            id: $category->getId(),
            name: 'New Name',
            slug: 'new-slug',
            parent: Uuid::v4(), executionContext: ExecutionContext::Internal
        );

        // When & Then
        $this->expectException(HandlerFailedException::class);
        $this->commandBus->dispatch($command);
    }
}
