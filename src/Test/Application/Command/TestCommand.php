<?php

namespace App\Test\Application\Command;

use App\Common\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

readonly class TestCommand extends Command
{
    public function __construct(
        public Uuid $id,
        public string $name,
    ) {
    }

}
