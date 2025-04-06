<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Common\Application\Command\CommandBus;
use App\PriceList\Application\Command\DeletePriceListCommand\DeletePriceListCommand;
use App\PriceList\Infrastructure\Api\Resource\PriceList;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<PriceList, null>
 */
class PriceListDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {
    }

    /**
     * @param PriceList $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new DeletePriceListCommand(Uuid::fromString($data->id));
        $this->commandBus->dispatch($command);

        return null;
    }
}
