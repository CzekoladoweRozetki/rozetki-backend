<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TsVectorType extends Type
{
    public const TSVECTOR = 'tsvector';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'tsvector';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return $value; // Stored as string
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value;
    }

    public function getName(): string
    {
        return self::TSVECTOR;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
