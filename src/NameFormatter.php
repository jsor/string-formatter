<?php

namespace Jsor\StringFormatter;

use Jsor\LocaleData\LocaleData;
use Jsor\StringFormatter\FieldDescriptor\ChoiceFieldDescriptor;
use Jsor\StringFormatter\FieldDescriptor\StaticValueFieldDescriptor;
use Jsor\StringFormatter\FieldDescriptor\ValueAliasFieldDescriptor;
use Jsor\StringFormatter\FieldDescriptor\ConditionalValueFieldDescriptor;
use Jsor\StringFormatter\FieldDescriptor\SimpleFieldDescriptor;

final class NameFormatter implements FormatterInterface
{
    private $locale;
    private $stringFormatter;

    public function __construct($locale, $pattern = null)
    {
        $this->locale = $locale;
        $this->pattern = $pattern;
    }

    public function format(array $values)
    {
        if (null === $this->stringFormatter) {
            $this->stringFormatter = $this->createStringFormatter();
        }

        return $this->stringFormatter->format($values);
    }

    private function createStringFormatter()
    {
        $locale  = $this->locale;
        $pattern = $this->pattern;

        if (null === $pattern) {
            $data = LocaleData::getInstance()->getNameData($locale);
            $pattern = $data['name_fmt'];
        }

        return new StringFormatter($pattern, array(
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('f'),
                array(
                    'family_name',
                    'family_names',
                )
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('F'),
                array(
                    'family_name_in_uppercase',
                    'family_names_in_uppercase',
                )
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('g'),
                array(
                    'given_name',
                    'given_names',
                )
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('G'),
                array(
                    'given_initial',
                    'given_initials',
                )
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('l'),
                array(
                    'given_name_with_latin_letters',
                    'given_names_with_latin_letters',
                )
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('o'),
                array(
                    'other_shorter_name',
                    'other_shorter_names',
                )
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('m'),
                array(
                    'additional_given_names',
                    'additional_given_name',
                )
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('M'),
                array(
                    'initials_for_additional_given_name',
                    'initials_for_additional_given_names',
                    'initial_for_additional_given_name',
                    'initial_for_additional_given_names',
                )
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('p'),
                array(
                    'profession',
                    'professions',
                )
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('s'),
                array(
                    'salutation',
                    'salutations',
                )
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('S'),
                array(
                    'abbreviated_salutation',
                    'abbreviated_salutations',
                )
            ),
            new ChoiceFieldDescriptor(
                new ChoiceFieldDescriptor(
                    new ValueAliasFieldDescriptor(
                        new SimpleFieldDescriptor('d'),
                        array(
                            'salutation_list_index',
                            'salutations_list_index',
                        )
                    ),
                    array(
                        1 => 'name_gen',
                        2 => 'name_mr',
                        3 => 'name_mrs',
                        4 => 'name_miss',
                        5 => 'name_ms',
                    )
                ),
                function () use ($locale) {
                    return LocaleData::getInstance()->getNameData($locale);
                },
                function ($value) {
                    if (!is_string($value)) {
                        return null;
                    }

                    return $value;
                }
            ),
            new ConditionalValueFieldDescriptor(
                new StaticValueFieldDescriptor(
                    new SimpleFieldDescriptor('t'),
                    ' '
                )
            ),
            new StaticValueFieldDescriptor(
                new SimpleFieldDescriptor('%'),
                '%'
            ),
        ));
    }
}
