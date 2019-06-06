<?php

namespace Jsor\StringFormatter;

use PHPUnit\Framework\TestCase;

class NameFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function it_replaces_with_default_format(): void
    {
        $values = [
            'f' => 'family_names',
            'F' => 'family_names_in_uppercase',
            'g' => 'given_name',
            'G' => 'given_initial',
            'l' => 'given_name_with_latin_letters',
            'o' => 'other_shorter_name',
            'm' => 'additional_given_names',
            'M' => 'initials_for_additional_given_names',
            'p' => 'profession',
            'd' => 2,
            's' => 'full_salutation',
            'S' => 'abbreviated_salutation',
        ];

        $formatter = new NameFormatter('');

        $name = $formatter->format($values);

        self::assertSame('profession given_name additional_given_names family_names', $name);
    }

    /**
     * @test
     */
    public function it_replaces_all_values(): void
    {
        $values = [
            'f' => 'family_names',
            'F' => 'family_names_in_uppercase',
            'g' => 'given_name',
            'G' => 'given_initial',
            'l' => 'given_name_with_latin_letters',
            'o' => 'other_shorter_name',
            'm' => 'additional_given_names',
            'M' => 'initials_for_additional_given_names',
            'p' => 'profession',
            'd' => 2,
            's' => 'full_salutation',
            'S' => 'abbreviated_salutation',
        ];

        $formatter = new NameFormatter('en_US', '%f%F%g%G%l%o%m%M%p%d%s%S%t');

        $name = $formatter->format($values);

        $parts = \array_merge($values, [
            'd' => 'Mr.',
            't' => ' ',
        ]);

        self::assertSame(\implode('', $parts), $name);
    }

    /**
     * @test
     */
    public function it_replaces_all_values_with_aliases(): void
    {
        $values = [
            'family_names' => 'family_names',
            'family_names_in_uppercase' => 'family_names_in_uppercase',
            'given_name' => 'given_name',
            'given_initial' => 'given_initial',
            'given_name_with_latin_letters' => 'given_name_with_latin_letters',
            'other_shorter_name' => 'other_shorter_name',
            'additional_given_names' => 'additional_given_names',
            'initials_for_additional_given_names' => 'initials_for_additional_given_names',
            'profession' => 'profession',
            'salutation' => 2,
            'full_salutation' => 'full_salutation',
            'abbreviated_salutation' => 'abbreviated_salutation',
        ];

        $formatter = new NameFormatter('en_US', '%f%F%g%G%l%o%m%M%p%d%s%S%t');

        $name = $formatter->format($values);

        $parts = \array_merge($values, [
            'salutation' => 'Mr.',
            't' => ' ',
        ]);

        self::assertSame(\implode('', $parts), $name);
    }

    /**
     * @test
     */
    public function it_replaces_saluation_with_default_value(): void
    {
        $values = [
            'd' => 'Custom Salutation',
        ];

        $formatter = new NameFormatter('en_US', '%d');

        $name = $formatter->format($values);

        self::assertSame('Custom Salutation', $name);
    }

    /**
     * @test
     */
    public function it_keeps_escaped_modulo(): void
    {
        $formatter = new NameFormatter('en_US', '%%');

        $name = $formatter->format([]);

        self::assertSame('%', $name);
    }

    /**
     * @test
     */
    public function it_allows_empty_values(): void
    {
        $formatter = new NameFormatter('en_US', '%f%F%g%G%l%o%m%M%p%s%S%d%t');

        $name = $formatter->format([]);

        self::assertSame('', $name);
    }

    /**
     * @test
     */
    public function it_silenty_ignores_unknown_custom_salutation_lists_index(): void
    {
        $values = [
            'd' => 10,
        ];

        $formatter = new NameFormatter('en_US', '%d');

        $name = $formatter->format($values);

        self::assertSame('', $name);
    }
}
