<?php
session_start();

//Generate a five letter code
$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$code = '';
for ($i = 0; $i < 5; $i++) { $code .= $chars[random_int(0, strlen($chars)-1)]; }

//Store uppercase in session
$_SESSION['captcha'] = strtoupper($code);

//Create image
$w = 140; $h = 48;
$img = imagecreatetruecolor($w, $h);
$bg  = imagecolorallocate($img, 20, 20, 20);
$fg  = imagecolorallocate($img, 250, 250, 250);
$no1 = imagecolorallocate($img, 60, 60, 60);
$no2 = imagecolorallocate($img, 100, 100, 100);

imagefilledrectangle($img, 0, 0, $w, $h, $bg);

//noise
for ($i=0;$i<60;$i++){
  $x = random_int(0, $w); $y = random_int(0, $h);
  imagesetpixel($img, $x, $y, ($i%2 ? $no1 : $no2));
}

//text
$font = 5; 
$tw = imagefontwidth($font) * strlen($code);
$th = imagefontheight($font);
$x = (int)(($w - $tw)/2);
$y = (int)(($h - $th)/2);
imagestring($img, $font, $x, $y, $code, $fg);

//output
header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
imagepng($img);
imagedestroy($img);
