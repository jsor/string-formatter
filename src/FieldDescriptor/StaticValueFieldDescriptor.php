<?php

declare(strict_types=1);

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

final class StaticValueFieldDescriptor implements FieldDescriptorInterface
{
    /**
     * @var FieldDescriptorInterface
     */
    private $descriptor;

    /**
     * @var string
     */
    private $value;

    public function __construct(FieldDescriptorInterface $descriptor, string $value)
    {
        $this->descriptor = $descriptor;
        $this->value = $value;
    }

    public function getCharacter(): string
    {
        return $this->descriptor->getCharacter();
    }

    public function getValue(FormatContext $context): string
    {
        return $this->value;
    }

    public function getReplacement(string $value, FormatContext $context): string
    {
        return $this->descriptor->getReplacement($value, $context);
    }
}
