<?php
header('Content-Type: image/jpeg');
function grayjpg($resimg)
{
    $image = imagecreatefromjpeg($resimg);
    $img_width = ImageSX($image);
    $img_height = ImageSY($image);
    for ($y = 0; $y < $img_height; $y++) {
        for ($x = 0; $x < $img_width; $x++) {
            $gray = (ImageColorAt($image, $x, $y) >> 8) & 0xFF;
            imagesetpixel($image, $x, $y, ImageColorAllocate($image, $gray, $gray, $gray));
        }
    }
    imagejpeg($image);
    imagedestroy($image);
}
grayjpg("C:/Users/fulia/Desktop/包图网_19455738男士戴口罩抗击新冠病毒插画.png");
//$im = imagecreatefromjpeg("F:/稻城亚丁/DSC07804.JPG");
//$rgb = ImageColorAt($im, 100, 100);//100,100
//$r = ($rgb >> 16) & 0xFF;
//$g = ($rgb >> 8) & 0xFF;
//$b = $rgb & 0xFF;
//var_dump($r, $g, $b);
//imagejpeg($im);
//imagedestroy($im);
