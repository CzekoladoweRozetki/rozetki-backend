<?php

namespace App\Common\Application\Event;

use App\Common\Domain\Event;
use Symfony\Component\Messenger\MessageBusInterface;

class EventBus
{
    public function __construct(
        private MessageBusInterface $eventBus
    )
    {
    }

    public function dispatch(Event $event): void
    {
        $this->eventBus->dispatch($event);
    }

}
