<?php

declare(strict_types=1);

namespace App\Component\Core;

use Random\RandomException;
use RuntimeException;

final class SlugGenerator
{
    private const CHARS = 'abcdefghijklmnopqrstuvwxyz0123456789';
    private const MIN_LENGTH = 8;
    private const MAX_LENGTH = 15;

    /**
     * @throws RandomException
     */
    public function generateUnique(
        callable $isUnique,
        int $startLength = self::MIN_LENGTH,
        int $maxLength = self::MAX_LENGTH
    ): string {
        $length = $startLength;

        while ($length <= $maxLength) {
            $slug = $this->generate($length);

            if ($isUnique($slug)) {
                return $slug;
            }

            $length++;
        }

        throw new RuntimeException('Unable to generate unique slug after ' . $maxLength . ' attempts');
    }

    /**
     * @throws RandomException
     */
    public function generate(int $length = self::MIN_LENGTH): string
    {
        $characters = self::CHARS;
        $maxIndex = strlen($characters) - 1;
        $slug = '';

        for ($i = 0; $i < $length; $i++) {
            $slug .= $characters[random_int(0, $maxIndex)];
        }

        return $slug;
    }
}
