<?php
require_once __DIR__ . '/vendor/autoload.php';

use Intervention\Image\ImageManagerStatic as Image;

Image::configure(['driver' => 'gd']);

$image = Image::canvas(300, 200, '#00ccff');
$image->save(__DIR__ . '/test.jpg');

echo "It worked!";
