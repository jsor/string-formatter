<?php

namespace Jsor\StringFormatter\Exception;

class MissingFieldDescriptorException extends \LogicException
{
    public static function create($character)
    {
        return new self(
            sprintf(
                'Missing field description for character %s.',
                json_encode($character)
            )
        );
    }
}
