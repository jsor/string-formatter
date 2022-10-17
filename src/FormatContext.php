<?php

declare(strict_types=1);

namespace Jsor\StringFormatter;

use function array_key_exists;

final class FormatContext
{
    /**
     * @var array<string, mixed>
     */
    private array $values;

    private bool $strict;

    private ?string $previousValue;

    private ?string $previousCharacter;

    private ?string $previousFormatCharacter;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        array $values,
        bool $strict,
        ?string $previousValue,
        ?string $previousCharacter,
        ?string $previousFormatCharacter,
    ) {
        $this->values = $values;
        $this->strict = $strict;
        $this->previousValue = $previousValue;
        $this->previousCharacter = $previousCharacter;
        $this->previousFormatCharacter = $previousFormatCharacter;
    }

    public function hasValue(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function getValue(string $key): mixed
    {
        return $this->values[$key] ?? null;
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
