<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Common\Application\Query\QueryBus;
use App\PriceList\Application\Query\GetPriceListCommand\GetPriceListQuery;
use App\PriceList\Application\Query\GetPriceListCommand\PriceListDTO;
use App\PriceList\Infrastructure\Api\Resource\PriceList;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

/**
 * @implements  ProviderInterface<PriceList>
 */
class PriceListSingleProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $priceListId = $uriVariables['id'] ?? null;

        if (null === $priceListId) {
            return null;
        }

        try {
            $query = new GetPriceListQuery(\Symfony\Component\Uid\Uuid::fromString($priceListId));

            /** @var PriceListDTO $priceList */
            $priceList = $this->queryBus->query($query);

            return new PriceList(
                id: $priceList->id->toString(),
                name: $priceList->name,
                currency: $priceList->currency,
            );
        } catch (\Exception $e) {
            // Rozpakuj HandlerFailedException, aby uzyskać oryginalny wyjątek
            if ($e instanceof HandlerFailedException && null !== $e->getPrevious()) {
                $e = $e->getPrevious();
            }

            // Sprawdź czy mamy DomainException z odpowiednim komunikatem
            if ($e instanceof \DomainException && str_contains($e->getMessage(), 'Price list not found')) {
                throw new NotFoundHttpException('Price list not found', $e);
            }

            throw $e;
        }
    }
}
