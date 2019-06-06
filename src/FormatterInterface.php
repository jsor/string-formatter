<?php

declare(strict_types=1);

namespace Jsor\StringFormatter;

interface FormatterInterface
{
    /**
     * @param array<string, mixed> $values
     */
    public function format(array $values): string;
}
