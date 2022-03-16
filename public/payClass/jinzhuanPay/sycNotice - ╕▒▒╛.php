<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/** 
 * 异步通知
 */

include 'config.php';

$param = $Db->param();
file_put_contents('data.txt', json_encode($param));

// $jsonStr = '{"amount":"100.00000000","merchantCode":"900100696","orderId":"C2019022117573754695U183","sign":"175986BA8BB5B95D86BFEC1F39DCCA9B","interfaceVersion":"1.0","sysOrderId":"283296121307009024","status":"SUCCESS"}';
// $param = json_decode($jsonStr, true);

$reSign['input_charset'] = $param['input_charset'];
$reSign['sign_type'] = $param['sign_type'];
$reSign['request_time'] = $param['request_time'];
$reSign['trade_id'] = $param['trade_id'];
$reSign['out_trade_no'] = $param['out_trade_no'];
$reSign['amount_str'] = $param['amount_str'];
$reSign['amount_fee'] = $param['amount_fee'];
$reSign['status'] = $param['status'];
$reSign['for_trade_id'] = $param['for_trade_id'];
$reSign['business_type'] = $param['business_type'];
$reSign['remark'] = $param['remark'];
$reSign['create_time'] = $param['create_time'];
$reSign['modified_time'] = $param['modified_time'];

$sign = $param['sign'];

$sign1 = sign_src($reSign, MD5KEY);

$merchantTradeNo = $param['out_trade_no'];
$position = strrpos($merchantTradeNo, 'U');
$orderNumber = substr($merchantTradeNo, 0, $position);
$uid = substr($merchantTradeNo, $position+1);

//验签
if($sign === $sign1 && $param['status'] == 1) {
    $res = $Db->runUpdate([
        'money'         =>  $param['amount_str'],
        'order_number'  =>  $orderNumber,
        'uid'           =>  $uid,
    ]);
    if ($res) {
        echo 'success';
    } else {
        echo '接收失败';
    }
}else {
	echo '接收失败';
}