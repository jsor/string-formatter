<?php

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

final class SimpleFieldDescriptor implements FieldDescriptorInterface
{
    private $character;

    public function __construct($character)
    {
        $this->character = $character;
    }

    public function getCharacter()
    {
        return $this->character;
    }

    public function getValue(FormatContext $context)
    {
        return $context->getValue($this->character);
    }

    public function getReplacement($value, FormatContext $context)
    {
        return $value;
    }
}
