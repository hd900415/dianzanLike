<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/** 
 * 异步通知
 */

include 'config.php';

$param = $Db->param();
file_put_contents('data1.txt', json_encode($param));

// $jsonStr = '{"amount":"100.00000000","merchantCode":"900100696","orderId":"C2019022117573754695U183","sign":"175986BA8BB5B95D86BFEC1F39DCCA9B","interfaceVersion":"1.0","sysOrderId":"283296121307009024","status":"SUCCESS"}';
// $param = json_decode($jsonStr, true);

$reSign['merchantId'] = $param['merchantId'];
$reSign['timestamp'] = $param['timestamp'];
$reSign['signatureMethod'] = $param['signatureMethod'];
$reSign['signatureVersion'] = $param['signatureVersion'];
$reSign['orderId'] = $param['orderId'];
$reSign['status'] = $param['status'];
$reSign['jOrderId'] = $param['jOrderId'];
$reSign['notifyUrl'] = $param['notifyUrl'];
$reSign['orderType'] = $param['orderType'];
$reSign['amount'] = $param['amount'];
$reSign['currency'] = $param['currency'];
$reSign['actualAmount'] = $param['actualAmount'];
$reSign['fee'] = $param['fee'];
$reSign['payWay'] = $param['payWay'];
$reSign['payTime'] = $param['payTime'];
$reSign['jExtra'] = $param['jExtra'];

$sign = $param['signature'];

$sign1 = strtoupper(sign_src($reSign, MD5KEY));

$merchantTradeNo = $param['jOrderId'];
$position = strrpos($merchantTradeNo, 'U');
$orderNumber = substr($merchantTradeNo, 0, $position);
$uid = substr($merchantTradeNo, $position+1);
//验签
if($sign === $sign1 && $param['status'] == 3) {
    $res = $Db->runUpdate([
        'money'         =>  $param['amount'],
        'order_number'  =>  $param['jOrderId'],
        'uid'           =>  $uid,
    ]);
    if ($res) {
        $back = array(
            'code'=>0,
            'message'=>"ok",
        );
        echo json_encode($back);
    } else {
        echo '接收失败';
    }
}else {
	echo '接收失败';
}


/*$param = $Db->param();
file_put_contents('data1.txt', json_encode($param));
print_r($param);*/