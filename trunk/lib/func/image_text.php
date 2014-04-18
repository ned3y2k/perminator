<?php
function imagettftextSp($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0) {
	if ($spacing == 0) {
		imagettftext ( $image, $size, $angle, $x, $y, $color, $font, $text );
	} else {
		$temp_x = $x;
		for($i = 0; $i < strlen ( $text ); $i ++) {
			$bbox = imagettftext ( $image, $size, $angle, $temp_x, $y, $color, $font, $text [$i] );
			$temp_x += $spacing + ($bbox [2] - $bbox [0]);
		}
	}
}

// Basic font settings
$minWidth = array_key_exists("minWidth", $_GET) ? (int)$_GET['minWidth'] : 500;

$paddingTop = array_key_exists("paddingTop", $_GET) ? (int)$_GET['paddingTop'] : 10;
$paddingBottom = array_key_exists("paddingBottom", $_GET) ? (int)$_GET['paddingBottom'] : 10;
$paddingLeft = array_key_exists("paddingLeft", $_GET) ? (int)$_GET['paddingLeft'] : 10;
$paddingRight = array_key_exists("paddingRight", $_GET) ? (int)$_GET['paddingRight'] : 10;

$fontSize = array_key_exists("fontSize", $_GET) ? (int)$_GET['fontSize'] : 100;
$fontColor = array_key_exists("fontColor", $_GET) ? $_GET['fontColor'] : 0xf6e336;
$fontFile = array_key_exists("fontFile", $_GET) ? $_GET['fontFile'] : 'HMKLS.ttf';
$text = array_key_exists("value", $_GET) ? $_GET['value'] : "blank";

// Starting X, Y position
$x=$paddingLeft; $y = $fontSize + $paddingTop;

$boxSize = imagettfbbox($fontSize, 0, $fontFile, $text);
$width = abs($boxSize[0]) + abs($boxSize[2]) + $paddingLeft + $paddingRight;
$width = $width < $minWidth ? $minWidth : $width;
$height = abs($boxSize[5]) + abs($boxSize[1] + $paddingTop + $paddingBottom);

$image = imagecreatetruecolor($width, $height);
imagesavealpha($image, true);
imageantialias($image, true);

$bgColor = imagecolorallocatealpha($image, 0, 0, 0, 127);
imagefill($image, 0, 0, $bgColor);


imagettftextSp($image, $fontSize, 0, $x, $y, $fontColor, $fontFile, $text, 0);

header('Content-Type: image/png; Content-Disposition: attachment; filename=image.png');
imagepng($image);
imagedestroy($image);
?>