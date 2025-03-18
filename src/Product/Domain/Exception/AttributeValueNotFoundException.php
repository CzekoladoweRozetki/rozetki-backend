<?php

declare(strict_types=1);

namespace App\Product\Domain\Exception;

class AttributeValueNotFoundException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Attribute value not found');
    }
}
