<?php

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

final class ConditionalValueFieldDescriptor implements FieldDescriptorInterface
{
    private $descriptor;

    public function __construct(FieldDescriptorInterface $descriptor)
    {
        $this->descriptor = $descriptor;
    }

    public function getCharacter()
    {
        return $this->descriptor->getCharacter();
    }
    public function getValue(FormatContext $context)
    {
        if ('' === $context->getPreviousValue()) {
            return '';
        }

        return $this->descriptor->getValue($context);
    }

    public function getReplacement($value, FormatContext $context)
    {
        return $this->descriptor->getReplacement($value, $context);
    }
}
