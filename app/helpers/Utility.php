<?php
use Illuminate\Support\Facades\Input;

class Utility {
    public static function stripXSS()
    {
        $sanitized = self::cleanArray(Input::get());
        return Input::merge($sanitized);
    }

    public static function cleanArray($array)
    {
        $result = array();
        foreach ($array as $key => $value) {
            $key = strip_tags($key);
            if (is_array($value)) {
                $result[$key] = self::cleanArray($value);
            } else {
                $result[$key] = trim(strip_tags($value)); // Remove trim() if you want to.
            }
       }
       return $result;
    }

    public static function array_strip_tags($array)
    {
        $result = array();
        foreach ($array as $key => $value) {
            $key = strip_tags($key);
            if (is_array($value)) {
                $result[$key] = array_strip_tags($value);
            }
            else {
                $result[$key] = strip_tags($value);
            }
        }
        return $result;
    }
}