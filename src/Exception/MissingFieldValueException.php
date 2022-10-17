<?php

declare(strict_types=1);

namespace Jsor\StringFormatter\Exception;

use RuntimeException;

class MissingFieldValueException extends RuntimeException
{
    public static function create(string $field): self
    {
        return new self(
            sprintf(
                'The value for the field %s is missing.',
                json_encode($field, JSON_THROW_ON_ERROR),
            ),
        );
    }
}
