<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once './uploadFile.php';
    $file = $_FILES['file'];
    $response = ['error' => 0, 'message' => '操作成功'];
    try {

        if ($file['error'] > 0) {
            throw new \Exception('上传失败');
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \Exception('无效得上传图片');
        }
        if ($file['size'] > (1024 * 1024 * 5)) {
            throw new \Exception('上传图片不能大于5MB');
        }
        if (!in_array($file['type'], ["image/jpeg", "image/png"])) {
            throw new \Exception('只允许上传jpg、jpeg、png格式的图片');
        }

        // 保存图片
        $result = uploadFile($file);
        if ($result['success'] == false) {
            throw new \Exception($result['message']);
        }

        $grayPath = grayImg($result['file_info']['file_path'], $result['file_info']['file_type']);
        if (!$grayPath) throw new \Exception('生成图片失败');

        $response['file_path'] = 'http://yimuyuan.xin/tool/' . $grayPath;

    } catch (\Exception $ex) {
        $response = ['error' => 100, 'message' => $ex->getMessage()];
    }

    echo json_encode($response);
}
function grayImg($resImg, $imgType = 'jpeg')
{
    switch ($imgType) {
        case 'png':
            header('Content-Type: image/png');
            $image = imagecreatefrompng($resImg);
            break;
        default:
            header('Content-Type: image/jpeg');
            $image = imagecreatefromjpeg($resImg);
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
    $grayPath = substr($resImg, 0, strripos($resImg, "/") + 1) . 'gray_' . basename($resImg); // 文件路径
    imagejpeg($image, $grayPath);
    imagedestroy($image);

    return $grayPath;
}
