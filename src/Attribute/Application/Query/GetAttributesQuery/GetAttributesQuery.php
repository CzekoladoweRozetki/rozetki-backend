<?php

declare(strict_types=1);

namespace App\Attribute\Application\Query\GetAttributesQuery;

use App\Common\Application\Query\Query;
use App\Common\Infrastructure\Security\ExecutionContext;

readonly class GetAttributesQuery extends Query
{
    /**
     * @param array<int, string>|null $ids
     */
    public function __construct(
        public ?array $ids = null,
        public ExecutionContext $executionContext = ExecutionContext::Web,
    ) {
        parent::__construct($executionContext);
    }
}
