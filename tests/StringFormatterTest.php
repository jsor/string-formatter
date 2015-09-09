<?php

namespace Jsor\StringFormatter;

use Jsor\StringFormatter\FieldDescriptor\RequiredValueFieldDescriptor;
use Jsor\StringFormatter\FieldDescriptor\SimpleFieldDescriptor;

class StringFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_immutable()
    {
        $formatter = new StringFormatter('%a');

        $changed = $formatter->withFieldDescriptor('a');

        $this->assertNotSame($formatter, $changed);

        $changed2 = $formatter->withPattern('%b');

        $this->assertNotSame($formatter, $changed2);
        $this->assertNotSame($changed, $changed2);
    }

    /**
     * @test
     */
    public function it_replaces_field_descriptors()
    {
        $values = array(
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
            'D' => 'd',
            'E' => 'e',
            'F' => 'f',
        );

        $formatter = new StringFormatter(
            '%a %b %c %D %E %F',
            array_keys($values)
        );

        $string = $formatter->format($values);

        $this->assertSame(implode(' ', $values), $string);
    }

    /**
     * @test
     */
    public function it_silently_removes_unknown_fields_by_default()
    {
        $formatter = new StringFormatter('%u');

        $string = $formatter->format(array());

        $this->assertSame('', $string);
    }

    /**
     * @test
     */
    public function it_does_not_escape_modulo_by_default()
    {
        $formatter = new StringFormatter('%%');

        $string = $formatter->format(array());

        $this->assertSame('', $string);
    }

    /**
     * @test
     */
    public function it_allows_empty_pattern()
    {
        $formatter = new StringFormatter('');

        $string = $formatter->format(array());

        $this->assertSame('', $string);
    }

    /**
     * @test
     * @expectedException \Jsor\StringFormatter\Exception\InvalidFieldDescriptorCharacterException
     */
    public function it_throws_for_invalid_field_descriptor_character()
    {
        $formatter = new StringFormatter('', array(
            null,
        ));

        $formatter->format(array());
    }

    /**
     * @test
     * @expectedException \Jsor\StringFormatter\Exception\InvalidFieldDescriptorCharacterException
     */
    public function it_throws_for_too_long_field_descriptor_character()
    {
        $formatter = new StringFormatter('', array(
            'ab',
        ));

        $formatter->format(array());
    }

    /**
     * @test
     * @expectedException \Jsor\StringFormatter\Exception\MissingFieldDescriptorException
     */
    public function it_throws_for_missing_field_descriptor_in_strict_mode()
    {
        $formatter = new StringFormatter('%a', array(), true);

        $formatter->format(array());
    }

    /**
     * @test
     * @expectedException \Jsor\StringFormatter\Exception\MissingFieldValueException
     */
    public function it_throws_for_missing_required_field_value()
    {
        $formatter = new StringFormatter('%a', array(
            new RequiredValueFieldDescriptor(new SimpleFieldDescriptor('a'))
        ), true);

        $formatter->format(array());
    }

    /**
     * @test
     */
    public function it_passes_when_required_field_value_is_provided()
    {
        $formatter = new StringFormatter('%a', array(
            new RequiredValueFieldDescriptor(new SimpleFieldDescriptor('a'))
        ), true);

        $string = $formatter->format(array('a' => 'a'));

        $this->assertSame('a', $string);
    }

    /**
     * @test
     * @dataProvider provideUnusualFormats
     */
    public function it_handles_unusual_formats($format, $expected)
    {
        $formatter = new StringFormatter($format);

        $string = $formatter->format(array());

        $this->assertSame($expected, $string);
    }

    public function provideUnusualFormats()
    {
        return array(
            array(
                '%',
                '',
            ),
            array(
                'abc%',
                'abc',
            ),
            array(
                ' %',
                ' ',
            ),
            array(
                '% ',
                '',
            ),
            array(
                'x%',
                'x',
            ),
            array(
                '% x',
                'x',
            ),
            array(
                'x% x',
                'xx',
            ),
            array(
                'x%  x',
                'x x',
            ),
        );
    }

    /**
     * @test
     * @dataProvider provideFormatContexts
     */
    public function it_provides_correct_format_context($format, $fields, FormatContext $context, $index)
    {
        /** @var FormatContext[] $contexts */
        $contexts = array();
        $descriptors = array();

        foreach ($fields as $field => $value) {
            $mock = $this->getMock('Jsor\StringFormatter\FieldDescriptor\FieldDescriptorInterface');

            $mock
                ->expects($this->any())
                ->method('getCharacter')
                ->will($this->returnValue($field))
            ;

            $mock
                ->expects($this->once())
                ->method('getValue')
                ->will($this->returnValue($value))
            ;

            $mock
                ->expects($this->once())
                ->method('getReplacement')
                ->will($this->returnCallback(function ($value, $context) use (&$contexts) {
                    $contexts[] = $context;

                    return $value;
                }))
            ;

            $descriptors[] = $mock;
        }

        $formatter = new StringFormatter($format, $descriptors, true);

        $formatter->format($fields);

        foreach ($fields as $field => $value) {
            $this->assertSame($context->hasValue($field), $contexts[$index]->hasValue($field));
            $this->assertSame($context->getValue($field), $contexts[$index]->getValue($field));
        }
        $this->assertSame($context->isStrict(), $contexts[$index]->isStrict());
        $this->assertSame($context->getPreviousValue(), $contexts[$index]->getPreviousValue());
        $this->assertSame($context->getPreviousCharacter(), $contexts[$index]->getPreviousCharacter());
        $this->assertSame($context->getPreviousFormatCharacter(), $contexts[$index]->getPreviousFormatCharacter());
    }

    public function provideFormatContexts()
    {
        return array(
            array(
                '%ab%c',
                array('a' => 'A', 'c' => 'C'),
                new FormatContext(array('a' => 'A', 'c' => 'C'), true, 'A', 'a', 'b'),
                1,
            ),
            array(
                '%a',
                array('a' => 'A'),
                new FormatContext(array('a' => 'A'), true, null, null, null),
                0,
            ),
            array(
                ' %a',
                array('a' => 'A'),
                new FormatContext(array('a' => 'A'), true, null, null, ' '),
                0,
            ),
            array(
                'x%a%b',
                array('a' => 'A', 'b' => 'B'),
                new FormatContext(array('a' => 'A', 'b' => 'B'), true, 'A', 'a', 'x'),
                1,
            ),
        );
    }
}
