<?php

declare(strict_types=1);

namespace App\Test\Application\Query;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TestQueryHandler
{
    public function __invoke(TestQuery $query): string
    {
        return 'Test query executed';
    }
}
