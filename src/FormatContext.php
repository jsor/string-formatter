<?php

namespace Jsor\StringFormatter;

final class FormatContext
{
    private $values;
    private $strict;
    private $previousValue;
    private $previousCharacter;
    private $previousFormatCharacter;

    public function __construct(
        array $values,
        $strict,
        $previousValue,
        $previousCharacter,
        $previousFormatCharacter
    ) {
        $this->values                  = $values;
        $this->strict                  = (bool) $strict;
        $this->previousValue           = (string) $previousValue;
        $this->previousCharacter       = (string) $previousCharacter;
        $this->previousFormatCharacter = (string) $previousFormatCharacter;
    }

    public function hasValue($key)
    {
        return array_key_exists($key, $this->values);
    }

    public function getValue($key)
    {
        if (!isset($this->values[$key])) {
            return null;
        }

        return $this->values[$key];
    }

    public function isStrict()
    {
        return $this->strict;
    }

    public function getPreviousValue()
    {
        return $this->previousValue;
    }

    public function getPreviousCharacter()
    {
        return $this->previousCharacter;
    }

    public function getPreviousFormatCharacter()
    {
        return $this->previousFormatCharacter;
    }
}
