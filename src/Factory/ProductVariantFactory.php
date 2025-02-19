<?php

namespace App\Factory;

use App\Product\Domain\Entity\ProductVariant;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<ProductVariant>
 */
final class ProductVariantFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(
        // private SluggerInterface $slugger,
    ) {
    }

    public static function class(): string
    {
        return ProductVariant::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'id' => Uuid::v4(),
            'description' => self::faker()->text(),
            'name' => self::faker()->name(),
            'slug' => self::faker()->slug(4),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(ProductVariant $productVariant): void {})
        ;
    }
}
