<?php

declare(strict_types=1);

namespace Jsor\StringFormatter;

use Jsor\StringFormatter\Exception\InvalidFieldDescriptorCharacterException;
use Jsor\StringFormatter\Exception\MissingFieldDescriptorException;
use Jsor\StringFormatter\FieldDescriptor\FieldDescriptorInterface;
use Jsor\StringFormatter\FieldDescriptor\SimpleFieldDescriptor;

use function is_string;
use function strlen;

final class StringFormatter implements FormatterInterface
{
    private string $pattern;

    /**
     * @var FieldDescriptorInterface[]
     */
    private array $fieldDescriptors = [];

    private bool $strict;

    /**
     * @param array<string|FieldDescriptorInterface> $fieldDescriptors
     */
    public function __construct(
        string $pattern,
        array $fieldDescriptors = [],
        bool $strict = false,
    ) {
        $this->pattern = $pattern;
        $this->strict = $strict;

        foreach ($fieldDescriptors as $fieldDescriptor) {
            $this->addFieldDescriptor($fieldDescriptor);
        }
    }

    /**
     * @param string|FieldDescriptorInterface|mixed $fieldDescriptor
     */
    private function addFieldDescriptor(mixed $fieldDescriptor): void
    {
        if (!$fieldDescriptor instanceof FieldDescriptorInterface) {
            if (!is_string($fieldDescriptor)) {
                throw InvalidFieldDescriptorCharacterException::create($fieldDescriptor);
            }

            $fieldDescriptor = new SimpleFieldDescriptor($fieldDescriptor);
        }

        $this->validateFieldDescriptor($fieldDescriptor);

        $this->fieldDescriptors[$fieldDescriptor->getCharacter()] = $fieldDescriptor;
    }

    private function validateFieldDescriptor(FieldDescriptorInterface $fieldDescriptor): void
    {
        $character = $fieldDescriptor->getCharacter();

        if (1 !== strlen($character)) {
            throw InvalidFieldDescriptorCharacterException::create($character);
        }
    }

    public function withPattern(string $pattern): self
    {
        $new = new self($pattern, [], $this->strict);

        $new->fieldDescriptors = $this->fieldDescriptors;

        return $new;
    }

    public function withFieldDescriptor(string $fieldDescriptor): self
    {
        $new = new self($this->pattern, [], $this->strict);

        $new->fieldDescriptors = $this->fieldDescriptors;
        $new->addFieldDescriptor($fieldDescriptor);

        return $new;
    }

    public function format(array $values): string
    {
        $pattern = $this->pattern;
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
                        $previousFormatCharacter,
                    ),
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
                $previousFormatCharacter = $pattern[$pos - 1];

                $result .= substr($pattern, 0, $pos);
                $pattern = substr($pattern, $pos);
            }
        }

        return $result;
    }

    private function handle(string $character, FormatContext $formatContext): string
    {
        $descriptor = $this->getFieldDescriptor($character);
        $value = $descriptor->getValue($formatContext);

        return $descriptor->getReplacement($value, $formatContext);
    }

    private function getFieldDescriptor(string $character): FieldDescriptorInterface
    {
        if (isset($this->fieldDescriptors[$character])) {
            return $this->fieldDescriptors[$character];
        }

        if (!$this->strict) {
            return $this->fieldDescriptors[$character] = new SimpleFieldDescriptor($character);
        }

        throw MissingFieldDescriptorException::create($character);
    }
}
