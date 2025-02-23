<?php

declare(strict_types=1);

namespace App\Category\Domain\Exception;

class CategoryAlreadyExistsException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Category already exists');
    }
}
