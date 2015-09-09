<?php

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

final class ValueAliasFieldDescriptor implements FieldDescriptorInterface
{
    private $descriptor;
    private $aliases;

    public function __construct(FieldDescriptorInterface $descriptor, array $aliases)
    {
        $this->descriptor = $descriptor;
        $this->aliases = $aliases;
    }

    public function getCharacter()
    {
        return $this->descriptor->getCharacter();
    }

    public function getValue(FormatContext $context)
    {
        foreach ($this->aliases as $alias) {
            if (!$context->hasValue($alias)) {
                continue;
            }

            return $context->getValue($alias);
        }

        return $this->descriptor->getValue($context);
    }

    public function getReplacement($value, FormatContext $context)
    {
        return $this->descriptor->getReplacement($value, $context);
    }
}
