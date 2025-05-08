<?php
// Set the content-type
header('Content-Type: image/png');

// Create the image
$im = imagecreatetruecolor(400, 150);

// Create some colors
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 0, 0, 0);
//imagefilledrectangle($im, 0, 0, 399, 150, $grey);

// The text to draw
$text = 'VERIFIED - 52';
// Replace path by your own font path
$font = 'Arial.ttf';

// Add some shadow to the text
//imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);

// Add the text
imagettftext($im, 30, 15, 20, 150, $grey, $font, $text);

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);
?>