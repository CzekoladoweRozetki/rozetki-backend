<?php

declare(strict_types=1);

namespace App\Tests\Test\Application\Query;

use App\Common\Application\Query\QueryBus;
use App\Test\Application\Query\TestQuery;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class TestQueryTest extends KernelTestCase
{
    public function testQuery(): void
    {
        self::bootKernel(['environment' => 'test']);
        $container = self::getContainer();

        $messageBus = $container->get(MessageBusInterface::class);
        $queryBus = new QueryBus($messageBus);

        $result = $queryBus->query(new TestQuery());

        self::assertEquals('Test query executed', $result);
    }
}
