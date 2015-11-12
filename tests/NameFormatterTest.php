<?php

namespace Jsor;

use Jsor\StringFormatter\NameFormatter;

class NameFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_replaces_with_default_format()
    {
        $values = array(
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
        );

        $formatter = new NameFormatter(null);

        $name = $formatter->format($values);

        $this->assertSame('profession given_name additional_given_names family_names', $name);
    }

    /**
     * @test
     */
    public function it_replaces_all_values()
    {
        $values = array(
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
        );

        $formatter = new NameFormatter('en_US', '%f%F%g%G%l%o%m%M%p%d%s%S%t');

        $name = $formatter->format($values);

        $parts = array_merge($values, array(
            'd' => 'Mr.',
            't' => ' ',
        ));

        $this->assertSame(implode('', $parts), $name);
    }

    /**
     * @test
     */
    public function it_replaces_all_values_with_aliases()
    {
        $values = array(
            'family_names'                        => 'family_names',
            'family_names_in_uppercase'           => 'family_names_in_uppercase',
            'given_name'                          => 'given_name',
            'given_initial'                       => 'given_initial',
            'given_name_with_latin_letters'       => 'given_name_with_latin_letters',
            'other_shorter_name'                  => 'other_shorter_name',
            'additional_given_names'              => 'additional_given_names',
            'initials_for_additional_given_names' => 'initials_for_additional_given_names',
            'profession'                          => 'profession',
            'salutation'                          => 2,
            'full_salutation'                     => 'full_salutation',
            'abbreviated_salutation'              => 'abbreviated_salutation',
        );

        $formatter = new NameFormatter('en_US', '%f%F%g%G%l%o%m%M%p%d%s%S%t');

        $name = $formatter->format($values);

        $parts = array_merge($values, array(
            'salutation' => 'Mr.',
            't'          => ' ',
        ));

        $this->assertSame(implode('', $parts), $name);
    }

    /**
     * @test
     */
    public function it_replaces_saluation_with_default_value()
    {
        $values = array(
            'd' => 'Custom Salutation',
        );

        $formatter = new NameFormatter('en_US', '%d');

        $name = $formatter->format($values);

        $this->assertSame('Custom Salutation', $name);
    }

    /**
     * @test
     */
    public function it_keeps_escaped_modulo()
    {
        $formatter = new NameFormatter('en_US', '%%');

        $name = $formatter->format(array());

        $this->assertSame('%', $name);
    }

    /**
     * @test
     */
    public function it_allows_empty_values()
    {
        $formatter = new NameFormatter('en_US', '%f%F%g%G%l%o%m%M%p%s%S%d%t');

        $name = $formatter->format(array());

        $this->assertSame('', $name);
    }

    /**
     * @test
     */
    public function it_silenty_ignores_unknown_custom_salutation_lists_index()
    {
        $values = array(
            'd' => 10,
        );

        $formatter = new NameFormatter('en_US', '%d');

        $name = $formatter->format($values);

        $this->assertSame('', $name);
    }
}
