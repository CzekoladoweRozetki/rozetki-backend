<?php

declare(strict_types=1);

namespace App\Tests\Auth\Application\Query;

use App\Auth\Application\Query\GetUserByIdQuery\GetUserByIdQuery;
use App\Auth\Application\Query\GetUserByIdQuery\UserDTO;
use App\Common\Application\Query\QueryBus;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class GetUserByIdQueryTest extends KernelTestCase
{
    use Factories;

    private QueryBus $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->queryBus = $container->get(QueryBus::class);
    }

    public function testGetUserById(): void
    {
        $userId = Uuid::v4();
        UserFactory::createOne(['id' => $userId, 'email' => 'test@example.com', 'password' => 'hashedpassword']
        );

        $query = new GetUserByIdQuery($userId);

        /** @var UserDTO $userDTO */
        $userDTO = $this->queryBus->query($query);

        $this->assertInstanceOf(UserDTO::class, $userDTO);
        $this->assertEquals($userId, $userDTO->id);
        $this->assertEquals('test@example.com', $userDTO->email);
        $this->assertEquals('hashedpassword', $userDTO->password);
    }
}
