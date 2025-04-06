<?php

declare(strict_types=1);

namespace App\Tests\PriceList\Application\Command;

use App\Common\Application\Command\CommandBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\Factory\PriceListFactory;
use App\PriceList\Application\Command\UpdatePriceListCommand\UpdatePriceListCommand;
use App\PriceList\Domain\Repository\PriceListRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class UpdatePriceListCommandTest extends KernelTestCase
{
    use Factories;

    public function testUpdateExistingPriceList(): void
    {
        // given
        $id = Uuid::v4();
        $originalName = 'Old Name';
        $originalCurrency = 'USD';
        $newName = 'New Name';
        $newCurrency = 'EUR';

        $command = new UpdatePriceListCommand($id, $newName, $newCurrency, ExecutionContext::Internal);
        $priceList = PriceListFactory::createOne(
            [
                'id' => $id,
                'name' => $originalName,
                'currency' => $originalCurrency,
            ]
        );

        $commandBus = static::getContainer()->get(CommandBus::class);

        // when
        $commandBus->dispatch($command);

        // then
        $priceListRepository = static::getContainer()->get(PriceListRepository::class);
        $updatedPriceList = $priceListRepository->findOneById($id);
        $this->assertNotNull($updatedPriceList);
        $this->assertEquals($newName, $updatedPriceList->getName());
        $this->assertEquals($newCurrency, $updatedPriceList->getCurrency());
    }
}
