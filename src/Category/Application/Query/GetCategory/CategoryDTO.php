<?php

declare(strict_types=1);

namespace App\Category\Application\Query\GetCategory;

use Symfony\Component\Uid\Uuid;

readonly class CategoryDTO
{
    public function __construct(
        public Uuid $id,
        public string $name,
        public string $slug,
        public ?Uuid $parent = null,
    ) {
    }
}
