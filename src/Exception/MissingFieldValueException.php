<?php

namespace Jsor\StringFormatter\Exception;

class MissingFieldValueException extends \RuntimeException
{
    public static function create($field)
    {
        return new self(
            sprintf(
                'The value for the field %s is missing.',
                json_encode($field)
            )
        );
    }
}
