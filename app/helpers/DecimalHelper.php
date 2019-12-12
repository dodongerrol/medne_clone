<?php
class DecimalHelper
{
	public static function formatDecimal($value)
	{
		return floor($value * 100) / 100;
	}
}
?>