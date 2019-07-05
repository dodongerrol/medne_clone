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
		$splits = explode("/v", $image);
		// $new_image = $split[0].'/q_auto/v'.$split[1];
		$new_image = "";
		$count = count($splits);

		foreach ($splits as $key => $split) {
			if($key == 1) {
				$new_image .= '/q_auto/v';
			} else if($key == $count - 1) {
				$new_image .= '/v';
			} 
			$new_image .= $split;
		}

		return $new_image;
	}

	public static function formatImageAutoQualityCustomer($image, $quality)
	{
		$splits = explode("/v", $image);
		// $new_image = $split[0].'/q_auto/v'.$split[1];
		$new_image = "";
		$count = count($splits);

		foreach ($splits as $key => $split) {
			if($key == 1) {
				$new_image .= '/q_'.$quality.'/v';
			} else if($key == $count - 1) {
				$new_image .= '/v';
			} 
			$new_image .= $split;
		}

		return $new_image;
	}

	public static function compress_image($source_url, $destination_url, $quality) {
       $info = getimagesize($source_url);

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source_url);
        } elseif ($info['mime'] == 'image/gif') {
        	$image = imagecreatefromgif($source_url);
        } elseif ($info['mime'] == 'image/png') {
        	$image = imagecreatefrompng($source_url);
        }

        imagejpeg($image, $destination_url, $quality);
    	return $destination_url;
    }

}
?>