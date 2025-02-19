<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\GetProduct;

use App\Common\Application\Query\Query;

readonly class GetProductQuery extends Query
{
    public function __construct(
        public string $slug,
    ) {
    }
}
