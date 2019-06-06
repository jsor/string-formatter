<?php

declare(strict_types=1);

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

interface FieldDescriptorInterface
{
    public function getCharacter(): string;

    public function getValue(FormatContext $context): string;

    public function getReplacement(string $value, FormatContext $context): string;
}
