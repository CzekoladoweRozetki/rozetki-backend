<?php

declare(strict_types=1);

namespace App\Attribute\Application\Command\CreateAttribute;

use App\Common\Application\Command\Command;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

readonly class CreateAttributeCommand extends Command
{
    /**
     * @param array<mixed> $values
     */
    public function __construct(
        public Uuid $id,
        #[NotBlank]
        public string $name,
        public array $values = [],
        public ?Uuid $parentId = null,
        ExecutionContext $context = ExecutionContext::Web,
    ) {
        parent::__construct($context);
    }
}
