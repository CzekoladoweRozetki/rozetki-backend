<?php

declare(strict_types=1);

namespace App\Tests\Test\Domain\Repository;

use App\Test\Domain\Entity\Test;
use App\Test\Domain\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class TestRepositoryTest extends KernelTestCase
{
    private TestRepository $testRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->testRepository = self::getContainer()->get(TestRepository::class);
    }

    public function testSaveAndFindOneById(): void
    {
        $uuid = Uuid::v4();
        $testEntity = new Test($uuid, 'Test Name');

        // Save the entity
        $this->testRepository->save($testEntity);

        // Retrieve the entity
        $retrievedEntity = $this->testRepository->findOneById($uuid);

        // Assertions
        $this->assertNotNull($retrievedEntity, 'The entity was not found.');
        $this->assertEquals($testEntity->getId(), $retrievedEntity->getId(), 'The entity ID does not match.');
        $this->assertEquals($testEntity->getName(), $retrievedEntity->getName(), 'The entity name does not match.');
    }
}
