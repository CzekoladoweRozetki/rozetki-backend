<?php

declare(strict_types=1);

namespace App\Attribute\Application\Query\GetAttributeByIdQuery;

use App\Common\Application\Query\Query;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Uid\Uuid;

readonly class GetAttributeByIdQuery extends Query
{
    public function __construct(
        public Uuid $id,
        public ExecutionContext $executionContext = ExecutionContext::Web,
    ) {
        parent::__construct($executionContext);
    }
}
