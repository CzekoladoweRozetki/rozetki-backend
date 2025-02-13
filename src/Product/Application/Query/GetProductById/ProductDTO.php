<?php

declare(strict_types=1);

namespace App\Product\Application\Query\GetProductById;

use Symfony\Component\Uid\Uuid;

readonly class ProductDTO
{
    public function __construct(
        public Uuid $id,
        public string $name,
        public string $description,
    ) {
    }

}
