<?php

declare(strict_types=1);

namespace App\PriceList\Application\Command\SchedulePriceChange;

use App\PriceList\Domain\Entity\Price;
use App\PriceList\Domain\Repository\PriceListRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SchedulePriceChangeHandler
{
    public function __construct(
        private readonly PriceListRepository $priceListRepository,
    ) {
    }

    public function __invoke(SchedulePriceChangeCommand $command): Price
    {
        $priceList = $this->priceListRepository->findOneById($command->priceListId);

        if (null === $priceList) {
            throw new \InvalidArgumentException(sprintf('Price list with ID %s not found', $command->priceListId));
        }

        $effectiveFrom = $command->effectiveFrom ?? new \DateTimeImmutable();

        // Use the rich domain model - the business logic is now in the aggregate
        $price = $priceList->schedulePrice(
            $command->productId,
            $command->price,
            $effectiveFrom,
            $command->reason
        );

        // Save the aggregate root
        $this->priceListRepository->save($priceList);

        return $price;
    }
}
