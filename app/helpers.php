<?php

/**
 *
 * A set of application's helper functions or utilities
 * that might be use in other parts.
 *
 */


if (!function_exists('medi_date_parser')) {
    /**
     * Replace backslash to a dash seperator
     * Sometimes PHP date doesn't properly read dates with format dd/mm/Y
     * @return Y-m-d format date
     */
    function medi_date_parser($value)
    {
        $date = str_replace('/','-', $value);

        return strftime("%Y-%m-%d", strtotime($date));
    }
}

if (!function_exists('is_date_between')) {
    function is_date_between($base, $start, $end)
    {
        $base = strtotime(medi_date_parser($base));
        $start = strtotime(medi_date_parser($start));
        $end = strtotime(medi_date_parser($end));

        return $base >= $start && $base <= $end;
    }
}

if (!function_exists('medi_date_format')) {
    function medi_date_format($date, $format = 'd/m/Y')
    {
        $raw = strtotime(medi_date_parser($date));

        return date($format, $raw);
    }
}