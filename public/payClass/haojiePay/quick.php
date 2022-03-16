<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/**
 * 收银台模式
 * http://120.77.255.230:8080/payType/haoPay/online.php?uid=183&price=100&typeid=1&bid=93&bank_code=ICBC
 */
include 'config.php';

$param = $Db->param();

//$Db->ajaxReturn($param);

// paykey
$dataArray['payKey'] = PAYKEY;
// 支付金额
$dataArray['orderPrice'] = $param['price'];

// 订单号
$param['orderNumber'] = 'C'.time();
$dataArray['outTradeNo'] = $param['orderNumber'].'U'.$param['uid'];

// 方式
$dataArray['productType'] = '90000103';

//$dataArray['bankAccountType'] = 'PRIVATE_DEBIT_ACCOUNT';//银行账户类型，对私借记卡
// 时间戳
$dataArray['orderTime'] = date('YmdHis');

$dataArray['productName'] = 'CNPPAY';

$dataArray['orderIp'] = '127.0.0.1';

//$dataArray['bankCode'] = $param['bank_code'];

$dataArray['remark'] = $param['uid'];

// 服务器异步通知地址
$dataArray['notifyUrl'] = NOTIFYURL;

$dataArray['returnUrl'] = MERURL;

//$dataArray['subPayKey'] = '';

// 签名
$dataArray['sign'] = sign_src($dataArray, PAYSECRET);


//数据验证
$param['paytype'] = FOLDER.'-DEV8888';
$verifyRes = $Db->verifyQuick($param);
if ($verifyRes != 'Y') {
    $Db->ajaxReturn($verifyRes);
}

// 提交地址
$submitUrl = 'http://47.75.170.98:8081/gateway//cnpGateWayPay/prePay';

?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<!-- <body> -->
<body onload="document.getElementById('sub').submit();">
    <form action="<?=$submitUrl;?>" method="post" id="sub" accept-charset="utf-8">
        <?php foreach ($dataArray as $key => $value): ?>
        <input type="hidden" name="<?=$key;?>" value="<?=$value;?>">
        <?php endforeach; ?>
    </form>
</body>
</html>