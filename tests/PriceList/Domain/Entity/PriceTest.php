<?php

declare(strict_types=1);

namespace App\Tests\PriceList\Domain\Entity;

use App\PriceList\Domain\Entity\Price;
use App\PriceList\Domain\Entity\PriceList;
use App\PriceList\Domain\ValueObject\Event\PriceCancelledEvent;
use App\PriceList\Domain\ValueObject\Event\PriceScheduledEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class PriceTest extends TestCase
{
    private Price $price;
    private PriceList $priceList;
    private string $productId = 'test-product-123';

    protected function setUp(): void
    {
        $this->priceList = $this->createMock(PriceList::class);
        $this->price = new Price(Uuid::v4(), $this->priceList, $this->productId);
    }

    public function testGettersReturnCorrectValues(): void
    {
        $this->assertEquals($this->productId, $this->price->getProductId());
        $this->assertSame($this->priceList, $this->price->getPriceList());
        $this->assertInstanceOf(Uuid::class, $this->price->getId());
    }

    public function testSchedulePriceAddsEvent(): void
    {
        // Given
        $initialAmount = 99.99;
        $effectiveFrom = new \DateTimeImmutable('+1 day');
        $reason = 'Initial price setting';

        // When
        $this->price->schedulePrice($initialAmount, $effectiveFrom, $reason);

        // Then
        $events = $this->price->getEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(PriceScheduledEvent::class, $events[0]);
        $this->assertEquals($initialAmount, $events[0]->getAmount());
        $this->assertEquals($effectiveFrom->format('Y-m-d'), $events[0]->getEffectiveFrom()->format('Y-m-d'));
        $this->assertEquals($reason, $events[0]->getReason());
    }

    public function testScheduleMultiplePrices(): void
    {
        // Given
        $currentPrice = 99.99;
        $futurePrice = 79.99;
        $now = new \DateTimeImmutable();
        $futureDate = new \DateTimeImmutable('+1 week');

        // When
        $this->price->schedulePrice($currentPrice, $now);
        $this->price->schedulePrice($futurePrice, $futureDate);

        // Then
        $events = $this->price->getEvents();
        $this->assertCount(2, $events);
        $this->assertInstanceOf(PriceScheduledEvent::class, $events[0]);
        $this->assertInstanceOf(PriceScheduledEvent::class, $events[1]);
        $this->assertEquals($currentPrice, $events[0]->getAmount());
        $this->assertEquals($futurePrice, $events[1]->getAmount());
    }

    public function testGetCurrentPrice(): void
    {
        // Given
        $oldPrice = 119.99;
        $currentPrice = 99.99;
        $futurePrice = 79.99;

        $pastDate = new \DateTimeImmutable('-1 week');
        $now = new \DateTimeImmutable();
        $futureDate = new \DateTimeImmutable('+1 week');

        $this->price->schedulePrice($oldPrice, $pastDate);
        $this->price->schedulePrice($currentPrice, $now);
        $this->price->schedulePrice($futurePrice, $futureDate);

        // When
        $result = $this->price->getCurrentPrice();

        // Then
        $this->assertEquals($currentPrice, $result);
    }

    public function testGetFuturePrice(): void
    {
        // Given
        $currentPrice = 99.99;
        $futurePrice1 = 89.99;
        $futurePrice2 = 79.99;

        $now = new \DateTimeImmutable();
        $futureDate1 = new \DateTimeImmutable('+1 week');
        $futureDate2 = new \DateTimeImmutable('+2 weeks');

        $this->price->schedulePrice($currentPrice, $now);
        $this->price->schedulePrice($futurePrice2, $futureDate2);
        $this->price->schedulePrice($futurePrice1, $futureDate1);

        // When
        $result = $this->price->getFuturePrice();

        // Then
        $this->assertIsArray($result);
        $this->assertEquals($futurePrice1, $result['price']);
        $this->assertEquals($futureDate1->format('Y-m-d'), $result['validFrom']->format('Y-m-d'));
    }

    public function testGetFuturePriceReturnsNullWhenNoFuturePrices(): void
    {
        // Given
        $currentPrice = 99.99;
        $now = new \DateTimeImmutable();

        $this->price->schedulePrice($currentPrice, $now);

        // When
        $result = $this->price->getFuturePrice();

        // Then
        $this->assertNull($result);
    }

    public function testCancelScheduledPrice(): void
    {
        // Given
        $futurePrice = 79.99;
        $futureDate = new \DateTimeImmutable('+1 week');
        $reason = 'Price promotion';
        $cancelReason = 'Promotion cancelled';

        $this->price->schedulePrice($futurePrice, $futureDate, $reason);

        // Capture the event ID to cancel it
        $events = $this->price->getEvents();
        $eventId = $events[0]->getId();

        // When
        $this->price->cancelScheduledPrice($eventId, $cancelReason);

        // Then
        $events = $this->price->getEvents();
        $this->assertCount(2, $events);
        $this->assertInstanceOf(PriceScheduledEvent::class, $events[0]);
        $this->assertInstanceOf(PriceCancelledEvent::class, $events[1]);
        $this->assertEquals($eventId, $events[1]->getCancelledEventId());
        $this->assertEquals($cancelReason, $events[1]->getReason());

        // Future price should now be null as it's cancelled
        $this->assertNull($this->price->getFuturePrice());
    }

    public function testCancelNonExistentPriceThrowsException(): void
    {
        // Given
        $nonExistentId = 'non-existent-id';

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Scheduled price event not found');

        // When
        $this->price->cancelScheduledPrice($nonExistentId);
    }

    public function testCancelAlreadyActivePriceThrowsException(): void
    {
        // Given
        $currentPrice = 99.99;
        // Use a price that's definitely active - further in the past to avoid any timing issues
        $currentDate = new \DateTimeImmutable('-7 days');

        $this->price->schedulePrice($currentPrice, $currentDate);

        // Capture the event ID to cancel it
        $events = $this->price->getEvents();
        $eventId = $events[0]->getId();

        // When & Then
        $exceptionThrown = false;
        try {
            $this->price->cancelScheduledPrice($eventId);
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = true;
            $this->assertEquals('Cannot cancel a price that is already active', $e->getMessage());
        }

        $this->assertTrue($exceptionThrown, 'Expected exception was not thrown');
    }

    public function testGetLowestPrice(): void
    {
        // Given
        $oldPrice = 89.99;
        $lowestPrice = 69.99;
        $currentPrice = 99.99;

        $oldDate = new \DateTimeImmutable('-40 days'); // Outside 30-day window
        $midDate = new \DateTimeImmutable('-15 days'); // Inside 30-day window
        $recentDate = new \DateTimeImmutable('-5 days'); // Inside 30-day window

        $this->price->schedulePrice($oldPrice, $oldDate);
        $this->price->schedulePrice($lowestPrice, $midDate);
        $this->price->schedulePrice($currentPrice, $recentDate);

        // When - with default 30 days lookback
        $result = $this->price->getLowestPrice();

        // Then
        $this->assertEquals($lowestPrice, $result);

        // When - with custom 10 days lookback
        $result10Days = $this->price->getLowestPrice(10);

        // Then
        $this->assertEquals($currentPrice, $result10Days);
    }

    public function testGetLowestPriceIgnoresCancelledPrices(): void
    {
        // Given
        $regularPrice = 99.99;
        $promotionalPrice = 79.99;

        // Schedule regular price in the past
        $this->price->schedulePrice($regularPrice, new \DateTimeImmutable('-20 days'));

        // Schedule a FUTURE promotional price (instead of past) so we can cancel it
        $futurePriceDate = new \DateTimeImmutable('+10 days');
        $this->price->schedulePrice($promotionalPrice, $futurePriceDate);

        // Then schedule another current price to test with
        $currentPrice = 89.99;
        $this->price->schedulePrice($currentPrice, new \DateTimeImmutable('-5 days'));

        $events = $this->price->getEvents();
        // Get the future promotion event (index 1)
        $promotionEventId = $events[1]->getId();
        $this->price->cancelScheduledPrice($promotionEventId, 'Promotion cancelled');

        // When
        $result = $this->price->getLowestPrice();

        // Then - should find the lowest non-cancelled price between the regular and current prices
        $expectedLowestPrice = min($regularPrice, $currentPrice);
        $this->assertEquals($expectedLowestPrice, $result);
    }

    public function testDeserializeEvents(): void
    {
        // This test verifies that the events are correctly serialized and deserialized

        // Given
        $price = 99.99;
        $effectiveFrom = new \DateTimeImmutable();
        $reason = 'Test reason';

        // When
        $this->price->schedulePrice($price, $effectiveFrom, $reason);

        // Create a new Price object with the same events to test deserialization
        $reflectionClass = new \ReflectionClass(Price::class);
        $eventsProperty = $reflectionClass->getProperty('events');
        $eventsProperty->setAccessible(true);
        $rawEvents = $eventsProperty->getValue($this->price);

        $newPrice = new Price(Uuid::v4(), $this->priceList, $this->productId);
        $eventsProperty->setValue($newPrice, $rawEvents);

        // Then
        $deserializedEvents = $newPrice->getEvents();
        $this->assertCount(1, $deserializedEvents);
        $this->assertInstanceOf(PriceScheduledEvent::class, $deserializedEvents[0]);
        $this->assertEquals($price, $deserializedEvents[0]->getAmount());
        $this->assertEquals($effectiveFrom->format('c'), $deserializedEvents[0]->getEffectiveFrom()->format('c'));
        $this->assertEquals($reason, $deserializedEvents[0]->getReason());
    }
}
