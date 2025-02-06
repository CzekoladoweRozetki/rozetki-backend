<?php

declare(strict_types=1);

namespace App\Test\Domain\Repository;

use App\Test\Domain\Entity\Test;
use Symfony\Component\Uid\Uuid;

interface TestRepository
{
    public function findOneById(Uuid $id): ?Test;

    public function save(Test $test): void;

}
