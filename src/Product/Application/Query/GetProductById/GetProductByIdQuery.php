<?php

declare(strict_types=1);

namespace App\Product\Application\Query\GetProductById;

use App\Common\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

readonly class GetProductByIdQuery extends Query
{
    public function __construct(
        public Uuid $id,
    ) {
    }

}
