<?php

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

final class StaticValueFieldDescriptor implements FieldDescriptorInterface
{
    private $descriptor;
    private $value;

    public function __construct(FieldDescriptorInterface $descriptor, $value)
    {
        $this->descriptor = $descriptor;
        $this->value = $value;
    }

    public function getCharacter()
    {
        return $this->descriptor->getCharacter();
    }

    public function getValue(FormatContext $context)
    {
        return $this->value;
    }

    public function getReplacement($value, FormatContext $context)
    {
        return $this->descriptor->getReplacement($value, $context);
    }
}
