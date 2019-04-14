<?php 
class FileHelper
{
	public static function checkFile($file_name)
	{
		$file = file(public_path().'/receipts/'.$file_name);
		$endfile = trim($file[count($file) - 1]);
		$n = "%%EOF";

		if ($endfile === $n) {
		    return true;
		} else {
		    return false;
		}
	}
}
?>