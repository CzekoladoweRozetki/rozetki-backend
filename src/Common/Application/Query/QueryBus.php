<?php

namespace App\Common\Application\Query;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus
{
    use HandleTrait;

    private MessageBusInterface $messageBus;
    public function __construct(
        private MessageBusInterface $queryBus
    )
    {
        $this->messageBus = $queryBus;
    }

    public function query(Query $query): mixed
    {
        return $this->handle($query);
    }

}
