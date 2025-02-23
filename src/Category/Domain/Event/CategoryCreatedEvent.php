<?php

declare(strict_types=1);

namespace App\Category\Domain\Event;

use App\Common\Domain\Event;
use Symfony\Component\Uid\Uuid;

readonly class CategoryCreatedEvent extends Event
{
    public function __construct(
        public Uuid $id,
        public string $name,
        public ?string $slug = null,
        public ?string $parent = null,
    ) {
    }
}
