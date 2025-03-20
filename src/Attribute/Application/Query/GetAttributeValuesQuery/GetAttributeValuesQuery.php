<?php

declare(strict_types=1);

namespace App\Attribute\Application\Query\GetAttributeValuesQuery;

use App\Common\Application\Query\Query;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Uid\Uuid;

readonly class GetAttributeValuesQuery extends Query
{
    /**
     * @param array<int, Uuid> $ids
     */
    public function __construct(
        public array $ids,
        public ExecutionContext $executionContext = ExecutionContext::Web,
    ) {
        parent::__construct($executionContext);
    }
}
