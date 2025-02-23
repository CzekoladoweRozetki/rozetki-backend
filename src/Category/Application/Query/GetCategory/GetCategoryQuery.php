<?php

declare(strict_types=1);

namespace App\Category\Application\Query\GetCategory;

use App\Common\Application\Query\Query;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Uid\Uuid;

readonly class GetCategoryQuery extends Query
{
    public function __construct(
        public Uuid $id,
        public ExecutionContext $executionContext = ExecutionContext::Web,
    ) {
        parent::__construct($executionContext);
    }
}
