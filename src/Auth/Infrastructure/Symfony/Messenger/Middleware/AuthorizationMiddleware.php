<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Symfony\Messenger\Middleware;

use App\Common\Infrastructure\Security\AuthorizableMessage;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AuthorizationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if (!$message instanceof AuthorizableMessage) {
            return $stack->next()->handle($envelope, $stack);
        }

        if (ExecutionContext::Console === $message->getExecutionContext()
            || ExecutionContext::Internal === $message->getExecutionContext()) {
            return $stack->next()->handle($envelope, $stack);
        }

        if (!$this->authorizationChecker->isGranted(get_class($message), $message)) {
            throw new AccessDeniedException('Access denied');
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
