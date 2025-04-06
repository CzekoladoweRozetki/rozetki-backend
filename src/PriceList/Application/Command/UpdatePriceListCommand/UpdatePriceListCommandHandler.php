<?php

declare(strict_types=1);

namespace App\PriceList\Application\Command\UpdatePriceListCommand;

use App\PriceList\Domain\Entity\PriceList;
use App\PriceList\Domain\Exception\PriceListNotFoundException;
use App\PriceList\Domain\Repository\PriceListRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdatePriceListCommandHandler
{
    public function __construct(
        private readonly PriceListRepository $priceListRepository,
    ) {
    }

    public function __invoke(UpdatePriceListCommand $command): void
    {
        /**
         * @var PriceList|null $priceList
         */
        $priceList = $this->priceListRepository->findOneById($command->id);

        if (null === $priceList) {
            throw new PriceListNotFoundException($command->id->toString());
        }

        $priceList->update(
            name: $command->name,
            currency: $command->currency,
        );

        $this->priceListRepository->save($priceList);
    }
}
