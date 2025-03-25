<?php

declare(strict_types=1);

namespace App\PriceList\Application\Command\CreatePriceListCommand;

use App\PriceList\Domain\Entity\PriceList;
use App\PriceList\Domain\Repository\PriceListRepository;
use Doctrine\Common\Collections\ArrayCollection;

class CreatePriceListCommandHandler
{
    public function __construct(
        private PriceListRepository $priceListRepository,
    ) {
    }

    public function __invoke(CreatePriceListCommand $command): void
    {
        $priceList = new PriceList(
            id: $command->id,
            priceChanges: new ArrayCollection(),
            name: $command->name,
            currency: $command->currency,
        );

        $this->priceListRepository->save($priceList);
    }
}
