<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\EventHandler;

use App\Auth\Domain\Event\PasswordResetRequested;
use App\Common\Application\Command\CommandBus;
use App\Mail\Application\SendMail\SendMailCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class PasswordResetRequestedEventHandler
{
    public function __construct(
        private CommandBus $commandBus,
        private string $fromName,
        private string $fromEmail,
        private string $passwordResetUrl,
    ) {
    }

    public function __invoke(PasswordResetRequested $event): void
    {
        $content = 'Reset your password by clicking the link below: '.$this->passwordResetUrl.$event->token;

        $command = new SendMailCommand(
            $event->email, 'Reset your password', $content, $this->fromEmail, $this->fromName
        );
        $this->commandBus->dispatch($command);
    }
}
