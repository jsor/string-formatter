<?php

namespace Jsor\StringFormatter;

use Jsor\StringFormatter\Exception\InvalidFieldDescriptorCharacterException;
use Jsor\StringFormatter\Exception\MissingFieldDescriptorException;
use Jsor\StringFormatter\Exception\MissingFieldValueException;
use Jsor\StringFormatter\FieldDescriptor\FieldDescriptorInterface;
use Jsor\StringFormatter\FieldDescriptor\RequiredValueFieldDescriptor;
use Jsor\StringFormatter\FieldDescriptor\SimpleFieldDescriptor;
use PHPUnit\Framework\TestCase;

class StringFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_immutable(): void
    {
        $formatter = new StringFormatter('%a');

        $changed = $formatter->withFieldDescriptor('a');

        self::assertNotSame($formatter, $changed);

        $changed2 = $formatter->withPattern('%b');

        self::assertNotSame($formatter, $changed2);
        self::assertNotSame($changed, $changed2);
    }

    /**
     * @test
     */
    public function it_replaces_field_descriptors(): void
    {
        $values = [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
            'D' => 'd',
            'E' => 'e',
            'F' => 'f',
        ];

        $formatter = new StringFormatter(
            '%a %b %c %D %E %F',
            \array_keys($values)
        );

        $string = $formatter->format($values);

        self::assertSame(\implode(' ', $values), $string);
    }

    /**
     * @test
     */
    public function it_silently_removes_unknown_fields_by_default(): void
    {
        $formatter = new StringFormatter('%u');

        $string = $formatter->format([]);

        self::assertSame('', $string);
    }

    /**
     * @test
     */
    public function it_does_not_escape_modulo_by_default(): void
    {
        $formatter = new StringFormatter('%%');

        $string = $formatter->format([]);

        self::assertSame('', $string);
    }

    /**
     * @test
     */
    public function it_allows_empty_pattern(): void
    {
        $formatter = new StringFormatter('');

        $string = $formatter->format([]);

        self::assertSame('', $string);
    }

    /**
     * @test
     */
    public function it_throws_for_invalid_field_descriptor_character(): void
    {
        $this->expectException(InvalidFieldDescriptorCharacterException::class);
        $this->expectExceptionMessage('A field descriptor character must be a string consisting of single character, got null ("NULL").');

        /** @psalm-suppress InvalidArgument */
        new StringFormatter('', [
            null,
        ]);
    }

    /**
     * @test
     */
    public function it_throws_for_too_long_field_descriptor_character(): void
    {
        $this->expectException(InvalidFieldDescriptorCharacterException::class);
        $this->expectExceptionMessage('A field descriptor character must be a string consisting of single character, got "ab" ("string").');

        new StringFormatter('', [
            'ab',
        ]);
    }

    /**
     * @test
     */
    public function it_throws_for_missing_field_descriptor_in_strict_mode(): void
    {
        $this->expectException(MissingFieldDescriptorException::class);
        $this->expectExceptionMessage('Missing field description for character "a".');

        $formatter = new StringFormatter('%a', [], true);

        $formatter->format([]);
    }

    /**
     * @test
     */
    public function it_throws_for_missing_required_field_value(): void
    {
        $this->expectException(MissingFieldValueException::class);
        $this->expectExceptionMessage('The value for the field "a" is missing.');

        $formatter = new StringFormatter('%a', [
            new RequiredValueFieldDescriptor(new SimpleFieldDescriptor('a')),
        ], true);

        $formatter->format([]);
    }

    /**
     * @test
     */
    public function it_passes_when_required_field_value_is_provided(): void
    {
        $formatter = new StringFormatter('%a', [
            new RequiredValueFieldDescriptor(new SimpleFieldDescriptor('a')),
        ], true);

        $string = $formatter->format(['a' => 'a']);

        self::assertSame('a', $string);
    }

    /**
     * @test
     * @dataProvider provideUnusualFormats
     */
    public function it_handles_unusual_formats(string $format, string $expected): void
    {
        $formatter = new StringFormatter($format);

        $string = $formatter->format([]);

        self::assertSame($expected, $string);
    }

    /**
     * @return array<array-key, array<array-key, string>>
     */
    public function provideUnusualFormats(): array
    {
        return [
            [
                '%',
                '',
            ],
            [
                'abc%',
                'abc',
            ],
            [
                ' %',
                ' ',
            ],
            [
                '% ',
                '',
            ],
            [
                'x%',
                'x',
            ],
            [
                '% x',
                'x',
            ],
            [
                'x% x',
                'xx',
            ],
            [
                'x%  x',
                'x x',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider provideFormatContexts
     *
     * @param array<string, string> $fields
     */
    public function it_provides_correct_format_context(string $format, array $fields, FormatContext $context, int $index): void
    {
        /** @var FormatContext[] $contexts */
        $contexts = [];

        /** @var FieldDescriptorInterface[] $descriptors */
        $descriptors = [];

        foreach ($fields as $field => $value) {
            $mock = $this->getMockBuilder(FieldDescriptorInterface::class)->getMock();

            $mock
                ->method('getCharacter')
                ->willReturn($field);

            $mock
                ->expects(self::once())
                ->method('getValue')
                ->willReturn($value);

            $mock
                ->expects(self::once())
                ->method('getReplacement')
                ->willReturnCallback(
                    static function (string $value, FormatContext $context) use (&$contexts): string {
                        $contexts[] = $context;

                        return $value;
                    }
                );

            $descriptors[] = $mock;
        }

        $formatter = new StringFormatter($format, $descriptors, true);

        $formatter->format($fields);

        foreach ($fields as $field => $value) {
            self::assertEquals($context->hasValue($field), $contexts[$index]->hasValue($field));
            self::assertEquals($context->getValue($field), $contexts[$index]->getValue($field));
        }
        self::assertEquals($context->isStrict(), $contexts[$index]->isStrict());
        self::assertEquals($context->getPreviousValue(), $contexts[$index]->getPreviousValue());
        self::assertEquals($context->getPreviousCharacter(), $contexts[$index]->getPreviousCharacter());
        self::assertEquals($context->getPreviousFormatCharacter(), $contexts[$index]->getPreviousFormatCharacter());
    }

    /**
     * @return array<array-key, array{0:string, 1:array<string, string>, 2:FormatContext, 3:int}>
     */
    public function provideFormatContexts(): array
    {
        return [
            [
                '%ab%c',
                ['a' => 'A', 'c' => 'C'],
                new FormatContext(['a' => 'A', 'c' => 'C'], true, 'A', 'a', 'b'),
                1,
            ],
            [
                '%a',
                ['a' => 'A'],
                new FormatContext(['a' => 'A'], true, null, null, null),
                0,
            ],
            [
                ' %a',
                ['a' => 'A'],
                new FormatContext(['a' => 'A'], true, null, null, ' '),
                0,
            ],
            [
                'x%a%b',
                ['a' => 'A', 'b' => 'B'],
                new FormatContext(['a' => 'A', 'b' => 'B'], true, 'A', 'a', 'x'),
                1,
            ],
        ];
    }
}
