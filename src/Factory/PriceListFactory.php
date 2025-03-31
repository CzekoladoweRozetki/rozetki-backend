<?php

namespace App\Factory;

use App\PriceList\Domain\Entity\PriceList;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PriceList>
 */
final class PriceListFactory extends PersistentProxyObjectFactory
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
        return PriceList::class;
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
            'id' => Uuid::v4(),
            'currency' => self::faker()->text(),
            'name' => self::faker()->text(),
            'priceChanges' => new ArrayCollection(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(PriceList $priceList): void {})
        ;
    }
}
