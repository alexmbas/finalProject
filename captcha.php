<?php
session_start();
$code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5);
$_SESSION['captcha'] = $code;

$img = imagecreatetruecolor(120, 40);
$bg = imagecolorallocate($img, 255, 255, 255);
$textColor = imagecolorallocate($img, 0, 0, 0);

imagefilledrectangle($img, 0, 0, 120, 40, $bg);
imagestring($img, 5, 30, 12, $code, $textColor);

header('Content-Type: image/png');
imagepng($img);
imagedestroy($img);
