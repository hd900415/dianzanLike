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
if (isset($param['attach'])) unset($param['attach']);
// 签名
//$signstr = $param['fxstatus'].$param['fxid'].$param['fxddh'].$param['fxfee'].PAYKEY;

$sign1 = make_sign($dataArray,PAYKEY);
//$sign1  = strtoupper($sign11);
$merchantTradeNo = $param['orderid'];
$position = strrpos($merchantTradeNo, 'U');
$orderNumber = substr($merchantTradeNo, 0, $position);
$uid = substr($merchantTradeNo, $position+1);

//验签
if($sign === $sign1 && $param['returncode'] == '00') {
    $res = $Db->runUpdate([
        'money'         =>  $param['amount'],
        'order_number'  =>  $param['orderid'],
        'uid'           =>  $uid,
    ]);
    if ($res) {
/*        $data = array(
            'ret_code'=>'0000',
            'ret_msg' =>'交易成功'
            );*/
        echo 'OK';
    } else {
        file_put_contents('data.txt', '上分失败'.$sign1."\r\n",FILE_APPEND);
        echo '上分失败';
    }
}else {
    file_put_contents('data.txt', '验签失败'.$sign1."\r\n",FILE_APPEND);
	echo '验签失败';
}