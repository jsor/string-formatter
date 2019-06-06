<?php

declare(strict_types=1);

namespace Jsor\StringFormatter;

final class FormatContext
{
    /**
     * @var array<string, string|int>
     */
    private $values;

    /**
     * @var bool
     */
    private $strict;

    /**
     * @var string|null
     */
    private $previousValue;

    /**
     * @var string|null
     */
    private $previousCharacter;

    /**
     * @var string|null
     */
    private $previousFormatCharacter;

    /**
     * @param array<string, string|int> $values
     */
    public function __construct(
        array $values,
        bool $strict,
        ?string $previousValue,
        ?string $previousCharacter,
        ?string $previousFormatCharacter
    ) {
        $this->values = $values;
        $this->strict = $strict;
        $this->previousValue = $previousValue;
        $this->previousCharacter = $previousCharacter;
        $this->previousFormatCharacter = $previousFormatCharacter;
    }

    public function hasValue(string $key): bool
    {
        return \array_key_exists($key, $this->values);
    }

    /**
     * @return mixed|null
     */
    public function getValue(string $key)
    {
        if (!isset($this->values[$key])) {
            return null;
        }

        return (string) $this->values[$key];
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function getPreviousValue(): ?string
    {
        return $this->previousValue;
    }

    public function getPreviousCharacter(): ?string
    {
        return $this->previousCharacter;
    }

    public function getPreviousFormatCharacter(): ?string
    {
        return $this->previousFormatCharacter;
    }
}
