<?php

declare(strict_types=1);

namespace App\Tests\PriceList\Application\Query;

use App\Auth\Domain\UserRole;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\Factory\PriceListFactory;
use App\Factory\UserFactory;
use App\PriceList\Application\Query\GetPriceListCommand\GetPriceListQuery;
use App\PriceList\Application\Query\GetPriceListCommand\PriceListDTO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class GetPriceListQueryTest extends KernelTestCase
{
    use Factories;

    private MessageBusInterface $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->queryBus = self::getContainer()->get('query.bus');
    }

    public function testGetPriceListAsSystem(): void
    {
        // Create test data
        $priceList = PriceListFactory::createOne([
            'name' => 'Test Price List',
            'currency' => 'USD',
        ]);

        // Create and dispatch query with system context
        $query = new GetPriceListQuery(
            priceListId: $priceList->getId(),
            executionContext: ExecutionContext::Internal
        );

        $envelope = $this->queryBus->dispatch($query);
        $handledStamp = $envelope->last(HandledStamp::class);

        // Verify response
        $this->assertInstanceOf(HandledStamp::class, $handledStamp);
        $result = $handledStamp->getResult();
        $this->assertInstanceOf(PriceListDTO::class, $result);
        $this->assertEquals($priceList->getId(), $result->id);
        $this->assertEquals('Test Price List', $result->name);
        $this->assertEquals('USD', $result->currency);
    }

    public function testGetPriceListAsAdmin(): void
    {
        // Create admin user
        $admin = UserFactory::createOne(['roles' => [UserRole::ROLE_ADMIN->value]]);

        // Create test data
        $priceList = PriceListFactory::createOne([
            'name' => 'Admin Price List',
            'currency' => 'EUR',
        ]);

        // Login as admin
        self::getContainer()->get('security.token_storage')->setToken(
            new UsernamePasswordToken(
                $admin,
                'main',
                $admin->getRoles()
            )
        );

        // Create and dispatch query
        $query = new GetPriceListQuery(
            priceListId: $priceList->getId()
        );

        $envelope = $this->queryBus->dispatch($query);
        $handledStamp = $envelope->last(HandledStamp::class);

        // Verify response
        $result = $handledStamp->getResult();
        $this->assertEquals('Admin Price List', $result->name);
        $this->assertEquals('EUR', $result->currency);
    }

    public function testGetPriceListAsRegularUserFails(): void
    {
        // Create regular user
        $user = UserFactory::createOne(['roles' => [UserRole::ROLE_USER->value]]);

        // Create test data
        $priceList = PriceListFactory::createOne();

        // Login as regular user
        self::getContainer()->get('security.token_storage')->setToken(
            new UsernamePasswordToken(
                $user,
                'main',
                $user->getRoles()
            )
        );

        // Create query
        $query = new GetPriceListQuery(
            priceListId: $priceList->getId()
        );

        // Expect access denied
        $this->expectException(AccessDeniedException::class);
        $this->queryBus->dispatch($query);
    }

    public function testGetNonExistingPriceList(): void
    {
        // Random ID that doesn't exist in database
        $nonExistingId = Uuid::v4();

        // Create query with system context
        $query = new GetPriceListQuery(
            priceListId: $nonExistingId,
            executionContext: ExecutionContext::Internal
        );

        // Expect domain exception
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Price list not found');

        try {
            $this->queryBus->dispatch($query);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
