<?php

declare(strict_types=1);

namespace App\Test\Application\Command;

use App\Common\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

readonly class UpdateTestCommand extends Command
{
    public function __construct(
        public Uuid $id,
        public string $name,
    ) {
    }

}
