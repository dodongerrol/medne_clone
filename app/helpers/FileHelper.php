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

	public static function formatImageAutoQuality($image)
	{
		$split = explode("/v", $image);
		$new_image = $split[0].'/q_auto/v'.$split[1];
		return $new_image;
	}
}
?>