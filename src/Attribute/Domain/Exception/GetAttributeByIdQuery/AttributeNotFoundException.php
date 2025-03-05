<?php

declare(strict_types=1);

namespace App\Attribute\Domain\Exception\GetAttributeByIdQuery;

class AttributeNotFoundException extends \DomainException
{
    public function __construct(string $attributeId)
    {
        parent::__construct(sprintf('Attribute with id "%s" can not be found.', $attributeId));
    }
}
