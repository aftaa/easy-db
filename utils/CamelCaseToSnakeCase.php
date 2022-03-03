<?php

namespace common\db\utils;

class CamelCaseToSnakeCase
{
    /**
     * @param string $string
     * @param string $us
     * @return string
     */
    public function transform(string $string, string $us = '_'): string
    {
        $patterns = [
            '/([a-z]+)([0-9]+)/i',
            '/([a-z]+)([A-Z]+)/',
            '/([0-9]+)([a-z]+)/i'
        ];
        $string = \preg_replace($patterns, '$1' . $us . '$2', $string);

        // Lowercase
        $string = \strtolower($string);

        return $string;
    }
}