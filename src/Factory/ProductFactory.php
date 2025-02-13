<?php

declare(strict_types=1);

namespace App\Factory;

use App\Product\Domain\Entity\Product;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Product>
 */
class ProductFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array|callable
    {
        return [
            'id' => Uuid::v4(),
            'name' => self::faker()->name(),
            'description' => self::faker()->text(),
        ];
    }

    public static function class(): string
    {
        return Product::class;
    }
}
