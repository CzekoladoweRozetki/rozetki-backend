<?php

declare(strict_types=1);

namespace App\PriceList\Application\Command\DeletePriceListCommand;

use App\PriceList\Domain\Repository\PriceListRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeletePriceListCommandHandler
{
    public function __construct(
        private readonly PriceListRepository $priceListRepository,
    ) {
    }

    public function __invoke(DeletePriceListCommand $command): void
    {
        $priceList = $this->priceListRepository->findOneById($command->id);

        if (null === $priceList) {
            return;
        }

        $this->priceListRepository->remove($priceList);
    }
}
