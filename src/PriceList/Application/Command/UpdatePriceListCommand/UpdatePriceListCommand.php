<?php

declare(strict_types=1);

namespace App\PriceList\Application\Command\UpdatePriceListCommand;

use App\Common\Application\Command\Command;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdatePriceListCommand extends Command
{
    public function __construct(
        public Uuid $id,
        public string $name,
        #[Assert\Currency()]
        public string $currency,
        public ExecutionContext $executionContext = ExecutionContext::Web,
    ) {
        parent::__construct($this->executionContext);
    }
}
