<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\GetProducts;

use App\Common\Application\Query\Query;
use App\Common\Infrastructure\Security\ExecutionContext;

readonly class GetProductsQuery extends Query
{
    /**
     * @param array<string, mixed>|null $attributes
     */
    public function __construct(
        public ?string $search = null,
        public int $page = 1,
        public int $limit = 10,
        public ?string $categorySlug = null,
        public ?array $attributes = null,
        public ExecutionContext $context = ExecutionContext::Web,
    ) {
        parent::__construct($context);
    }
}
