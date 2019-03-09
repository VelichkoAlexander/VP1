<?php
require './vendor/autoload.php';

// open an image file
use Intervention\Image\ImageManager;

$manager = new ImageManager();
$img = $manager->make('./img/content/burger.png');

$img->rotate(45);

$img->text(
    'WATERMARK TEXT',
    $img->width() / 2,
    $img->height() / 2,
    function ($font) {
        $font->color(array(255, 0, 0, 0.5));
        $font->align('center');
        $font->valign('center');
    });

$img->resize(200, null, function ($img) {
    $img->aspectRatio();
});


$img->save('./img/result/result.png');


echo 'success';