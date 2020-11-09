<?php
class DecimalHelper
{
	public static function formatDecimal($value)
	{
		return number_format(round($value * 100) / 100, 2);
	}
}
?>