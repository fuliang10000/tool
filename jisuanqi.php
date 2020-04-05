<?php
/**
 * 计算器
 */
header('Content-Type: text/html; charset=utf-8');
function counter(){
    $arr = func_get_args();
    if(empty($arr[0]) && empty($arr[2])){
        return "不能为空！";
    }elseif($arr[1] == "/" && $arr[2] == 0){
        return "除数不能为0";
    }elseif($arr[1] == "+"){
        return $arr[0]+$arr[2];
    }elseif($arr[1] == "-"){
        return $arr[0]-$arr[2];
    }elseif($arr[1] == "*"){
        return $arr[0]*$arr[2];
    }elseif($arr[1] == "/"){
        return $arr[0]/$arr[2];
    }
}
//接收数据
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $num1 = $_POST['num1'];
    $func = $_POST['func'];
    $num2 = $_POST['num2'];
    $result = counter($num1,$func,$num2);
};
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
    第一个数：<input type="text" name="num1"/><br/>
    &emsp;操作符：<select name="func" style="width: 50px">
        <option value="+"> + </option>
        <option value="-"> - </option>
        <option value="*"> * </option>
        <option value="/"> / </option>
    </select><br/>
    第一个数：<input type="text" name="num2"/><br/>
    <input type="submit" value="计算"/>结果为：<?php echo @$result;?>
</form>
<p class="beian-bottom">蜀ICP备19004669号-1</p>
</body>
</html>
