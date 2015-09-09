<?php

namespace Jsor\StringFormatter;

interface FormatterInterface
{
    /**
     * @param array $values
     * @return string
     */
    public function format(array $values);
}
