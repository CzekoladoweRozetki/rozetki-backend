<?php

declare(strict_types=1);

namespace App\Test\Application\Query;

use App\Common\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

readonly class TestQuery extends Query
{
    public function __construct(
        public Uuid $id,
    ) {
    }
}
