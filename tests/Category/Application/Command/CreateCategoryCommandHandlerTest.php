<?php

declare(strict_types=1);

namespace App\Tests\Category\Application\Command\CreateCategory;

use App\Category\Application\Command\CreateCategory\CreateCategoryCommand;
use App\Category\Domain\Entity\Category;
use App\Category\Domain\Exception\CategoryAlreadyExistsException;
use App\Category\Domain\Exception\CategoryNotFoundException;
use App\Category\Domain\Repository\CategoryRepository;
use App\Common\Application\Command\CommandBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\Factory\CategoryFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\Category\Application\Command\CreateCategory\CreateCategoryCommandHandler
 */
class CreateCategoryCommandHandlerTest extends KernelTestCase
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

    public function testShouldCreateCategory(): void
    {
        // Given
        $id = Uuid::v4();
        $command = new CreateCategoryCommand(
            id: $id,
            name: 'Electronics',
            slug: 'electronics', executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $category = $this->categoryRepository->findOneById($id);
        self::assertInstanceOf(Category::class, $category);
        self::assertEquals('Electronics', $category->getName());
        self::assertEquals('electronics', $category->getSlug());
        self::assertNull($category->getParent());
    }

    public function testShouldCreateSubcategory(): void
    {
        // Given
        $parent = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $id = Uuid::v4();
        $command = new CreateCategoryCommand(
            id: $id,
            name: 'Laptops',
            slug: 'laptops',
            parent: $parent->getId(), executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $category = $this->categoryRepository->findOneById($id);
        self::assertInstanceOf(Category::class, $category);
        self::assertEquals('Laptops', $category->getName());
        self::assertEquals('laptops', $category->getSlug());
        self::assertEquals($parent->getId()->toString(), $category->getParent()->getId()->toString());
    }

    public function testShouldThrowExceptionWhenCategoryExists(): void
    {
        // Given
        CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $command = new CreateCategoryCommand(
            id: Uuid::v4(),
            name: 'Electronics',
            slug: 'electronics', executionContext: ExecutionContext::Internal
        );

        // When & Then
        $this->expectException(HandlerFailedException::class);
        $this->expectException(CategoryAlreadyExistsException::class);
        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }

    public function testShouldThrowExceptionWhenParentDoesNotExist(): void
    {
        // Given
        $nonExistentParentId = Uuid::v4();
        $command = new CreateCategoryCommand(
            id: Uuid::v4(),
            name: 'Laptops',
            slug: 'laptops',
            parent: $nonExistentParentId,
            executionContext: ExecutionContext::Internal
        );

        // When & Then
        $this->expectException(HandlerFailedException::class);
        $this->expectException(CategoryNotFoundException::class);
        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }

    public function testShouldGenerateUniqueSlugWhenSlugExists(): void
    {
        // Given
        CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics-gadgets',
        ]);

        $id = Uuid::v4();
        $command = new CreateCategoryCommand(
            id: $id,
            name: 'Electronics and gadgets',
            slug: 'electronics-gadgets',
            executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $category = $this->categoryRepository->findOneById($id);
        self::assertInstanceOf(Category::class, $category);
        self::assertEquals('Electronics and gadgets', $category->getName());
        self::assertNotEquals('electronics-gadgets', $category->getSlug());
        self::assertStringStartsWith('electronics-', $category->getSlug());
    }

    public function testShouldUseNameAsSlugWhenProvidedSlugExists(): void
    {
        // Given
        CategoryFactory::createOne([
            'name' => 'Old Category',
            'slug' => 'gadgets',
        ]);

        $id = Uuid::v4();
        $command = new CreateCategoryCommand(
            id: $id,
            name: 'New Electronics',
            slug: 'gadgets',
            executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $category = $this->categoryRepository->findOneById($id);
        self::assertInstanceOf(Category::class, $category);
        self::assertEquals('New Electronics', $category->getName());
        self::assertNotEquals('gadgets', $category->getSlug());
        self::assertStringStartsWith('new-electronics', $category->getSlug());
    }

    public function testShouldGenerateRandomSlugWhenBothNameAndSlugExist(): void
    {
        // Given
        CategoryFactory::createSequence([
            ['name' => 'Old Electronics', 'slug' => 'electronics'],
            ['name' => 'New Electronics', 'slug' => 'new-electronics'],
        ]);

        $id = Uuid::v4();
        $command = new CreateCategoryCommand(
            id: $id,
            name: 'New Electronics 2',
            slug: 'electronics',
            executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $category = $this->categoryRepository->findOneById($id);
        self::assertInstanceOf(Category::class, $category);
        self::assertEquals('New Electronics 2', $category->getName());
        self::assertNotEquals('electronics', $category->getSlug());
        self::assertNotEquals('new-electronics', $category->getSlug());
        self::assertStringStartsWith('new-electronics-', $category->getSlug());
    }
}
