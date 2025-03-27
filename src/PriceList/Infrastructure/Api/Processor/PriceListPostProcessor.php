<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Common\Application\Command\CommandBus;
use App\PriceList\Application\Command\CreatePriceListCommand\CreatePriceListCommand;
use App\PriceList\Infrastructure\Api\DTO\PriceListInputDTO;
use App\PriceList\Infrastructure\Api\Resource\PriceList;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<PriceListInputDTO, PriceList>
 */
class PriceListPostProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new CreatePriceListCommand(
            Uuid::v4(),
            $data->name,
            $data->currency
        );

        try {
            $this->commandBus->dispatch($command);
        } catch (\Exception $e) {
            if ($e instanceof ValidationFailedException) {
                throw new \InvalidArgumentException($e->getViolations()->get(0)->getMessage());
            }
            throw $e;
        }

        return new PriceList(
            $command->id->toString(),
            $command->name,
            $command->currency
        );
    }
}
