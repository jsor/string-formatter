<?php

declare(strict_types=1);

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

final class SimpleFieldDescriptor implements FieldDescriptorInterface
{
    private string $character;

    public function __construct(string $character)
    {
        $this->character = $character;
    }

    public function getCharacter(): string
    {
        return $this->character;
    }

    public function getValue(FormatContext $context): string
    {
        return (string) $context->getValue($this->character);
    }

    public function getReplacement(string $value, FormatContext $context): string
    {
        return $value;
    }
}
