<?php

declare(strict_types=1);

namespace App\Tests\Product\Application\Command;

use App\Common\Infrastructure\Security\ExecutionContext;
use App\Factory\ProductFactory;
use App\Product\Application\Command\RemoveProduct\RemoveProductCommand;
use App\Product\Domain\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class RemoveProductCommandTest extends KernelTestCase
{
    use Factories;

    private MessageBusInterface $commandBus;
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get(MessageBusInterface::class);
        $this->productRepository = self::getContainer()->get(ProductRepository::class);
    }

    public function testRemoveProduct(): void
    {
        // Given
        $productId = Uuid::v4();
        $product = ProductFactory::createOne([
            'id' => $productId,
        ]);

        // When
        $command = new RemoveProductCommand($productId, executionContext: ExecutionContext::Internal);
        $this->commandBus->dispatch($command);

        // Then
        $removedProduct = $this->productRepository->findOneById($productId);
        $this->assertNull($removedProduct);
    }
}
