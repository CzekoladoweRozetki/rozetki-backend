<?php

declare(strict_types=1);

namespace App\PriceList\Application\Command\CancelFuturePrice;

use App\PriceList\Domain\Repository\PriceListRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CancelFuturePriceHandler
{
    public function __construct(
        private readonly PriceListRepository $priceListRepository,
    ) {
    }

    public function __invoke(CancelFuturePriceCommand $command): void
    {
        $priceList = $this->priceListRepository->findOneById($command->priceListId);

        if (null === $priceList) {
            throw new \InvalidArgumentException(sprintf('Price list with ID %s not found', $command->priceListId));
        }

        // Use the rich domain model - the business logic is in the aggregate
        $priceList->cancelScheduledPrice(
            $command->productId,
            $command->priceEventId,
            $command->reason
        );

        $this->priceListRepository->save($priceList);
    }
}
