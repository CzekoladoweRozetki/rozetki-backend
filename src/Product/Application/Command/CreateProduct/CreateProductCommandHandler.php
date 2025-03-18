<?php

declare(strict_types=1);

namespace App\Product\Application\Command\CreateProduct;

use App\Category\Application\Query\GetCategory\GetCategoryQuery;
use App\Category\Domain\Exception\CategoryNotFoundException;
use App\Common\Application\Event\EventBus;
use App\Common\Application\Query\QueryBus;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Event\Partial\ProductVariant;
use App\Product\Domain\Event\ProductCreatedEvent;
use App\Product\Domain\Repository\ProductRepository;
use App\Product\Domain\Repository\ProductVariantRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class CreateProductCommandHandler
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductVariantRepository $productVariantRepository,
        private SluggerInterface $slugger,
        private EventBus $eventBus,
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(CreateProductCommand $command): void
    {
        foreach ($command->categories as $categoryId) {
            $categoryId = Uuid::fromString($categoryId);
            $category = $this->queryBus->query(
                new GetCategoryQuery($categoryId, executionContext: $command->executionContext)
            );
            if (null === $category) {
                throw new CategoryNotFoundException();
            }
        }

        $product = new Product(
            $command->id,
            $command->name,
            $command->description,
            categories: $command->categories,
        );

        $product->addAttributeValues($command->attributeValues);

        foreach ($command->variants as $variant) {
            $slug = $variant->slug;
            if (null === $variant->slug) {
                $slug = $this->slugger->slug($variant->name)->lower();
            }
            while ($this->productVariantRepository->findOneBySlug($slug->toString())) {
                $slug = $this->slugger->slug($variant->name.'-'.bin2hex(random_bytes(4)))->lower();
            }
            $variant->slug = $slug->toString();
        }

        $product->addVariants($command->variants);

        $this->productRepository->save($product);

        $event = new ProductCreatedEvent(
            $product->getId()->toString(),
            $product->getName(),
            $product->getDescription(),
            array_map(fn ($variant) => ProductVariant::fromVariant($variant), $product->getVariants()->toArray()),
            $product->getCategories(),
        );

        $this->eventBus->dispatch($event);
    }
}
