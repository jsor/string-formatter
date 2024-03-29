<?php

declare(strict_types=1);

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\Exception\MissingFieldValueException;
use Jsor\StringFormatter\FormatContext;

final class RequiredValueFieldDescriptor implements FieldDescriptorInterface
{
    private FieldDescriptorInterface $descriptor;

    public function __construct(FieldDescriptorInterface $descriptor)
    {
        $this->descriptor = $descriptor;
    }

    public function getCharacter(): string
    {
        return $this->descriptor->getCharacter();
    }

    public function getValue(FormatContext $context): string
    {
        if (!$context->hasValue($this->descriptor->getCharacter())) {
            throw MissingFieldValueException::create($this->descriptor->getCharacter());
        }

        return $this->descriptor->getValue($context);
    }

    public function getReplacement(string $value, FormatContext $context): string
    {
        return $this->descriptor->getReplacement($value, $context);
    }
}
