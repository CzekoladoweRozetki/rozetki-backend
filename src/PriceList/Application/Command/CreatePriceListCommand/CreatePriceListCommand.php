<?php

declare(strict_types=1);

namespace App\PriceList\Application\Command\CreatePriceListCommand;

use App\Common\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

readonly class CreatePriceListCommand extends Command
{
    public function __construct(
        public Uuid $id,
        public string $name,
        public string $currency,
    ) {
    }
}
