<?php

declare(strict_types=1);

namespace App\Category\Domain\Exception;

class CategoryNotFoundException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Category not found');
    }
}
