<?php

declare(strict_types=1);

namespace App\Mail\Infrastructure\EventHandler;

use App\Auth\Domain\Event\UserCreatedEvent;
use App\Common\Application\Command\CommandBus;
use App\Mail\Application\SendMail\SendMailCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
class UserRegisteredEventHandler
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private CommandBus $commandBus,
        private string $fromName,
        private string $fromEmail,
        private string $accountActivationUrl
    ) {
    }

    public function __invoke(UserCreatedEvent $event): void
    {
        $path = $this->accountActivationUrl . $event->token;

        $content = 'Welcome to our website. Please activate your account by clicking the link below: ' . $path;

        $command = new SendMailCommand(
            email: $event->email,
            subject: 'Welcome to our website',
            content: $content,
            fromMail: $this->fromEmail,
            fromName: $this->fromName
        );
        $this->commandBus->dispatch($command);
    }
}
