<?php

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

final class ChoiceFieldDescriptor implements FieldDescriptorInterface
{
    private $descriptor;
    private $choices;
    private $defaultValueTranslator;

    public function __construct(
        FieldDescriptorInterface $descriptor,
        $choices,
        $defaultValueTranslator = null
    ) {
        $this->descriptor = $descriptor;
        $this->choices = $choices;
        $this->defaultValueTranslator = $defaultValueTranslator;
    }

    public function getCharacter()
    {
        return $this->descriptor->getCharacter();
    }

    public function getValue(FormatContext $context)
    {
        $value = $this->descriptor->getValue($context);

        $choices = $this->getChoices($value);

        if (isset($choices[$value])) {
            return $choices[$value];
        }

        if (is_callable($this->defaultValueTranslator)) {
            return call_user_func($this->defaultValueTranslator, $value);
        }

        return $value;
    }

    public function getReplacement($value, FormatContext $context)
    {
        return $this->descriptor->getReplacement($value, $context);
    }

    private function getChoices($value)
    {
        $choices = $this->choices;

        if (is_callable($choices)) {
            $choices = call_user_func($choices, $value);
        }

        return $choices;
    }
}
