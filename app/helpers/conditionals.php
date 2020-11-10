<?php

if (!function_exists('isEqual')) {
    function isEqual($value1, $value2, $strict = false): bool
    {
        if ($strict) {
            return $value1 === $value2;
        }

        return $value1 == $value2;
    }
}

if (!function_exists('isNotEqual')) {
    function isNotEqual($value1, $value2, $strict = false): bool
    {
        if ($strict) {
            return $value1 !== $value2;
        }

        return $value1 != $value2;
    }
}