<?php

namespace App\Mail\Application\SendMail;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class SendMailCommandHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(SendMailCommand $command): void
    {
        $message = (new Email())
            ->from(new Address($command->fromMail, $command->fromName))
            ->to($command->email)
            ->subject($command->subject)
            ->html($command->content);
        $this->mailer->send($message);

        $log = [
            'from' => $command->fromMail,
            'to' => $command->email,
            'subject' => $command->subject,
            'content' => $command->content,
        ];
        $this->logger->info(json_encode($log));
    }
}
