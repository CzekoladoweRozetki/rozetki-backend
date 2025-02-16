<?php

declare(strict_types=1);

namespace App\Tests\Auth\Infrastructure\EventHandler;

use App\Auth\Domain\Event\UserCreatedEvent;
use App\Common\Application\Event\EventBus;
use App\Factory\UserFactory;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\Auth\Infrastructure\EventHandler\UserRegisteredEventHandler
 */
class UserRegisteredEventHandlerTest extends KernelTestCase
{
    use Factories;

    private MailerInterface|MockObject $mailer;

    private EventBus $eventBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->mailer = $this->createMock(MailerInterface::class);
        $container->set(MailerInterface::class, $this->mailer);
        $this->eventBus = $container->get(EventBus::class);
    }

    public function testUserRegisteredEvent(): void
    {
        // Given
        $user = UserFactory::createOne();
        $event = new UserCreatedEvent($user->getId()->toString(), $user->getEmail(), 'test-activation-token');

        // Expect
        $this->mailer->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(function ($message) use ($user, $event) {
                    $this->assertSame($user->getEmail(), $message->getTo()[0]->getAddress());
                    $this->assertSame('Welcome to our website', $message->getSubject());
                    $this->assertStringContainsString(
                        'Please activate your account by clicking the link below:',
                        $message->getHtmlBody()
                    );
                    $this->assertStringContainsString(
                        'localhost/activate-account/'.$event->token,
                        $message->getHtmlBody()
                    );

                    return true;
                })
            );

        // When
        $this->eventBus->dispatch($event);
    }
}
