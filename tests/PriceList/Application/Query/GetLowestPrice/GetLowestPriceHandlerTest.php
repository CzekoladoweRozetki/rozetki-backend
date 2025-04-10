<?php

declare(strict_types=1);

namespace App\Tests\PriceList\Application\Query\GetLowestPrice;

use App\Common\Application\Command\CommandBus;
use App\Common\Application\Query\QueryBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\PriceList\Application\Command\SchedulePriceChange\SchedulePriceChangeCommand;
use App\PriceList\Application\Query\GetLowestPrice\GetLowestPriceQuery;
use App\PriceList\Domain\Entity\PriceList;
use App\PriceList\Domain\Repository\PriceListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Uid\Uuid;

/**
 * @covers \App\PriceList\Application\Query\GetLowestPrice\GetLowestPriceHandler
 */
class GetLowestPriceHandlerTest extends KernelTestCase
{
    private QueryBus $queryBus;
    private CommandBus $commandBus;
    private PriceListRepository $priceListRepository;
    private PriceList $priceList;
    private string $productId;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->queryBus = self::getContainer()->get(QueryBus::class);
        $this->commandBus = self::getContainer()->get(CommandBus::class);
        $this->priceListRepository = self::getContainer()->get(PriceListRepository::class);

        // Create a test price list
        $this->priceList = new PriceList(
            Uuid::v4(),
            'Test Price List',
            'USD',
            prices: new ArrayCollection()
        );
        $this->priceListRepository->save($this->priceList);

        // Setup for Omnibus Directive (lowest price from 30 days) testing
        $this->productId = 'product-omnibus';

        // Create a series of price changes over time for testing
        $this->setupPriceHistory();
    }

    protected function tearDown(): void
    {
        // Clean up test data
        $this->priceListRepository->remove($this->priceList);
        parent::tearDown();
    }

    private function setupPriceHistory(): void
    {
        // Price 40 days ago (outside 30 day window)
        $this->schedulePrice($this->productId, 89.99, new \DateTimeImmutable('-40 days'));

        // Price 25 days ago (within 30 day window)
        $this->schedulePrice($this->productId, 79.99, new \DateTimeImmutable('-25 days'));

        // Price 15 days ago - lowest price in period
        $this->schedulePrice($this->productId, 69.99, new \DateTimeImmutable('-15 days'));

        // Price 5 days ago (within 30 day window)
        $this->schedulePrice($this->productId, 99.99, new \DateTimeImmutable('-5 days'));

        // Current price
        $this->schedulePrice($this->productId, 89.99, new \DateTimeImmutable());
    }

    private function schedulePrice(string $productId, float $price, \DateTimeImmutable $effectiveFrom): void
    {
        $command = new SchedulePriceChangeCommand(
            $this->priceList->getId(),
            $productId,
            $price,
            $effectiveFrom,
            context: ExecutionContext::Internal
        );

        $this->commandBus->dispatch($command);
    }

    public function testGetLowestPrice(): void
    {
        // Given
        $query = new GetLowestPriceQuery(
            $this->priceList->getId(),
            $this->productId,
            30,
            context: ExecutionContext::Internal
        );

        // When
        $result = $this->queryBus->query($query);

        // Then
        // The lowest price in the last 30 days should be 69.99
        $this->assertEquals(69.99, $result);
    }

    public function testGetLowestPriceWithCustomLookbackPeriod(): void
    {
        // Given
        $query = new GetLowestPriceQuery(
            $this->priceList->getId(),
            $this->productId,
            10,
            context: ExecutionContext::Internal
        );

        // When
        $result = $this->queryBus->query($query);

        // Then
        // The lowest price in the last 10 days should be 89.99
        $this->assertEquals(89.99, $result);
    }

    public function testGetLowestPriceForNonexistentProduct(): void
    {
        // Given
        $nonexistentProductId = 'non-existent-product';

        $query = new GetLowestPriceQuery(
            $this->priceList->getId(),
            $nonexistentProductId,
            context: ExecutionContext::Internal
        );

        // When
        $result = $this->queryBus->query($query);

        // Then
        // Should return null for non-existent product
        $this->assertNull($result);
    }

    public function testGetLowestPriceForNonexistentPriceList(): void
    {
        // Given
        $nonExistentId = Uuid::v4();

        $query = new GetLowestPriceQuery(
            $nonExistentId,
            $this->productId,
            context: ExecutionContext::Internal
        );

        // When & Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Price list with ID %s not found', $nonExistentId));

        try {
            $this->queryBus->query($query);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }
}
