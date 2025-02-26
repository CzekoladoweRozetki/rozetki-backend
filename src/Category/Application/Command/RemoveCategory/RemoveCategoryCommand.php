<?php

declare(strict_types=1);

namespace App\Category\Application\Command\RemoveCategory;

use App\Common\Application\Command\Command;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Uid\Uuid;

readonly class RemoveCategoryCommand extends Command
{
    public function __construct(
        public Uuid $id,
        public ExecutionContext $executionContext = ExecutionContext::Web,
    ) {
        parent::__construct($executionContext);
    }
}
