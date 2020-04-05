<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once './uploadFile.php';
    $file = $_FILES['file'];

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

        $imgType = 'jpeg';
        if ($file['type'] == 'image/png') $imgType = 'png';
        grayImg($result['file_info']['file_path'], $imgType);

    } catch (\Exception $ex) {

        echo $ex->getMessage();
    }

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
    imagejpeg($image);
    imagedestroy($image);
}
?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="file"><br/>
    <input type="submit" value="上传"/>
</form>
