<?php
/**
 * 使用系统全局变量：$_FILES 实现上传文件到指定的目录
 * @param array $cert 系统全局变量：$_FILES['myFile']（必要参数）
 * @param int $maxSize 最大上传文件大小，单位：MB，默认：5MB
 * @param string $savePath 指定文件保存的路径，默认：'./uploads/年月日/'
 * @example
 * $cert = $_FILES['file'];
 * $savePath = './uploads/';
 * @return array
 * [
 *      "success": true,
 *       "message": "UPLOAD SUCCESS!",
 *       "file_info": [
 *           "file_path": "./updates/0_0_1228_6193#326_1.jpg",
 *           "file_type": "image/jpeg",
 *           "file_size": "0.37MB"
 *       ]
 * ]
 * @author fuliang
 * @date 2018-04-03
 */
function uploadFile (array $cert, int $maxSize = 5, string $savePath = './uploads/'): array
{

    try {

        /*验证参数*/
        if (!is_array($cert)) {
            throw new \Exception('PARAMS ERROR!');
        }

        if (!is_uploaded_file($cert['tmp_name'])) {
            throw new \Exception('Invalid upload file');
        }

        /*验证是否上传成功*/
        if ($cert['error'] > 0) {
            throw new \Exception('UPLOAD FAIL：' . $cert['error']);
        }

        if ($cert['size'] > (1024 * 1024 * $maxSize)) {
            throw new \Exception("上传大小不能大于{$maxSize}MB");
        }

        $url      = $cert['tmp_name'];
        $name     = $cert['name'];                           //文件名
        $fileSize = round($cert['size']/1024/1024, 2) .'MB'; //文件大小
        $fileType = $cert['type'];                           // 文件类型

        /*设置上传路径，默认保存到uploads文件夹下的当前日期目录下*/
        if (empty($savePath)) {
            $savePath = './uploads/' . date('Ymd');
        }
        if (substr($savePath, -1) != '/') {
            $savePath .= '/';
        }
        if (!is_dir($savePath)) {
            if (!mkdir($savePath, 0755, true)) {
                throw new \Exception('CREATE DIR FAIL!');
            }
        }
        /*以原文件名来命名上传的文件*/
        $filePath = $savePath . $name;
        if (is_file($filePath)) {
            throw new \Exception('FILE EXIST!');
        }

        /*保存文件到指定目录*/
        if (!move_uploaded_file($url, iconv("UTF-8", "GBK", $filePath))) {
            throw new \Exception('FAILED TO UPLOAD FILES TO THE SPECIFIED DIR!');
        }
        $response = [
            'success' => true ,
            'message' => 'UPLOAD SUCCESS!',
            'file_info' => [
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_size' => $fileSize,
            ]
        ];
    } catch (\Exception $e) {
        $response = ['success' => false , 'message' => $e->getMessage()];
    }
    @ob_clean();

    return $response;
}
