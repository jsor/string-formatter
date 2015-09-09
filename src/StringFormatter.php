<?php

namespace Jsor\StringFormatter;

use Jsor\StringFormatter\Exception\InvalidFieldDescriptorCharacterException;
use Jsor\StringFormatter\Exception\MissingFieldDescriptorException;
use Jsor\StringFormatter\FieldDescriptor\FieldDescriptorInterface;
use Jsor\StringFormatter\FieldDescriptor\SimpleFieldDescriptor;

final class StringFormatter implements FormatterInterface
{
    private $pattern;
    private $fieldDescriptors;
    private $strict;

    public function __construct(
        $pattern,
        array $fieldDescriptors = array(),
        $strict = false
    ) {
        $this->pattern = $pattern;
        $this->strict = $strict;

        foreach ($fieldDescriptors as $fieldDescriptor) {
            $this->addFieldDescriptor($fieldDescriptor);
        }
    }

    public function withPattern($pattern)
    {
        $new = new self($pattern, array(), $this->strict);

        $new->fieldDescriptors = $this->fieldDescriptors;

        return $new;
    }

    public function withFieldDescriptor($fieldDescriptor)
    {
        $new = new self($this->pattern, array(), $this->strict);

        $new->fieldDescriptors = $this->fieldDescriptors;
        $new->addFieldDescriptor($fieldDescriptor);

        return $new;
    }

    public function format(array $values)
    {
        $pattern = (string) $this->pattern;
        $result = '';

        $previousValue = null;
        $previousCharacter = null;
        $previousFormatCharacter = null;

        while ('' !== $pattern) {
            if (preg_match('/^%(.{1})/', $pattern, $matches)) {
                $value = $this->handle(
                    $matches[1],
                    new FormatContext(
                        $values,
                        $this->strict,
                        $previousValue,
                        $previousCharacter,
                        $previousFormatCharacter
                    )
                );

                $previousValue = $value;
                $previousCharacter = $matches[1];

                $result .= $value;
                $pattern = substr($pattern, 2);
            }

            // Single trailing %
            if ('%' === $pattern) {
                break;
            }

            $pos = strpos($pattern, '%');

            // No more %
            if (false === $pos) {
                $result .= $pattern;
                break;
            }

            if (0 !== $pos) {
                $previousFormatCharacter = substr($pattern, $pos - 1, 1);

                $result .= substr($pattern, 0, $pos);
                $pattern = substr($pattern, $pos);
            }
        }

        return $result;
    }

    private function handle($character, FormatContext $formatContext)
    {
        $descriptor = $this->getFieldDescriptor($character);
        $value = $descriptor->getValue($formatContext);

        return $descriptor->getReplacement($value, $formatContext);
    }

    /**
     * @return FieldDescriptorInterface
     */
    private function getFieldDescriptor($character)
    {
        if (isset($this->fieldDescriptors[$character])) {
            return $this->fieldDescriptors[$character];
        }

        if (!$this->strict) {
            return $this->fieldDescriptors[$character] = new SimpleFieldDescriptor($character);
        }

        throw MissingFieldDescriptorException::create($character);
    }

    private function addFieldDescriptor($fieldDescriptor)
    {
        if (!$fieldDescriptor instanceof FieldDescriptorInterface) {
            $fieldDescriptor = new SimpleFieldDescriptor($fieldDescriptor);
        }

        $this->validateFieldDescriptor($fieldDescriptor);

        $this->fieldDescriptors[(string) $fieldDescriptor->getCharacter()] = $fieldDescriptor;
    }

    private function validateFieldDescriptor(FieldDescriptorInterface $fieldDescriptor)
    {
        $character = $fieldDescriptor->getCharacter();

        if (!is_string($character) || 1 !== strlen($character)) {
            throw InvalidFieldDescriptorCharacterException::create($character);
        }
    }
}
