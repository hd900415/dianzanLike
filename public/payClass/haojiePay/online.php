<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/**
 * 扫码
 * http://120.77.255.230:8080/payType/haoPay/scan.php?uid=183&price=20&typeid=1&bank_code=alipay&bid=160
 */
include 'phpqrcode.php';
include 'config.php';

$param = $Db->param();

switch ($param['bank_code']) {
	case 'yinlian':
		$productType = '907';
		$productName = 'yinlian';
		break;
	case 'weixin':
		$productType = '902';
		$productName = 'wechat';
		break;
	case 'alipay':
		$productType = '903';
		$productName = 'alipay';
		break;
	default:
		$productType = 'error';
		$productName = 'error';
		break;
}


$dataArray['pay_memberid'] = MERCHANTCODE;
// paykey
//$dataArray['payKey'] = PAYKEY;
// 支付金额
$dataArray['pay_amount'] = $param['price'];//单位

// 订单号
$param['orderNumber'] = 'C'.time().'U'.$param['uid'];
$dataArray['pay_orderid'] = $param['orderNumber'];

// 方式
$dataArray['pay_bankcode'] = $productType;
// 时间戳

$dataArray['pay_applydate'] = date("Y-m-d H:i:s");;

// 服务器异步通知地址
$dataArray['pay_notifyurl'] = NOTIFYURL;

$dataArray['pay_callbackurl'] = MERURL;

// 签名
$dataArray['pay_md5sign'] = sign_src($dataArray, PAYKEY);

$dataArray['pay_productname'] = $productName;
//$Db->ajaxReturn($dataArray);
//数据验证
$param['paytype'] = FOLDER.'-'.$dataArray['pay_memberid'];
$verifyRes = $Db->verifyScan($param);
//$Db->ajaxReturn($param);
if ($verifyRes != 'Y') $Db->ajaxReturn($verifyRes);

/*$res = $Db->curl_post('http://www.ixmpay.com/Pay_Index.html', $dataArray);
$resArray = json_decode($res, true);

if ($resArray['platRespMessage'] != '交易成功') {
	$rdata['code'] = 5;
	$rdata['code_dec'] = $resArray;
	$Db->ajaxReturn($rdata,'JSON',JSON_UNESCAPED_UNICODE);
}
$paycontent = json_decode($resArray['content'], true);
//$Db->ajaxReturn($resArray);
// $TimeStr = preg_replace('/\.?/','',microtime(true));
// if(!file_exists('./img')) mkdir('./img');

// $pic = './img/'.$TimeStr.'.png';
// if(file_exists($pic)) unlink($pic);

// $qrcode = $resArray['qrCode'];
// QRcode::png ( $qrcode, $pic, 'L', 10, 2 );

header("Location:".$paycontent['payUrl']);*/


// $rdata['code'] = 0;
// $rdata['img_path'] = 'http://'.$_SERVER['HTTP_HOST'].'/payType/tudouPay'.ltrim($pic, '.');
// $Db->ajaxReturn($rdata);
$submitUrl = 'http://154.221.23.134/Pay_Index.html';
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