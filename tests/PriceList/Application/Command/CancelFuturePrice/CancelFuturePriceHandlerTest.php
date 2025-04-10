<?php

declare(strict_types=1);

namespace App\Tests\PriceList\Application\Command\CancelFuturePrice;

use App\Common\Application\Command\CommandBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\PriceList\Application\Command\CancelFuturePrice\CancelFuturePriceCommand;
use App\PriceList\Application\Command\SchedulePriceChange\SchedulePriceChangeCommand;
use App\PriceList\Domain\Entity\Price;
use App\PriceList\Domain\Entity\PriceList;
use App\PriceList\Domain\Repository\PriceListRepository;
use App\PriceList\Domain\ValueObject\Event\PriceCancelledEvent;
use App\PriceList\Domain\ValueObject\Event\PriceScheduledEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Uid\Uuid;

/**
 * @covers \App\PriceList\Application\Command\CancelFuturePrice\CancelFuturePriceHandler
 */
class CancelFuturePriceHandlerTest extends KernelTestCase
{
    private CommandBus $commandBus;
    private PriceListRepository $priceListRepository;
    private PriceList $priceList;
    private string $productId;
    private string $priceEventId;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get(CommandBus::class);
        $this->priceListRepository = self::getContainer()->get(PriceListRepository::class);

        // Create a test price list
        $this->priceList = new PriceList(
            Uuid::v4(),
            'Test Price List',
            'USD',
            prices: new ArrayCollection(),
        );
        $this->priceListRepository->save($this->priceList);

        // Create a future price to cancel
        $this->productId = 'product-123';
        $futureDate = new \DateTimeImmutable('+1 week');

        // Schedule a future price change
        $price = $this->priceList->schedulePrice(
            $this->productId,
            149.99,
            $futureDate,
            'Future promotion'
        );

        // Get the event ID for cancellation
        $events = $price->getEvents();
        $this->priceEventId = $events[0]->getId();

        $this->priceListRepository->save($this->priceList);
    }

    protected function tearDown(): void
    {
        // Clean up test data
        $this->priceListRepository->remove($this->priceList);
        parent::tearDown();
    }

    public function testCancelFuturePrice(): void
    {
        // Given
        $reason = 'Promotion cancelled';

        $command = new CancelFuturePriceCommand(
            $this->priceList->getId(),
            $this->productId,
            $this->priceEventId,
            $reason,
            context: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $priceList = $this->priceListRepository->findOneById($this->priceList->getId());
        $priceEntity = $priceList->findPrice($this->productId);

        $this->assertInstanceOf(Price::class, $priceEntity);

        $events = $priceEntity->getEvents();
        $this->assertCount(2, $events);

        // First event should be the price scheduled event
        $this->assertInstanceOf(PriceScheduledEvent::class, $events[0]);

        // Second event should be the cancellation
        $this->assertInstanceOf(PriceCancelledEvent::class, $events[1]);
        $this->assertEquals($this->priceEventId, $events[1]->getCancelledEventId());
        $this->assertEquals($reason, $events[1]->getReason());

        // Future price should now be null
        $futurePrice = $priceList->getFuturePrice($this->productId);
        $this->assertNull($futurePrice);
    }

    public function testCancelNonexistentFuturePrice(): void
    {
        // Given
        $nonexistentEventId = 'non-existent-id';

        $command = new CancelFuturePriceCommand(
            $this->priceList->getId(),
            $this->productId,
            $nonexistentEventId,
            context: ExecutionContext::Internal
        );

        // When & Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Scheduled price event not found');

        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }

    public function testCancelPriceForNonexistentProduct(): void
    {
        // Given
        $nonexistentProductId = 'non-existent-product';

        $command = new CancelFuturePriceCommand(
            $this->priceList->getId(),
            $nonexistentProductId,
            $this->priceEventId,
            context: ExecutionContext::Internal
        );

        // When & Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('No price found for product ID %s', $nonexistentProductId));

        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }

    public function testCancelWithNonExistentPriceList(): void
    {
        // Given
        $nonExistentId = Uuid::v4();

        $command = new CancelFuturePriceCommand(
            $nonExistentId,
            $this->productId,
            $this->priceEventId,
            context: ExecutionContext::Internal
        );

        // When & Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Price list with ID %s not found', $nonExistentId));

        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }

    public function testCannotCancelActivePrices(): void
    {
        // Given
        // Schedule an immediate price change
        $currentPrice = 99.99;
        $scheduleCommand = new SchedulePriceChangeCommand(
            $this->priceList->getId(),
            'product-current',
            $currentPrice,
            new \DateTimeImmutable(),
            context: ExecutionContext::Internal
        );

        $this->commandBus->dispatch($scheduleCommand);

        // Get the price entity with the event ID
        $priceList = $this->priceListRepository->findOneById($this->priceList->getId());
        $priceEntity = $priceList->findPrice('product-current');
        $events = $priceEntity->getEvents();
        $currentPriceEventId = $events[0]->getId();

        // Try to cancel the active price
        $cancelCommand = new CancelFuturePriceCommand(
            $this->priceList->getId(),
            'product-current',
            $currentPriceEventId,
            context: ExecutionContext::Internal
        );

        // When & Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot cancel a price that is already active');

        try {
            $this->commandBus->dispatch($cancelCommand);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }
}
