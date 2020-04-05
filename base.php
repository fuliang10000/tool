<?php
/**
 *完成进制转换
 */
header('Content-Type: text/html; charset=utf-8');
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $num = $_POST['num'];
    $change = $_POST['change'];
    //判断数据的合法性
    if(substr($change,0,3) == 'dec'){
        $pattern = '/^[\d]+$/';
        if(!preg_match($pattern,$num)){
            echo '数据不合法！';exit;
        }
    }elseif(substr($change,0,3) == 'bin'){
        $pattern = '/^[01]+$/';
        if(!preg_match($pattern,$num)){
            echo '数据不合法！';exit;
        }
    }elseif(substr($change,0,3) == 'oct'){
        $pattern = '/^[01234567]+$/';
        if(!preg_match($pattern,$num)){
            echo '数据不合法！';exit;
        }
    }elseif(substr($change,0,3) == 'hex'){
        $pattern = '/^[\abcdef]+$/i';
        if(!preg_match($pattern,$num)){
            echo '数据不合法！';exit;
        }
    }
    if($change == "binoct"){
		$result = base_convert($num,2,8);
    }elseif($change == "binhex"){
        $result = base_convert($num,2,16);
    }elseif($change == "octbin"){
        $result = base_convert($num,8,2);
    }elseif($change == "octhex"){
        $result = base_convert($num,8,6);
    }elseif($change == "hexbin"){
        $result = base_convert($num,16,2);
    }elseif($change == "hexoct"){
        $result = base_convert($num,16,8);
    }elseif($change == null){
        $result = "请选择转换规则";
    }else{
        $result = $change($num);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>小工具</title>
    <link rel="stylesheet" type="text/css" href="./css/index.css">
</head>
<body>
<form method="post">
    请输入要转换的数：<input type="text" name="num"/>
    <select name="change">
        <option value="">-请选择转换规则-</option>
        <option value="decbin">10进制转2进制</option>
        <option value="decoct">10进制转8进制</option>
        <option value="dechex">10进制转16进制</option>
        <option value="binoct">2进制转8进制</option>
        <option value="bindec">2进制转10进制</option>
        <option value="binhex">2进制转16进制</option>
        <option value="octbin">8进制转2进制</option>
        <option value="octdec">8进制转10进制</option>
        <option value="octhex">8进制转16进制</option>
        <option value="hexbin">16进制转2进制</option>
        <option value="hexoct">16进制转8进制</option>
        <option value="hexdec">16进制转10进制</option>
    </select>
    <input type="submit" value="转换"/>
    <input type="text" value="<?php echo @$result;?>"/>
</form>
<p class="beian-bottom">蜀ICP备19004669号-1</p>
</body>
</html>
