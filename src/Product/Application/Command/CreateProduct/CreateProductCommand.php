<?php

declare(strict_types=1);

namespace App\Product\Application\Command\CreateProduct;

use App\Common\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

readonly class CreateProductCommand extends Command
{
    public function __construct(
        public Uuid $id,
        public string $name,
        public string $description,
        /**
         * @var ProductVariantDTO[]
         */
        public array $variants = [],
    ) {
    }
}
