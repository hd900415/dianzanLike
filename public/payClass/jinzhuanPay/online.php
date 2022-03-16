<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/**
 * 收银台模式
 * http://120.77.255.230:8080/payType/haoPay/online.php?uid=183&price=100&typeid=1&bid=93&bank_code=ICBC
 */
include 'config.php';

$param = $Db->param();

// 版本号
$dataArray['version'] = '3.0';
// 接口名称
$dataArray['method'] = 'yy.online.interface';
// 商户号
$dataArray['partner'] = MERCHANTCODE;
// 银行编号
$dataArray['banktype'] = $param['bank_code'];
// 金额
$dataArray['paymoney'] = $param['price'];
// 订单号
$param['orderNumber'] = 'C'.$Db->trading_number();
$dataArray['ordernumber'] = $param['orderNumber'].'U'.$param['uid'];
// 服务器异步通知地址
$dataArray['callbackurl'] = NOTIFYURL;
// 校验数据取值
$dataArray['sign'] = sign_src($dataArray, MD5KEY);
// 是否显示收银台。1：网关；2：返回二维码地址
$dataArray['isshow'] = 1;


//数据验证
$param['paytype'] = FOLDER.'-'.$dataArray['partner'];
$verifyRes = $Db->verifyOnline($param);
if ($verifyRes != 'Y') {
    $Db->ajaxReturn($verifyRes);
}

// 提交地址
$submitUrl = 'http://open.yyy114.com/online/gateway';

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