<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/** 
 * 异步通知
 */

include 'config.php';

$param = $Db->param();
file_put_contents('data.txt', json_encode($param)."\r\n",FILE_APPEND);

$sign = $param['sign'];
unset($param['sign']);
if (isset($param['attach'])) {
   unset($param['attach']);
}

// 签名
/*$signArray = array(
    'customerid'    =>  $param['customerid'],
    'status'        =>  $param['status'],
    'sdpayno'       =>  $param['sdpayno'],
    'sdorderno'     =>  $param['sdorderno'],
    'total_fee'     =>  $param['total_fee'],
    'paytype'       =>  $param['paytype'],
);*/
$sign1 = sign_src($param, PAYKEY);

$merchantTradeNo = $param['mchOrderNo'];
$position = strrpos($merchantTradeNo, 'U');
$orderNumber = substr($merchantTradeNo, 0, $position);
$uid = substr($merchantTradeNo, $position+1);

//验签
if($sign === $sign1 && $param['status'] == 2) {
    $res = $Db->runUpdate([
        'money'         =>  $param['amount']/100,
        'order_number'  =>  $param['mchOrderNo'],
        'uid'           =>  $uid,
    ]);
    if ($res) {
        echo 'success';
    } else {
        echo '接收失败';
        file_put_contents('data.txt', '接收失败'."\r\n",FILE_APPEND);
    }
}else {
	echo '签名失败';
    file_put_contents('data.txt', '签名失败sin:'.$sign.' sin1:'.$sign1."\r\n",FILE_APPEND);
}