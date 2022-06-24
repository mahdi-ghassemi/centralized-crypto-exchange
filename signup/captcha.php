<?php

session_start();

function getRandomWord($len = 5) {
    $word = array_merge(range('0', '9'), range('0', '9'));
    shuffle($word);
    return substr(implode($word), 0, $len);
}

$ranStr = getRandomWord();
$_SESSION["vercode"] = $ranStr;


$height = 35; //CAPTCHA image height
$width = 50; //CAPTCHA image width
$font_size = 16; 

$image_p = imagecreate($width, $height);
$graybg = imagecolorallocate($image_p, 245, 245, 245);
$textcolor = imagecolorallocate($image_p, 34, 34, 34);
$realpath = realpath('mono.ttf');

imagefttext($image_p, $font_size, 0, 1, 26, $textcolor, $realpath, $ranStr);
//imagestring($image_p, $font_size, 5, 3, $ranStr, $white);
imagepng($image_p);
?>