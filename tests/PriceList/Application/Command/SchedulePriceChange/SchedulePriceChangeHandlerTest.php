<?php

declare(strict_types=1);

namespace App\Tests\PriceList\Application\Command\SchedulePriceChange;

use App\Common\Application\Command\CommandBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\PriceList\Application\Command\SchedulePriceChange\SchedulePriceChangeCommand;
use App\PriceList\Domain\Entity\Price;
use App\PriceList\Domain\Entity\PriceList;
use App\PriceList\Domain\Repository\PriceListRepository;
use App\PriceList\Domain\ValueObject\Event\PriceScheduledEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

/**
 * @covers \App\PriceList\Application\Command\SchedulePriceChange\SchedulePriceChangeHandler
 */
class SchedulePriceChangeHandlerTest extends KernelTestCase
{
    private CommandBus $commandBus;
    private PriceListRepository $priceListRepository;
    private PriceList $priceList;

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
    }

    protected function tearDown(): void
    {
        // Clean up test data
        $this->priceListRepository->remove($this->priceList);
        parent::tearDown();
    }

    public function testScheduleNewPrice(): void
    {
        // Given
        $productId = 'product-123';
        $price = 199.99;
        $effectiveFrom = new \DateTimeImmutable('+1 day');
        $reason = 'Price promotion';

        $command = new SchedulePriceChangeCommand(
            $this->priceList->getId(),
            $productId,
            $price,
            $effectiveFrom,
            $reason,
            context: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $priceList = $this->priceListRepository->findOneById($this->priceList->getId());
        $priceEntity = $priceList->findPrice($productId);

        $this->assertInstanceOf(Price::class, $priceEntity);

        $events = $priceEntity->getEvents();
        $this->assertNotEmpty($events);

        $event = $events[0];
        $this->assertInstanceOf(PriceScheduledEvent::class, $event);
        $this->assertEquals($price, $event->getAmount());
        $this->assertEquals($effectiveFrom->format('Y-m-d'), $event->getEffectiveFrom()->format('Y-m-d'));
        $this->assertEquals($reason, $event->getReason());
    }

    public function testScheduleImmediatePrice(): void
    {
        // Given
        $productId = 'product-456';
        $price = 299.99;
        $effectiveFrom = new \DateTimeImmutable(); // immediate price

        $command = new SchedulePriceChangeCommand(
            $this->priceList->getId(),
            $productId,
            $price,
            $effectiveFrom,
            context: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $priceList = $this->priceListRepository->findOneById($this->priceList->getId());
        $currentPrice = $priceList->getCurrentPrice($productId);

        $this->assertEquals($price, $currentPrice);
    }

    public function testScheduleMultiplePrices(): void
    {
        // Given
        $productId = 'product-789';

        // First price - immediate
        $currentPrice = 99.99;
        $currentCommand = new SchedulePriceChangeCommand(
            $this->priceList->getId(),
            $productId,
            $currentPrice,
            new \DateTimeImmutable(),
            context: ExecutionContext::Internal
        );

        // Second price - future
        $futurePrice = 79.99;
        $futureDate = new \DateTimeImmutable('+3 days');
        $futureCommand = new SchedulePriceChangeCommand(
            $this->priceList->getId(),
            $productId,
            $futurePrice,
            $futureDate,
            'Scheduled discount',
            context: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($currentCommand);
        $this->commandBus->dispatch($futureCommand);

        // Then
        $priceList = $this->priceListRepository->findOneById($this->priceList->getId());

        // Check current price
        $actualCurrentPrice = $priceList->getCurrentPrice($productId);
        $this->assertEquals($currentPrice, $actualCurrentPrice);

        // Check future price
        $actualFuturePrice = $priceList->getFuturePrice($productId);
        $this->assertNotNull($actualFuturePrice);
        $this->assertEquals($futurePrice, $actualFuturePrice['price']);
        $this->assertEquals(
            $futureDate->format('Y-m-d'),
            $actualFuturePrice['validFrom']->format('Y-m-d')
        );
    }

    public function testSchedulePriceWithNonExistentPriceList(): void
    {
        // Given
        $nonExistentId = Uuid::v4();
        $command = new SchedulePriceChangeCommand(
            $nonExistentId,
            'product-123',
            199.99,
            new \DateTimeImmutable(),
            context: ExecutionContext::Internal
        );

        // When & Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Price list with ID %s not found', $nonExistentId));

        try {
            $this->commandBus->dispatch($command);
        } catch (\Symfony\Component\Messenger\Exception\HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }
}
