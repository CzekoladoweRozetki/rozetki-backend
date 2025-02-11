<?php

declare(strict_types=1);

namespace App\Tests\Product\Application\Command;

use App\Product\Application\Command\CreateProduct\CreateProductCommand;
use App\Product\Domain\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class CreateProductCommandTest extends KernelTestCase
{
    private MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get(MessageBusInterface::class);
    }

    public function testCreateProduct(): void
    {
        $command = new CreateProductCommand(
            Uuid::v4(),
            'Test Product',
            'This is a test product description.'
        );

        $this->commandBus->dispatch($command);

        // Add assertions to verify the product was created
        // For example, you can check the database or the repository
        $productRepository = self::getContainer()->get(ProductRepository::class);
        $product = $productRepository->find($command->id);

        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals('This is a test product description.', $product->getDescription());
    }
}
