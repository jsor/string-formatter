<?php

declare(strict_types=1);

namespace Jsor\StringFormatter\Exception;

use LogicException;

use function gettype;

class InvalidFieldDescriptorCharacterException extends LogicException
{
    public static function create(mixed $character): self
    {
        return new self(
            sprintf(
                'A field descriptor character must be a string consisting of single character, got %s (%s).',
                json_encode($character, JSON_THROW_ON_ERROR),
                json_encode(gettype($character), JSON_THROW_ON_ERROR),
            ),
        );
    }
}
