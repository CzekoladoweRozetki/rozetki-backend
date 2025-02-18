<?php

declare(strict_types=1);

namespace App\Tests\Product\Application\Query;

use App\Common\Application\Query\QueryBus;
use App\Factory\ProductFactory;
use App\Product\Application\Query\DTO\ProductDTO;
use App\Product\Application\Query\GetProductById\GetProductByIdQuery;
use App\Product\Domain\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

class GetProductByIdQueryTest extends KernelTestCase
{
    use Factories;

    private QueryBus $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->queryBus = self::getContainer()->get(QueryBus::class);
    }

    public function testGetProductById(): void
    {
        /**
         * @var Product $product
         */
        $product = ProductFactory::createOne();
        // Assuming a product with this ID exists in the database
        $query = new GetProductByIdQuery($product->getId());

        /** @var ProductDTO $productDTO */
        $productDTO = $this->queryBus->query($query);

        $this->assertEquals($product->getId(), $productDTO->id);
        $this->assertEquals($product->getName(), $productDTO->name);
        $this->assertEquals($product->getDescription(), $productDTO->description);
    }
}
