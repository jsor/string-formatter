<?php

declare(strict_types=1);

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

final class ConditionalValueFieldDescriptor implements FieldDescriptorInterface
{
    /**
     * @var FieldDescriptorInterface
     */
    private $descriptor;

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
        if ('' === (string) $context->getPreviousValue()) {
            return '';
        }

        return $this->descriptor->getValue($context);
    }

    public function getReplacement(string $value, FormatContext $context): string
    {
        return $this->descriptor->getReplacement($value, $context);
    }
}
