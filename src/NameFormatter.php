<?php

declare(strict_types=1);

namespace Jsor\StringFormatter;

use Jsor\LocaleData\LocaleData;
use Jsor\StringFormatter\FieldDescriptor\ChoiceFieldDescriptor;
use Jsor\StringFormatter\FieldDescriptor\ConditionalValueFieldDescriptor;
use Jsor\StringFormatter\FieldDescriptor\SimpleFieldDescriptor;
use Jsor\StringFormatter\FieldDescriptor\StaticValueFieldDescriptor;
use Jsor\StringFormatter\FieldDescriptor\ValueAliasFieldDescriptor;

final class NameFormatter implements FormatterInterface
{
    private string $locale;

    private ?string $pattern;

    private ?StringFormatter $stringFormatter = null;

    public function __construct(string $locale, string $pattern = null)
    {
        $this->locale = $locale;
        $this->pattern = $pattern;
    }

    public function format(array $values): string
    {
        if (null === $this->stringFormatter) {
            $this->stringFormatter = $this->createStringFormatter();
        }

        return $this->stringFormatter->format($values);
    }

    private function createStringFormatter(): StringFormatter
    {
        $locale = $this->locale;
        $pattern = $this->pattern;

        $localeData = LocaleData::getInstance();

        if (null === $pattern) {
            /** @var array<string, string> $data */
            $data = $localeData->getNameData($locale);
            $pattern = $data['name_fmt'];
        }

        return new StringFormatter($pattern, [
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('f'),
                [
                    'family_name',
                    'family_names',
                ],
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('F'),
                [
                    'family_name_in_uppercase',
                    'family_names_in_uppercase',
                ],
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('g'),
                [
                    'given_name',
                    'given_names',
                ],
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('G'),
                [
                    'given_initial',
                    'given_initials',
                ],
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('l'),
                [
                    'given_name_with_latin_letters',
                    'given_names_with_latin_letters',
                ],
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('o'),
                [
                    'other_shorter_name',
                    'other_shorter_names',
                ],
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('m'),
                [
                    'additional_given_names',
                    'additional_given_name',
                ],
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('M'),
                [
                    'initials_for_additional_given_name',
                    'initials_for_additional_given_names',
                    'initial_for_additional_given_name',
                    'initial_for_additional_given_names',
                ],
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('p'),
                [
                    'profession',
                    'professions',
                ],
            ),
            new ChoiceFieldDescriptor(
                new ChoiceFieldDescriptor(
                    new ValueAliasFieldDescriptor(
                        new SimpleFieldDescriptor('d'),
                        [
                            'salutation',
                            'salutations',
                        ],
                    ),
                    [
                        1 => 'name_gen',
                        2 => 'name_mr',
                        3 => 'name_mrs',
                        4 => 'name_miss',
                        5 => 'name_ms',
                    ],
                    /**
                     * @param mixed $value
                     */
                    static function ($value) {
                        if (is_numeric($value)) {
                            return null;
                        }

                        return $value;
                    },
                ),
                static function () use ($locale, $localeData): array {
                    return $localeData->getNameData($locale);
                },
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('s'),
                [
                    'full_salutation',
                    'full_salutations',
                ],
            ),
            new ValueAliasFieldDescriptor(
                new SimpleFieldDescriptor('S'),
                [
                    'abbreviated_salutation',
                    'abbreviated_salutations',
                ],
            ),
            new ConditionalValueFieldDescriptor(
                new StaticValueFieldDescriptor(
                    new SimpleFieldDescriptor('t'),
                    ' ',
                ),
            ),
            new StaticValueFieldDescriptor(
                new SimpleFieldDescriptor('%'),
                '%',
            ),
        ]);
    }
}
