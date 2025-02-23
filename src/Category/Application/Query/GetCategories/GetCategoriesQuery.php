<?php

declare(strict_types=1);

namespace App\Category\Application\Query\GetCategories;

use App\Common\Application\Query\Query;
use App\Common\Infrastructure\Security\ExecutionContext;

readonly class GetCategoriesQuery extends Query
{
    public function __construct(
        public ExecutionContext $executionContext = ExecutionContext::Web,
    ) {
        parent::__construct($executionContext);
    }
}
