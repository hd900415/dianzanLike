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
/*print_r($param);
$result = 22;
echo $result;
die();*/
$verifyRes = $Db->verifyDraw($param);
if ($verifyRes != 1) exit($verifyRes);

// 商户号
$merchantCode = MERCHANTCODE;
// 订单号
$outOrderId = $param['order'];
// 随机数
$nonceStr = 'C'.time();
// 金额
$totalAmount = $param['amount'];

if ($totalAmount < 10) {
    echo "出款最低10元";
    die();
}
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
$bankName = $param['bank_Name'];

$cityinfo = $Db->get_city($outOrderId);


// 参与签名字段
$sign_fields1 = Array(
    "appId",
    "amount",
    "orderId",
    "accountName",
    "accountNo",
    "bankName",
    "asyncNotifyUrl",
    "quick",
    "subject",
);
$map1 = Array(
    "mchid" => $merchantCode,
    "money" => $totalAmount,
    "out_trade_no" => $outOrderId,
    "accountname" => $intoCardName,
    "cardnumber" => $intoCardNo,
    "bankname" => $bankName,
    "subbranch" => $bankName,
    "province" => $cityinfo['province'],
    "city" => $cityinfo['city'],
    //"extends" => '',
    "lhh"   =>  123,
);

$map1['pay_md5sign'] = sign_src($map1, PAYKEY);
$res = $Db->curl_post('http://154.221.23.134/Payment_Dfpay_add.html', $map1);
$resArray = json_decode($res, true);
/*print_r($map1);
print_r($resArray);
die();*/
if ($resArray['status'] == 'success'){
    echo 1;
}else {
    echo '提交失败'.$resArray['status'].$resArray['msg'];
}
