<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

header("Content-type:text/html;charset=utf-8");

include 'datebase.php';
$Db = new Datebase();

// 商户号
define("MERCHANTCODE", "10577");
// payKEY
define("PAYKEY", "WWFDFp4tQEJ4zhW52VJnzuEhGrP4znyu");
// paySecret
//define("PAYSECRET", "f745c7f3fade4a7daeaea4135350ce96");
// 同步地址
define("MERURL", "http://".$_SERVER['HTTP_HOST']);
// 当前文件夹名
$dirPath = __DIR__;
$dirIndex = strrpos($dirPath, '\\');
$folderName = substr($dirPath, $dirIndex+1);
define("FOLDER", $folderName);
// 异步地址
define("NOTIFYURL", MERURL."/payClass/ltPay/sycNotice.php");


/**
 * 生成签名
 * $signdata 签名数据 array
 * $api_key 商户秘钥
 * @return 返回md5签名
 */
function make_sign($signdata,$api_key)
{
    //签名步骤一：按字典序排序参数
    ksort($signdata);
    $string = to_params($signdata);
    //签名步骤二：在string后加入KEY
    $string = $string . "&key=".$api_key;
    //$file = "make_sign1";
    //$path ="/data/wwwroot/";
    //$content = $string;
    //F($file,$content,$path);
    //签名步骤三：MD5加密
    //file_put_contents('data.txt', $string."\r\n",FILE_APPEND);
    $string = md5($string);
    //签名步骤四：所有字符转为大写
    $result = strtoupper($string);
    return $result;
}

/**
 * 格式化参数格式化成url参数
 */
function to_params($signdata)
{
    $buff = "";
    foreach ($signdata as $k => $v)
    {
        if($k != "sign" && $v != "" && !is_array($v)){
            $buff .= $k . "=" . $v . "&";
        }
    }

    $buff = trim($buff, "&");
    return $buff;
}

function postSend($urlString, $dataArray, $acceptString = "application/json"){
    $headers = array("Content-type: application/json;charset='utf-8'",
        "Accept: $acceptString",
        "Cache-Control: no-cache",
        "Pragma: no-cache"
    );
    $dataJsonString = json_encode($dataArray);
    //file_put_contents('data.txt', $dataJsonString."\r\n",FILE_APPEND);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlString);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($dataJsonString));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
