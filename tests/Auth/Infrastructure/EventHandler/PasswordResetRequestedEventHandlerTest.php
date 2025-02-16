<?php

declare(strict_types=1);

namespace App\Tests\Auth\Infrastructure\EventHandler;

use App\Auth\Domain\Event\PasswordResetRequested;
use App\Common\Application\Event\EventBus;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @covers \App\Auth\Infrastructure\EventHandler\PasswordResetRequestedEventHandler
 */
class PasswordResetRequestedEventHandlerTest extends KernelTestCase
{
    private EventBus $eventBus;

    private MailerInterface|MockObject $mailer;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->eventBus = self::getContainer()->get(EventBus::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        self::getContainer()->set(MailerInterface::class, $this->mailer);
    }

    public function testPasswordResetMailSent(): void
    {
        // Given
        $event = new PasswordResetRequested(Uuid::v4(), 'user@example.com', Uuid::v4());

        // Then
        $this->mailer->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(function ($message) use ($event) {
                    $this->assertSame($event->email, $message->getTo()[0]->getAddress());
                    $this->assertSame('Reset your password', $message->getSubject());
                    $this->assertStringContainsString(
                        'Reset your password by clicking the link below:',
                        $message->getHtmlBody()
                    );
                    return true;
                }
                )
            );

        // When
        $this->eventBus->dispatch($event);
    }
}
