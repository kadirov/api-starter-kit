<?php declare(strict_types=1);

namespace App\Component\Core\Constants;

abstract class AbstractConstant
{
    abstract public static function getTexts(): array;

    public static function getText(string $constant): string
    {
        return static::getTexts()[$constant] ?? '';
    }

    public function isValid($value): bool
    {
        return (isset(static::getTexts()[$value]));
    }
}
