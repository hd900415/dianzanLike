<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/**
 * 在线网银
 */
include 'config.php';

$param = $Db->param();

// 商户号
$merchantCode = MERCHANTCODE;
// 订单号
$param['orderNumber'] = $outOrderId = 'C'.$Db->trading_number();
// 金额
$totalAmount = $param['price'] * 100;
// 银行代码
$bankCode = $param['bank_code'];
// 提交时间
$orderCreateTime = date('YmdHis');
// 同步地址
$merUrl = MERURL;
// 异步地址
$noticeUrl = NOTIFYURL;
// 支付银行卡类型，01：借记卡
$bankCardType = '01';
// 备注信息
$ext = $param['uid'];

//数据验证
$param['paytype'] = 'jisanpay-'.$merchantCode;
$verifyRes = $Db->verifyOnline($param);
if ($verifyRes != 'Y') {
    $Db->ajaxReturn($verifyRes);
}

// 参与签名字段
$sign_fields1 = Array(
    "merchantCode",
    "outOrderId",
    "totalAmount",
    "orderCreateTime",
    "lastPayTime",
);
$map1 = Array(
    "merchantCode" => $merchantCode,
    "outOrderId" => $outOrderId,
    "totalAmount" => $totalAmount,
    "orderCreateTime" => $orderCreateTime,
    "lastPayTime" => ''
);

$sign0 = sign_mac($sign_fields1, $map1, MD5KEY);
// 将小写字母转成大写字母
$sign1 = strtoupper($sign0);

// 提交地址
$submitUrl = 'http://148.70.23.175/pre/ebank/pay.do';
// 使用方法
$post_data1 = array(
    'merchantCode'      => $merchantCode,
    'outOrderId'        => $outOrderId,
    'totalAmount'       => $totalAmount,
    'goodsName'         => '',
    'goodsExplain'      => '',
    'orderCreateTime'   => $orderCreateTime,
    'merUrl'            => $merUrl,
    'noticeUrl'         => $noticeUrl,
    'bankCode'          => $bankCode,
    'bankCardType'      => $bankCardType,
    'ext'               => $param['uid'],
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