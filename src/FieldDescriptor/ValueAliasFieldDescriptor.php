<?php

declare(strict_types=1);

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

final class ValueAliasFieldDescriptor implements FieldDescriptorInterface
{
    private FieldDescriptorInterface $descriptor;

    /**
     * @var array<string>
     */
    private array $aliases;

    /**
     * @param array<string> $aliases
     */
    public function __construct(
        FieldDescriptorInterface $descriptor,
        array $aliases,
    ) {
        $this->descriptor = $descriptor;
        $this->aliases = $aliases;
    }

    public function getCharacter(): string
    {
        return $this->descriptor->getCharacter();
    }

    public function getValue(FormatContext $context): string
    {
        foreach ($this->aliases as $alias) {
            if (!$context->hasValue($alias)) {
                continue;
            }

            return (string) $context->getValue($alias);
        }

        return $this->descriptor->getValue($context);
    }

    public function getReplacement(
        string $value,
        FormatContext $context,
    ): string {
        return $this->descriptor->getReplacement($value, $context);
    }
}
