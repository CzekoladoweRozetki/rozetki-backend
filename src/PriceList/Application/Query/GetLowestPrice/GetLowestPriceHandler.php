<?php

declare(strict_types=1);

namespace App\PriceList\Application\Query\GetLowestPrice;

use App\PriceList\Domain\Repository\PriceListRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetLowestPriceHandler
{
    public function __construct(
        private readonly PriceListRepository $priceListRepository,
    ) {
    }

    public function __invoke(GetLowestPriceQuery $query): ?float
    {
        $priceList = $this->priceListRepository->findOneById($query->priceListId);

        if (null === $priceList) {
            throw new \InvalidArgumentException(sprintf('Price list with ID %s not found', $query->priceListId));
        }

        // Use the rich domain model - the business logic is in the PriceList entity
        return $priceList->getLowestPrice($query->productId, $query->daysToLookBack);
    }
}
