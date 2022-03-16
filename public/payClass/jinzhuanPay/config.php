<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

header("Content-type:text/html;charset=utf-8");

include 'datebase.php';
$Db = new Datebase();

/*// 商户号
define("MERCHANTCODE", "757cc6ac36956301ea3dc89fbf94d42c");
// MD5KEY
define("MD5KEY", "de54f881ea71f87b1388c0c303a9796f");*/
// 商户号
define("MERCHANTCODE", "f23b57996bc76232b68cc4da7f7a9435");
// MD5KEY
define("MD5KEY", "179a49203a3a59992877dc8cfe249f7a");
// 同步地址
define("MERURL", "http://".$_SERVER['HTTP_HOST']);
// 当前文件夹名
$dirPath = __DIR__;
$dirIndex = strrpos($dirPath, '\\');
$folderName = substr($dirPath, $dirIndex+1);
define("FOLDER", $folderName);
// 异步地址
define("NOTIFYURL", MERURL."/payClass/jinzhuanPay/sycNotice.php");


/* 构建签名原文 */
function sign_src($dataArray, $md5Key){	
    ksort($dataArray);

    $sign_src = "";
    foreach ($dataArray as $key => $value) {
    	if ($value == '') continue;
        $sign_src .= $key.'='.$value.'&';
    }

    $signSrc = rtrim($sign_src,'&');
    //file_put_contents('data.txt', $signSrc);
    return hash_hmac('sha256',$signSrc,$md5Key);
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJsonString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
