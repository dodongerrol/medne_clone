<?php
class DecimalHelper
{
	public static function formatDecimal($value)
	{
		return number_format(floor($value * 100) / 100, 2);
	}

	public static function formatWithNoCommas($value)
	{
		return number_format(floor($value * 100) / 100, 2, '.', '');
	}
}
?>