<?php

declare(strict_types=1);

namespace App\Category\Application\Command\CreateCategory;

use App\Common\Application\Command\Command;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Uid\Uuid;

readonly class CreateCategoryCommand extends Command
{
    public function __construct(
        public Uuid $id,
        public string $name,
        public ?string $slug = null,
        public ?Uuid $parent = null,
        public ExecutionContext $executionContext = ExecutionContext::Web,
    ) {
        parent::__construct($executionContext);
    }
}
