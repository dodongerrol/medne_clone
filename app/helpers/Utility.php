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
    
    public static function convert_date_format($old_date = '')
	{
		$old_date = trim($old_date);

		if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $old_date)) // MySQL-compatible YYYY-MM-DD format
		{
			$new_date = $old_date;
		}
		elseif (preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/', $old_date)) // DD-MM-YYYY format
		{
			$new_date = substr($old_date, 6, 4) . '-' . substr($old_date, 3, 2) . '-' . substr($old_date, 0, 2);
        }
        elseif (preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/', $old_date)) // DD-MM-YYYY format
		{
			$new_date = substr($old_date, 6, 4) . '-' . substr($old_date, 3, 2) . '-' . substr($old_date, 0, 2);
		}
		elseif (preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{2}$/', $old_date)) // DD-MM-YY format
		{
			$new_date = substr($old_date, 6, 4) . '-' . substr($old_date, 3, 2) . '-20' . substr($old_date, 0, 2);
		}
		else // Any other format. Set it as an empty date.
		{
			$new_date = '0000-00-00';
		}
		return $new_date;
	}
}