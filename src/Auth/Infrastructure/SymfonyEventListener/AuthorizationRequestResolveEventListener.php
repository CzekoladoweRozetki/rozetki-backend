<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\SymfonyEventListener;

use League\Bundle\OAuth2ServerBundle\Event\AuthorizationRequestResolveEvent;
use League\Bundle\OAuth2ServerBundle\OAuth2Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: OAuth2Events::AUTHORIZATION_REQUEST_RESOLVE)]
class AuthorizationRequestResolveEventListener
{
    public function __invoke(AuthorizationRequestResolveEvent $event): void
    {
        $event->resolveAuthorization(AuthorizationRequestResolveEvent::AUTHORIZATION_APPROVED);
    }
}
