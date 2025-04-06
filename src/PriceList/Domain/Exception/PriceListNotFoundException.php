<?php

declare(strict_types=1);

namespace App\PriceList\Domain\Exception;

class PriceListNotFoundException extends \DomainException
{
    public function __construct(string $toString)
    {
        parent::__construct(sprintf('Price list with id %s not found', $toString));
    }
}
