<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/**
 * 扫码
 * http://120.77.255.230:8080/payType/haoPay/scan.php?uid=183&price=20&typeid=1&scanType=alipay&bid=160
 */
include 'phpqrcode.php';
include 'config.php';

$param = $Db->param();

switch ($param['scanType']) {
	case 'yinlian':
		$productType = '1';
		$productName = 'zfb';
		$submitUrl = 'https://api.scp365.cn/payCenter/unionqrpay';
		break;
	default:
		$productType = 'error';
		$productName = 'error';
		break;
}

// 商户号
$dataArray['partner'] = MERCHANTCODE;
// 支付金额
$dataArray['amount'] = sprintf("%.2f",$param['price']);
// 时间戳
$dataArray['request_time'] = time();
// 订单号
$param['orderNumber'] = 'C'.time().'U'.$param['uid'];
$dataArray['trade_no'] = $param['orderNumber'];

// 服务器异步通知地址
$dataArray['notify_url'] = NOTIFYURL;

$dataArray['callback_url'] = MERURL;
// 签名
$dataArray['sign'] = sign_src($dataArray, MD5KEY);

//数据验证
$param['paytype'] = FOLDER.'-'.$dataArray['partner'];
$verifyRes = $Db->verifyScan($param);
if ($verifyRes != 'Y') $Db->ajaxReturn($verifyRes);

/*$res = $Db->curl_post('https://api.scp365.cn/payCenter/unionqrpay', $dataArray);
$resArray = json_decode($res, true);

if ($resArray['is_success'] != 'T') {
	$rdata['code'] = 5;
	$rdata['code_dec'] = $resArray['fail_msg'];
	$Db->ajaxReturn($rdata,'JSON',JSON_UNESCAPED_UNICODE);
}*/

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