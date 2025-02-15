<?php

namespace App\Mail\Application\SendMail;

use App\Common\Application\Command\Command;
use Symfony\Component\Validator\Constraints as Assert;

readonly class SendMailCommand extends Command
{
    public function __construct(
        #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
        public string $email,
        public string $subject,
        public string $content,
        #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
        public string $fromMail,
        public string $fromName,
    ) {
    }
}
