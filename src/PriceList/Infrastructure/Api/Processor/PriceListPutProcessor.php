<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Common\Application\Command\CommandBus;
use App\PriceList\Application\Command\UpdatePriceListCommand\UpdatePriceListCommand;
use App\PriceList\Infrastructure\Api\Resource\PriceList;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<PriceList, PriceList>
 */
class PriceListPutProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {
    }

    /**
     * @param PriceList $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // Remove the dd($data) line

        $command = new UpdatePriceListCommand(
            id: Uuid::fromString($data->id),
            name: $data->name,
            currency: $data->currency,
        );

        try {
            $this->commandBus->dispatch($command);
        } catch (\Exception $e) {
            if ($e instanceof HandlerFailedException) {
                $e = $e->getPrevious();
            }
            if ($e instanceof ValidationFailedException) {
                throw new \InvalidArgumentException($e->getViolations()[0]->getMessage());
            }
            throw $e;
        }

        return new PriceList(
            id: $command->id->toString(),
            name: $command->name,
            currency: $command->currency,
        );
    }
}
