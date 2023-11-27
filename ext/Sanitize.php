<?php

namespace ext;

class Sanitize {

    /**
     * Sanitize a string for use in a route folder and protect against SQL injections.
     *
     * @param string|null $input The input string to be sanitized.
     * @return string The sanitized string.
     */
    public static function sanitizeString(?string $input): string
    {
        // Use a regular expression to check if the string contains only alphanumeric characters and underscores.
        return (isset($input) && preg_match('/^[a-zA-Z0-9_]+$/', $input) ? $input : 'injection');
    }

    public static function sanitizeDate(?string $input): string
    {
        // If $input is null or empty, return an empty string
        if (!$input) {
            return '';
        }

        // Try parsing the input as a date
        $date = DateTime::createFromFormat('Y-m-d', $input);

        // Check if the input is a valid date
        if ($date && $date->format('Y-m-d') === $input) {
            // Valid date, return the sanitized date
            return $input;
        }

        return '0000-00-00';
    }
}