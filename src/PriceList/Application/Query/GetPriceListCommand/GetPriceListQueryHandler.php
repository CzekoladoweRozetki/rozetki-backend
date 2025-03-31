<?php

declare(strict_types=1);

namespace App\PriceList\Application\Query\GetPriceListCommand;

use App\PriceList\Domain\Repository\PriceListRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetPriceListQueryHandler
{
    public function __construct(
        private PriceListRepository $priceListRepository,
    ) {
    }

    public function __invoke(GetPriceListQuery $query): PriceListDTO
    {
        $priceList = $this->priceListRepository->findOneById($query->priceListId);

        if (null === $priceList) {
            throw new \DomainException('Price list not found');
        }

        return new PriceListDTO(
            id: $priceList->getId(),
            name: $priceList->getName(),
            currency: $priceList->getCurrency(),
        );
    }
}
