<?php

declare(strict_types=1);

namespace App\PriceList\Application\Command\CreatePriceListCommand;

use App\Common\Application\Command\Command;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\Constraints\NotBlank;

readonly class CreatePriceListCommand extends Command
{
    public function __construct(
        public Uuid $id,
        public string $name,
        #[Currency]
        #[NotBlank]
        public string $currency,
    ) {
    }
}
