<?php

declare(strict_types=1);

namespace Jsor\StringFormatter\Exception;

use LogicException;

class InvalidFieldDescriptorCharacterException extends LogicException
{
    /**
     * @param mixed $character
     */
    public static function create($character): self
    {
        return new self(
            \sprintf(
                'A field descriptor character must be a string consisting of single character, got %s (%s).',
                \json_encode($character),
                \json_encode(\gettype($character))
            )
        );
    }
}
