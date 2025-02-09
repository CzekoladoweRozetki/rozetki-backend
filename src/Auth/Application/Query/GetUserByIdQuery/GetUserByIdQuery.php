<?php

declare(strict_types=1);

namespace App\Auth\Application\Query\GetUserByIdQuery;

use App\Common\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

readonly class GetUserByIdQuery extends Query
{
    public function __construct(public Uuid $id)
    {
    }
}
