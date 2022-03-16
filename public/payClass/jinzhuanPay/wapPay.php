<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/**
 * 扫码WAP
 */
include 'config.php';

$param = $Db->param();

// 商户号
$merchantCode = MERCHANTCODE;
// 订单号
$param['orderNumber'] = $outOrderId = 'C'.$Db->trading_number();
// 金额
$totalAmount = $param['price'] * 100;
// 提交时间
$merchantOrderTime = date('YmdHis');
// 异步地址
$notifyUrl = NOTIFYURL;
// 随机字符串
$randomStr = '1';

//数据验证
$param['paytype'] = 'jisanpay-'.$merchantCode;
$verifyRes = $Db->verifyScan($param);
if ($verifyRes != 'Y') {
    $Db->ajaxReturn($verifyRes);
}

// 参与签名字段
$sign_fields1 = Array(
    "merchantCode",
    "outOrderId",
    "totalAmount",
    "merchantOrderTime",
    "notifyUrl",
    "randomStr"
);
$map1 = Array(
    "merchantCode" => $merchantCode,
    "outOrderId" => $outOrderId,
    "totalAmount" => $totalAmount,
    "merchantOrderTime" => $merchantOrderTime,
    "notifyUrl" => $notifyUrl,
    "randomStr" => $randomStr
);

$sign0 = sign_mac($sign_fields1, $map1, MD5KEY);
// 将小写字母转成大写字母
$sign1 = strtoupper($sign0);

// 提交地址
$submitUrl = 'http://148.70.23.175/expand/wap/createOrder.do';
// 使用方法
switch ($param['scanType']) {
    case 'weixin_wap':
        $payWay = '00';
        break;
    case 'qq_wap':
        $payWay = '02';
        break;
    default:
        $payWay = '01';
        break;
}
$post_data1 = array(
    'merchantCode'      => $merchantCode,
    'outOrderId'        => $outOrderId,
    'totalAmount'       => $totalAmount,
    'goodsName'         => '',
    'goodsDescription'  => '',
    'merchantOrderTime' => $merchantOrderTime,
    'lastPayTime'       => '',
    'notifyUrl'         => $notifyUrl,
    'randomStr'         => $randomStr,
    'ext'               => $param['uid'],
    'payWay'            => $payWay,
    'sign'              => $sign1
);

?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<!-- <body> -->
<body onload="document.getElementById('sub').submit();">
    <form action="<?=$submitUrl;?>" method="post" id="sub" accept-charset="utf-8">
        <?php foreach ($post_data1 as $key => $value): ?>
        <input type="hidden" name="<?=$key;?>" value="<?=$value;?>">
        <?php endforeach; ?>
    </form>
</body>
</html>