<?php

declare(strict_types=1);

namespace App\Tests\PriceList\Application\Command;

use App\Common\Application\Command\CommandBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\Factory\PriceListFactory;
use App\PriceList\Application\Command\DeletePriceListCommand\DeletePriceListCommand;
use App\PriceList\Domain\Repository\PriceListRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class DeletePriceListCommandTest extends KernelTestCase
{
    public function testDeleteExistingPriceList(): void
    {
        // given
        $commandBus = static::getContainer()->get(CommandBus::class);
        $id = Uuid::v4();
        $priceList = PriceListFactory::createOne(
            [
                'id' => $id,
                'name' => 'Test Price List',
                'currency' => 'USD',
            ]
        );
        $command = new DeletePriceListCommand($id, ExecutionContext::Internal);

        // when
        $commandBus->dispatch($command);

        // then

        $priceListRepository = static::getContainer()->get(PriceListRepository::class);
        $deletedPriceList = $priceListRepository->findOneById($id);
        $this->assertNull($deletedPriceList);
    }
}
