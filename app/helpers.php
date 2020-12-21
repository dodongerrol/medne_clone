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

if (!function_exists('medi_date_foarmat')) {
    function medi_date_format($date, $format = 'd/m/Y')
    {
        $raw = strtotime(medi_date_parser($date));

        return date($format, $raw);
    }
}

if (!function_exists('validate_phone')) {
    function validate_phone($phone, $country)
    {
        $length = strlen($phone);

        $result = [
            'error' => false,
            'message' => null
        ];

        switch ($country) {
            case 65:
                $stringify = (string) $phone;
                if ($length < 8 || $length > 8) {
                    $result['error'] = true;
                    $result['message'] = 'Mobile Number for your country code should be 8 digits.';
                } else {
                    if ($stringify[0] !== '8' && $stringify[0] !== '9') {
                        $result['error'] = true;
                        $result['message'] = 'Invalid mobile format.';
                    }
                }
                break;
            case 60:
                if ($length < 9 || $length > 10) {
                    $result['error'] = true;
                    $result['message'] = 'Invalid mobile format. Please enter mobile in the format of 9-10 digit number without the prefix “0”.';
                }
                break;
            case 63:
                if ($length < 9 || $length > 9) {
                    $result['error'] = true;
                    $result['message'] = 'Mobile Number for your country code should be 9 digits.';
                }
                break;
            default:
                $result['error'] = false;
                $result['message'] = null;
                break;
        }

        return $result;
    }
}

if (!function_exists('sum')) {
    function sum($values)
    {
        return array_sum($values);
    }
}

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

if (!function_exists('collect')) {
    function collect($data): Collection
    {
        return Collection::make($data);
    }
}

if (!function_exists('isNullOrEmptyString')) {
    function isNullOrEmptyString($str): bool
    {
        return (!isset($str) || trim($str) === '');
    }
}