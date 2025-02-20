<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\GetProducts;

use App\Common\Application\Query\Query;

readonly class GetProductsQuery extends Query
{
    public function __construct(
        public ?string $search = null,
        public int $page = 1,
        public int $limit = 10,
    ) {
    }
}
