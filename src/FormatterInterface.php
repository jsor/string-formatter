<?php

declare(strict_types=1);

namespace Jsor\StringFormatter;

interface FormatterInterface
{
    /**
     * @param array<string, string|int> $values
     */
    public function format(array $values): string;
}
