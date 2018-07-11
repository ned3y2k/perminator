<?php
/*
 * PHP function to resize an image maintaining aspect ratio
* http://salman-w.blogspot.com/2008/10/resize-images-using-phpgd-library.html
*
* Creates a resized (e.g. thumbnail, small, medium, large)
* version of an image file and saves it as another file
*/

/** 이미지 리사이징 고정 비율 없음 */
define('IMAGE_ASPECT_RATIO_NONE', -1);
/** 이미지 리사이징 너비에 따른 고정 비율 */
define('IMAGE_ASPECT_RATIO_WIDTH', 0);
/** 이미지 리사이징 높이에 따른 고정 비율 */
define('IMAGE_ASPECT_RATIO_HEIGHT', 1);

class ImageBuffer {
	public $buff;
	public $type;
}

function image_file_type_from_binary($binary) {
	if (! preg_match (
			'/\A(?:(\xff\xd8\xff)|(GIF8[79]a)|(\x89PNG\x0d\x0a)|(BM)|(\x49\x49(?:\x2a\x00|\x00\x4a))|(FORM.{4}ILBM))/', $binary, $hits
	)) {return 'application/octet-stream';}
	static $type = array (
			1 => 'image/jpeg',
			2 => 'image/gif',
			3 => 'image/png',
			4 => 'image/x-windows-bmp',
			5 => 'image/tiff',
			6 => 'image/x-ilbm' );
	return $type [count ( $hits ) - 1];
}

/**
 * 이미지 사이즈를 조정하여 돌려준다.
 *
 * @param string      $buff
 * @param bool|string $constrain_aspect_ratio
 * @param int|number  $thumbnail_width
 * @param int|number  $thumbnail_height
 *
 * @param int         $quality
 * @return ImageBuffer|bool
 */
function image_resize_from_buff($buff, $constrain_aspect_ratio = true, $thumbnail_width = 150, $thumbnail_height = 150, $quality = 90) {
	$item = new ImageBuffer();
	$item->type = image_file_type_from_binary($buff);

	$source_gd_image = imagecreatefromstring($buff);

	if ($source_gd_image === false) {
		return false;
	}

	$source_image_width = imagesx($source_gd_image);
	$source_image_height = imagesy($source_gd_image);

	list($thumbnail_image_width, $thumbnail_image_height) =
		image_calculate_size($constrain_aspect_ratio, $thumbnail_width, $thumbnail_height, $source_image_width, $source_image_height);

	$thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);

	imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);

	ob_start();

	switch ($item->type) {
		case 'image/jpeg':
			imagejpeg($thumbnail_gd_image, null, $quality);
			break;
		case 'image/gif':
			imagegif($thumbnail_gd_image);
			break;
		case 'image/png':
			$quality = floor($quality * 0.1);
			$quality = 10 - $quality;
			imagepng($thumbnail_gd_image, null, $quality, PNG_ALL_FILTERS);
			break;
		case 'image/x-windows-bmp':
			imagewbmp($thumbnail_gd_image);
			break;
		default:
			imagejpeg($thumbnail_gd_image, null, $quality);
			$item->type = 'image/jpeg';
	}

	$item->buff = ob_get_contents(); // read from buffer
	ob_end_clean(); // delete buffer

	imagedestroy($source_gd_image);
	imagedestroy($thumbnail_gd_image);

	return $item;
}


/**
 * 이미지 사이즈를 조정하여 다시 저장한다.
 *
 * @param string $source_image_path
 * @param string $thumbnail_image_path
 * @param bool   $constrain_aspect_ratio
 * @param int    $thumbnail_width
 * @param int    $thumbnail_height
 *
 * @param  int   $quality
 * @return bool
 */
function image_resize($source_image_path, $thumbnail_image_path, $constrain_aspect_ratio = true, $thumbnail_width = 150, $thumbnail_height = 150, $quality = 90)
{
	$type = null;

	if(filesize($source_image_path) == 0) throw new InvalidArgumentException("file size is zero");

	list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
	switch ($source_image_type) {
		case IMAGETYPE_GIF:
			$type = "gif";
			$source_gd_image = imagecreatefromgif($source_image_path);
			break;
		case IMAGETYPE_JPEG:
			$type = "jpg";
			$source_gd_image = imagecreatefromjpeg($source_image_path);
			break;
		case IMAGETYPE_PNG:
			$type = "png";
			$source_gd_image = imagecreatefrompng($source_image_path);
			break;
		default:
			throw new InvalidArgumentException(sprintf("not suported image type: %s", $source_image_path));
	}

	if ($source_gd_image === false) {
		return false;
	}

	list($thumbnail_image_width, $thumbnail_image_height) =
		image_calculate_size($constrain_aspect_ratio, $thumbnail_width, $thumbnail_height, $source_image_width, $source_image_height);

	$thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
	imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);

	if($type == 'gif') {
		imagegif($thumbnail_gd_image, $thumbnail_image_path);
	} elseif($type == 'jpg') {
		imagejpeg($thumbnail_gd_image, $thumbnail_image_path, $quality);
	} elseif($type == 'png') {
		$quality = floor($quality * 0.1);
		$quality = 10 - $quality;

		imagepng($thumbnail_gd_image, $thumbnail_image_path, $quality);
	}

	imagedestroy($source_gd_image);
	imagedestroy($thumbnail_gd_image);
	return true;
}

/**
 * @param $constrain_aspect_ratio
 * @param $thumbnail_image_width
 * @param $thumbnail_image_height
 * @param $source_image_width
 * @param $source_image_height
 * @return array
 */
function image_calculate_size($constrain_aspect_ratio, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height) {
	if ($constrain_aspect_ratio == IMAGE_ASPECT_RATIO_WIDTH) {
		$aspect_ratio = $thumbnail_image_width / $source_image_width;
		$thumbnail_image_height = (int)($source_image_height * $aspect_ratio);
	} elseif ($constrain_aspect_ratio == IMAGE_ASPECT_RATIO_HEIGHT) {
		$aspect_ratio = $thumbnail_image_height / $source_image_height;
		$thumbnail_image_width = (int)($source_image_width * $aspect_ratio);
	} elseif($constrain_aspect_ratio != IMAGE_ASPECT_RATIO_NONE
	         && $constrain_aspect_ratio == IMAGE_ASPECT_RATIO_WIDTH
	         && $constrain_aspect_ratio == IMAGE_ASPECT_RATIO_HEIGHT) {
		throw new InvalidArgumentException("invalid resize mode");
	}

	return array($thumbnail_image_width, $thumbnail_image_height);
}