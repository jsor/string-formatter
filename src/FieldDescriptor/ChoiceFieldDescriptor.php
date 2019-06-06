<?php

declare(strict_types=1);

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

final class ChoiceFieldDescriptor implements FieldDescriptorInterface
{
    /**
     * @var FieldDescriptorInterface
     */
    private $descriptor;

    /**
     * @var array|callable
     */
    private $choices;

    /**
     * @var callable|null
     */
    private $defaultValueTranslator;

    /**
     * @param array|callable $choices
     */
    public function __construct(
        FieldDescriptorInterface $descriptor,
        $choices,
        callable $defaultValueTranslator = null
    ) {
        $this->descriptor = $descriptor;
        $this->choices = $choices;
        $this->defaultValueTranslator = $defaultValueTranslator;
    }

    public function getCharacter(): string
    {
        return $this->descriptor->getCharacter();
    }

    public function getValue(FormatContext $context): string
    {
        $value = $this->descriptor->getValue($context);

        $choices = $this->getChoices($value);

        if (\array_key_exists($value, $choices)) {
            return (string) $choices[$value];
        }

        if (\is_callable($this->defaultValueTranslator)) {
            return (string) \call_user_func($this->defaultValueTranslator, $value);
        }

        return $value;
    }

    private function getChoices(string $value): array
    {
        $choices = $this->choices;

        if (\is_callable($choices)) {
            $choices = (array) $choices($value);
        }

        return $choices;
    }

    public function getReplacement(string $value, FormatContext $context): string
    {
        return $this->descriptor->getReplacement($value, $context);
    }
}
