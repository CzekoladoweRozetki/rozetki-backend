<?php

declare(strict_types=1);

namespace App\Product\Domain\Repository;

use App\Product\Domain\Entity\Product;
use Symfony\Component\Uid\Uuid;

interface ProductRepository
{
    public function save(Product $product): void;

    public function remove(Product $product): void;

    public function findById(Uuid $id): ?Product;
}
