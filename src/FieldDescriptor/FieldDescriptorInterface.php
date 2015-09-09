<?php

namespace Jsor\StringFormatter\FieldDescriptor;

use Jsor\StringFormatter\FormatContext;

interface FieldDescriptorInterface
{
    public function getCharacter();
    public function getValue(FormatContext $context);
    public function getReplacement($value, FormatContext $context);
}
