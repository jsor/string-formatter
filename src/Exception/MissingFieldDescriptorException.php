<?php

declare(strict_types=1);

namespace Jsor\StringFormatter\Exception;

use LogicException;

class MissingFieldDescriptorException extends LogicException
{
    public static function create(string $character): self
    {
        return new self(
            sprintf(
                'Missing field description for character %s.',
                json_encode($character)
            )
        );
    }
}
