<?php

namespace Jsor\StringFormatter;

interface FormatterInterface
{
    /**
     * @param string $format
     * @param array  $values
     *
     * @return string
     */
    public function format($format, array $values = array());
}
