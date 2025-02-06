<?php

declare(strict_types=1);

namespace App\Tests\Test\Application\Query;

use App\Common\Application\Query\QueryBus;
use App\Test\Application\Query\TestQuery;
use App\Test\Domain\Entity\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class TestQueryTest extends KernelTestCase
{
    public function testQuery(): void
    {
        self::bootKernel(['environment' => 'test']);
        $container = self::getContainer();

        $messageBus = $container->get(MessageBusInterface::class);
        $queryBus = new QueryBus($messageBus);
        $em = $container->get('doctrine.orm.entity_manager');

        $id = Uuid::v4();
        $test = new Test($id, 'Test Name');
        $em->persist($test);
        $em->flush();

        $result = $queryBus->query(new TestQuery($id));

        self::assertEquals($id, $result->id);
        self::assertEquals('Test Name', $result->name);
    }
}
