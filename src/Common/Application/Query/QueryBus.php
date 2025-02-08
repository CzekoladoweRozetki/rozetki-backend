<?php

namespace App\Common\Application\Query;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus
{
    use HandleTrait;

    /**
     * @phpstan-ignore-next-line property.onlyWritten
     */
    private MessageBusInterface $messageBus;

    public function __construct(
        /**
         * @phpstan-ignore-next-line property.onlyWritten
         */
        private MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    public function query(Query $query): mixed
    {
        return $this->handle($query);
    }
}
