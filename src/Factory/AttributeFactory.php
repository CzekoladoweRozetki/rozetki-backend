<?php

namespace App\Factory;

use App\Attribute\Domain\Entity\Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Attribute>
 */
final class AttributeFactory extends PersistentProxyObjectFactory
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
        return Attribute::class;
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
            'name' => self::faker()->text(255),
            'parent' => null,
            'children' => new ArrayCollection(),
            'values' => new ArrayCollection(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(Attribute $attribute): void {})
        ;
    }
}
