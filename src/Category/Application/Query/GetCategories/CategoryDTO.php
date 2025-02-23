<?php

declare(strict_types=1);

namespace App\Category\Application\Query\GetCategories;

use Symfony\Component\Uid\Uuid;

readonly class CategoryDTO
{
    /**
     * @param array<CategoryDTO> $children
     */
    public function __construct(
        public Uuid $id,
        public string $name,
        public string $slug,
        public ?Uuid $parent,
        public array $children = [],
    ) {
    }
}
