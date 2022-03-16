<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/**
 * 出款
 */
include 'config.php';

$param = $Db->param();

// $param['account_Name'] = '赵忠意';
// $param['account_Number'] = '6217003380009745164';
// $param['bank_Code'] = 'CCB';

$verifyRes = $Db->verifyDraw($param);
if ($verifyRes != 1) exit($verifyRes);

// 商户号
$merchantCode = MERCHANTCODE;
// 订单号
$outOrderId = $param['order'];
// 随机数
$nonceStr = md5($outOrderId);
// 金额
$totalAmount = $param['amount'] * 100;
// 收款账号
// $intoCardNo = $param['account_Number'] = '6217003380009745164';
$intoCardNo = $param['account_Number'];
// 收款账户名
// $intoCardName = $param['account_Name'] = '赵忠意';
$intoCardName = $param['account_Name'];
// 收款账户类型   1-对公，2-对私
$intoCardType = 2;
// 收款开户行行号，该字段必须传值“”，字符串空值，但是必须参与签名
$bankCode = '';
// 到账类型，03-非实时到账，04-实时到账
$type = '04';
// 收款人开户行名称，该字段必须传值“”，字符串空值，但是必须参与签名
$bankName = '';

// 参与签名字段
$sign_fields1 = Array(
    "merchantCode",
    "outOrderId",
    "nonceStr",
    "totalAmount",
    "intoCardNo",
    "intoCardName",
    "intoCardType",
    "bankCode",
    "type",
    "bankName",
);
$map1 = Array(
    "merchantCode" => $merchantCode,
    "outOrderId" => $outOrderId,
    "nonceStr" => $nonceStr,
    "totalAmount" => $totalAmount,
    "intoCardNo" => $intoCardNo,
    "intoCardName" => $intoCardName,
    "intoCardType" => $intoCardType,
    "bankCode" => $bankCode,
    "type" => $type,
    "bankName" => $bankName,
);

$sign0 = sign_mac($sign_fields1, $map1, MD5KEY);
// 将小写字母转成大写字母
$sign1 = strtoupper($sign0);


// 使用方法
$post_data1 = array(
    'merchantCode'      => $merchantCode,
    'outOrderId'        => $outOrderId,
    'nonceStr'          => $nonceStr,
    'totalAmount'       => $totalAmount,
    'intoCardNo'        => $intoCardNo,
    'intoCardName'      => $intoCardName,
    'intoCardType'      => $intoCardType,
    'bankCode'          => $bankCode,
    'type'              => $type,
    'bankName'          => $bankName,
    'sign'              => $sign1
);

$res = $Db->curl_post('http://148.70.23.175/expand/payment/payment.do', $post_data1);
$resArray = json_decode($res, true);

if ($resArray['code'] != '00') exit('提交失败');

echo 1;