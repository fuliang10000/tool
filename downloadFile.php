<?php
// 文件下载，支持断点续传
$params = $_GET;
try {

    if (empty($params['file_url'])) throw new \Exception('请传入文件地址！');
    $fileUrl = $params['file_url'];
    $fileName = $params['file_name'] ?? null;
    $filePath = $params['file_path'] ?? null;
    $chunkSize = $params['chunk_size'] ?? null;

    downloadFileHttp($fileUrl, $fileName, $filePath, $chunkSize);

} catch (\Exception $ex) {

    echo $ex->getMessage();
}

/**
 * 远程文件下载，支持断点续传
 * @param string $url 远程文件地址（必要参数）
 * @param string|null $fileName 下载完成后的文件名 （可选参数：默认源文件名）
 * @param string|null $filePath 下载完成后的文件路径 （可选参数：默认当前文件目录下）
 * @param int|null $chunkSize 每次写入的大小,单位byte （可选参数：默认1M）
 * @example
 * $url = "http://ceshi-admin.sharcom.cn//uploads/member_files/order/2017/12/08/2cf4d497f9bd061b49d5c57857e11333.pdf";
 * $fileName = "1514518158.pdf";
 * $filePath = "uploads/member_files/order/20171229/";
 * $chunkSize = "1024 * 2048";
 * @return json
 * 	{
 *      "success":true,
 *      "data":{
 *          "file_size":23149,
 *          "file_name":"77aba2f582515fadfb346241700bc843_thumb.jpg",
 *          "file_path":"C:/Users/fulia/Desktop"
 *      },
 *      "message":"下载成功！"
 * }
 * @author fuliang
 * @date 2017-12-29
 */
function downloadFileHttp (string $url, ?string $fileName = null, ?string $filePath = null, ?int $chunkSize = null)
{
    header('Content-Type: text/html; charset=utf-8');
    $response = ['success' => false , 'data' => [], 'message' => '下载失败！'];
    // 默认下载到当前目录下,如果指定了目录则下载到指定目录下,指定目录不存在则创建
    if (!empty($filePath)) {
        if(!is_dir($filePath)) {
            mkdir($filePath, 0755, true);
        }
    } else {
        $filePath = './';
    }
    try {
        // 验证参数
        if (empty($url)) {
            throw new \Exception('请传入文件地址！');
        }
        if (is_file($filePath.$fileName)) {
            throw new \Exception('当前文件已存在！');
        }
        if (!empty($chunkSize)) {
            if (!is_numeric($chunkSize)) {
                throw new \Exception('每次写入的大小必须是数字！');
            }
        } else {
            $chunkSize = 1024 * 1024;
        }
        // 文件名默认为源文件名
        $fileName = empty($fileName) ? basename($url) : strval($fileName);
        $header = get_headers($url, 1);
        $size = $header['Content-Length']; // 文件大小,单位byte
        $size2 = $size - 1;
        $range = 0;

        // http_range表示请求一个实体/文件的一个部分,用这个实现多线程下载和断点续传！
        if (isset($_SERVER['HTTP_RANGE'])) {
            header('HTTP /1.1 206 Partial Content');
            $range = str_replace('=','-',$_SERVER['HTTP_RANGE']);
            $range = explode('-',$range);
            $range = trim($range[1]);
            header('Content-Length:'.$size);
            header('Content-Range: bytes '.$range.'-'.$size2.'/'.$size);
        } else {
            header('Content-Length:'.$size);
            header('Content-Range: bytes 0-'.$size2.'/'.$size);
        }
        header('Content-Description: File Transfer');
        header('Pragma: public');

        $fp = @fopen($url, 'rb'); // 指定的名字资源绑定到一个流上
        if ($fp == false) {
            throw new \Exception('文件不存在或打开失败！');
        }
        @fseek($fp,$range);
        ob_clean();
        ob_end_flush();
        set_time_limit(0);
        // 写入方式打开文件，将文件指针指向文件末尾。如果文件不存在则尝试创建之
        $fp2 = fopen($filePath.$fileName, 'a');
        while (!feof($fp)) {
            $buffer = fread($fp, $chunkSize);
            fwrite($fp2, $buffer); // 写入数据
            @ob_flush();
            flush();
        }
        fclose($fp);
        fclose($fp2);
        // 下载完成后的文件大小,单位byte
        $fileSize = filesize($filePath.$fileName);
        $response = [
            'success' => true,
            'data' => [
                'file_size' => $fileSize,
                'file_name' => $fileName,
                'file_path' => $filePath,
            ],
            'message' => '下载成功！'
        ];
    } catch (\Exception $e) {
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}
