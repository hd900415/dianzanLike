<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/** 
 * 异步通知
 */

include 'config.php';

$param = $Db->param();
file_put_contents('data.txt', json_encode($param));

$sign = $param["sign"];

//签名数组
$sign_fields1 = Array(
    "merchantCode",
    "transType",
    "instructCode",
    "outOrderId",
    "transTime",
    "totalAmount"   
);
//获取异步通知数据，并赋值给数组
$map = Array(
    "merchantCode" => $param["merchantCode"],
    "transType" => $param["transType"], 
    "instructCode" => $param["instructCode"],
    "outOrderId" => $param["outOrderId"], 
    "transTime" => $param["transTime"], 
    "totalAmount" => $param["totalAmount"]
);
    
$sign0 = sign_mac($sign_fields1, $map, MD5KEY);
// 将小写字母转成大写字母
$sign1 = strtoupper($sign0);

//验签
if($sign === $sign1) {
    $res = $Db->runUpdate([
        'money'         =>  $param['totalAmount'] / 100,
        'order_number'  =>  $param['outOrderId'],
        'uid'           =>  $param['ext'],
    ]);
    if ($res) {
        echo "{'code':'00'}";
    } else {
        echo "{'code':'01'}";
    }
}else {
	echo "{'code':'01'}";
}