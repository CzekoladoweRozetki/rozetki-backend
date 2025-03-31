<?php

namespace App\Factory;

use App\PriceList\Domain\Entity\PriceChange;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PriceChange>
 */
final class PriceChangeFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return PriceChange::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @return array<string, mixed>
     *
     * @todo add your default values here
     */
    protected function defaults(): array
    {
        return [
            'endAt' => self::faker()->dateTime(),
            'price' => self::faker()->randomNumber(),
            'startFrom' => self::faker()->dateTime(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(PriceChange $priceChange): void {})
        ;
    }
}
