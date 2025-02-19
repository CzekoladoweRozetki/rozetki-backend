<?php

declare(strict_types=1);

namespace App\Product\Application\Command\CreateProduct;

use App\Common\Application\Event\EventBus;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Event\Partial\ProductVariant;
use App\Product\Domain\Event\ProductCreatedEvent;
use App\Product\Domain\Repository\ProductRepository;
use App\Product\Domain\Repository\ProductVariantRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsMessageHandler]
class CreateProductCommandHandler
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductVariantRepository $productVariantRepository,
        private SluggerInterface $slugger,
        private EventBus $eventBus,
    ) {
    }

    public function __invoke(CreateProductCommand $command): void
    {
        $product = new Product(
            $command->id,
            $command->name,
            $command->description
        );

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
            array_map(fn ($variant) => ProductVariant::fromVariant($variant), $product->getVariants()->toArray())
        );

        $this->eventBus->dispatch($event);
    }
}
