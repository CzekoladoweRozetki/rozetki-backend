<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Api\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryInputDTO
{
    public function __construct(
        #[NotBlank(message: 'Name should not be blank')]
        public string $name,
        public ?string $slug = null,
        public ?string $parent = null,
    ) {
    }
}
