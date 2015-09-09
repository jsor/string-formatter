<?php

namespace Jsor\StringFormatter;

use Jsor\StringFormatter\Exception\InvalidFieldDescriptorCharacterException;
use Jsor\StringFormatter\Exception\MissingFieldDescriptorException;
use Jsor\StringFormatter\FieldDescriptor\FieldDescriptorInterface;
use Jsor\StringFormatter\FieldDescriptor\SimpleFieldDescriptor;

final class StringFormatter implements FormatterInterface
{
    private $fieldDescriptors;
    private $strict;

    public function __construct(array $fieldDescriptors = array(), $strict = false)
    {
        foreach ($fieldDescriptors as $fieldDescriptor) {
            $this->addFieldDescriptor($fieldDescriptor);
        }

        $this->strict = $strict;
    }

    public function withFieldDescriptor($fieldDescriptor)
    {
        $new = new self(array(), $this->strict);

        $new->fieldDescriptors = $this->fieldDescriptors;
        $new->addFieldDescriptor($fieldDescriptor);

        return $new;
    }

    public function format($format, array $values = array())
    {
        $format = (string) $format;
        $result = '';

        $previousValue = null;
        $previousCharacter = null;
        $previousFormatCharacter = null;

        while ('' !== $format) {
            if (preg_match('/^%(.{1})/', $format, $matches)) {
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
                $format = substr($format, 2);
            }

            // Single trailing %
            if ('%' === $format) {
                break;
            }

            $pos = strpos($format, '%');

            // No more %
            if (false === $pos) {
                $result .= $format;
                break;
            }

            if (0 !== $pos) {
                $previousFormatCharacter = substr($format, $pos - 1, 1);

                $result .= substr($format, 0, $pos);
                $format = substr($format, $pos);
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
