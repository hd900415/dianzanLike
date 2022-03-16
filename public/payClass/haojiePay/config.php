<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

header("Content-type:text/html;charset=utf-8");

include 'datebase.php';
$Db = new Datebase();

// 商户号
define("MERCHANTCODE", "408");
// payKEY
define("PAYKEY", "KU2WP8NQPSC5ZFRVPIDURTXIE65AUMWPZW72MDZJBLYJCBUJYBMXTXPWUY4FCUHP743JBLR1KJSJEYKPD6WODILLNFSWIW9UT7MV2EDCBUFTSAOH93OZX3NVKJRBGMDW");
// paySecret
define("APPID", "e21d913413b84de6928b07f5b56ebecc");
// 同步地址
define("MERURL", "http://".$_SERVER['HTTP_HOST']);
// 当前文件夹名
$dirPath = __DIR__;
$dirIndex = strrpos($dirPath, '\\');
$folderName = substr($dirPath, $dirIndex+1);
define("FOLDER", $folderName);
// 异步地址
define("NOTIFYURL", MERURL."/payClass/haojiePay/sycNotice.php");


/* 构建签名原文 */
function sign_src($dataArray, $md5Key){ 
    ksort($dataArray);

    foreach ($dataArray as $key => $value) {
        if ($value == '') continue;
        $sign_src .= $key.'='.$value.'&';
    }
    $signSrc = $sign_src.'key='.$md5Key;

    return strtoupper(md5($signSrc));
}

/**将关联数组转换为string */
function ascarray2string($get_array){
    $result = array_map(
        function($key,$value){
        return $key."=".$value;
        },
        array_keys($get_array),
        array_values($get_array)
    );
    $result = join("&",$result);
    return $result;
}

function postSend($urlString, $dataArray, $acceptString = "application/json"){
    $headers = array("Content-type: application/json;charset='utf-8'",
        "Accept: $acceptString",
        "Cache-Control: no-cache",
        "Pragma: no-cache"
    );
    $dataJsonString = json_encode($dataArray);

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

function randomFloat($min = 0, $max = 1) {
    $num = $min+mt_rand()/mt_getrandmax()*($max-$min);
    $num = round($num,2);
    return $num;
}