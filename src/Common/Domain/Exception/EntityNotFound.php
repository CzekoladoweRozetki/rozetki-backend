<?php

declare(strict_types=1);

namespace App\Common\Domain\Exception;

class EntityNotFound extends \Exception
{

    public function __construct()
    {
        parent::__construct('Entity not found');
    }
}
