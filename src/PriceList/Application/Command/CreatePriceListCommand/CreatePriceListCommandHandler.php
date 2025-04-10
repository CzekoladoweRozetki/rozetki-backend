<?php

declare(strict_types=1);

namespace App\PriceList\Application\Command\CreatePriceListCommand;

use App\PriceList\Domain\Entity\PriceList;
use App\PriceList\Domain\Repository\PriceListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
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
            name: $command->name,
            currency: $command->currency,
            prices: new ArrayCollection()
        );

        $this->priceListRepository->save($priceList);
    }
}
