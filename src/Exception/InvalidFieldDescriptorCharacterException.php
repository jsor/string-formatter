<?php

namespace Jsor\StringFormatter\Exception;

class InvalidFieldDescriptorCharacterException extends \LogicException
{
    public static function create($character)
    {
        return new self(
            sprintf(
                'A field descriptor character must be a string consisting of single character, got %s (%s).',
                json_encode($character),
                json_encode(gettype($character))
            )
        );
    }
}
