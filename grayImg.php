<?php
header('Content-Type:application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $response = ['error' => 0, 'message' => '上传成功'];
    $file = $_FILES['file'];
    try {

        /*验证是否上传成功*/
        if ($file['error'] > 0) {
            throw new \Exception('上传失败');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \Exception('上传文件错误');
        }

        if ($file['size'] > (1024 * 1024 * 5)) {
            throw new \Exception("上传大小不能大于5MB");
        }

        if (!in_array($file['type'], ["image/jpeg", "image/png"])) {
            throw new \Exception('只允许上传jpg、jpeg、png格式的图片');
        }

        $imgType = substr($file['type'], strripos($file['type'], "/") + 1);
        $grayPath = grayImg($file['tmp_name'], $imgType);
        if (!$grayPath) throw new \Exception('生成图片失败');

        $response['file_path'] = $grayPath;

    } catch (\Exception $ex) {
        $response = ['error' => 100, 'message' => $ex->getMessage()];
    }

    echo json_encode($response);
}
function grayImg($resImg, $imgType = 'jpeg')
{
    switch ($imgType) {
        case 'png':
            $image = imagecreatefrompng($resImg);
            $func = 'imagejpeg';
            break;
        default:
            $image = imagecreatefromjpeg($resImg);
            $func = 'imagepng';
            break;

    }
    $img_width = ImageSX($image);
    $img_height = ImageSY($image);
    for ($y = 0; $y < $img_height; $y++) {
        for ($x = 0; $x < $img_width; $x++) {
            $gray = (ImageColorAt($image, $x, $y) >> 8) & 0xFF;
            imagesetpixel($image, $x, $y, ImageColorAllocate($image, $gray, $gray, $gray));
        }
    }
    $fileName = 'gray_' . time() . rand(1000, 9999) . '.' . $imgType;
    $grayPath = './uploads/' . date('Ymd') . '/';
    if (!is_dir($grayPath)) {
        mkdir($grayPath, 0777, true);
    }
    $grayPath .= $fileName;
    $func($image, $grayPath);
    imagedestroy($image);

    return $grayPath;
}
