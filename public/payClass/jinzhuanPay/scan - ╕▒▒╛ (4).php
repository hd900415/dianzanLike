<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/**
 * 扫码
 * http://120.77.255.230:8080/payType/haoPay/scan.php?uid=183&price=20&typeid=1&scanType=alipay&bid=160
 */
include 'phpqrcode.php';
include 'config.php';
/*$param['uid'] = 183;
$param['scanType'] = 'alipay';
$param['price'] = '300';*/

$param = $Db->param();

switch ($param['scanType']) {
	case 'alipay':
		$productType =  'AliPay';
		$productName = 'zfb';
		break;
	case 'weixin':
		$productType = 'WechatPay';
		$productName = 'wechat';
		break;
	default:
		$productType = 'error';
		$productName = 'error';
		break;
}

// 商户号
$dataArray1['merchantId'] = MERCHANTCODE;

// 时间戳
$dataArray1['timestamp'] = $Db->time_msectime();

$dataArray1['signatureMethod'] = 'HmacSHA256';
$dataArray1['signatureVersion'] = '1';
// 订单号
$param['orderNumber'] = 'C'.time().'U'.$param['uid'];
$dataArray['jOrderId'] = $param['orderNumber'];
$dataArray1['jOrderId']  = $param['orderNumber'];
$dataArray['jUserId'] = $param['uid'];
$dataArray['jUserIp'] = $Db->get_client_ip();
$dataArray['jExtra'] = 'abc';
// 方式
$dataArray['orderType'] = '1';
$dataArray['payWay'] = $productType;
// 支付金额
$dataArray['amount'] = $param['price'];
$dataArray['currency'] = 'CNY';
// 服务器异步通知地址
$dataArray['notifyUrl'] = NOTIFYURL;

$dataArray1['jUserId'] = $param['uid'];
$dataArray1['jUserIp'] = $Db->get_client_ip();
// 方式
$dataArray1['orderType'] = '1';
$dataArray1['payWay'] = $productType;
$dataArray1['jExtra'] = 'abc';
// 支付金额
$dataArray1['amount'] = $param['price'];
$dataArray1['currency'] = 'CNY';
// 服务器异步通知地址
$dataArray1['notifyUrl'] = NOTIFYURL;
// 签名
$dataArray1['signature'] = strtoupper(sign_src($dataArray1, MD5KEY));
/*print_r($dataArray1['signature']);
die();*/
//数据验证
$param['paytype'] = FOLDER.'-'.$dataArray1['merchantId'];
$verifyRes = $Db->verifyScan($param);
if ($verifyRes != 'Y') $Db->ajaxReturn($verifyRes);
$url = 'https://api.jzpay.vip/jzpay_exapi/v1/order/createOrder?signatureMethod='.$dataArray1['signatureMethod'].'&signatureVersion='.$dataArray1['signatureVersion'].'&merchantId='.$dataArray1['merchantId'].'&timestamp='.$dataArray1['timestamp'].'&signature='.$dataArray1['signature'];
/*print_r($url);
echo "</br>";
print_r($dataArray);
echo "</br>";*/
$res = $Db->curl_post($url, $dataArray);
//$res = postSend($url, $dataArray);
/*print_r($res);
die();*/
$resArray = json_decode($res, true);

if ($resArray['message'] != '成功') {
	$rdata['code'] = 5;
	$rdata['code_dec'] = $resArray['message'];
	$Db->ajaxReturn($rdata,'JSON',JSON_UNESCAPED_UNICODE);
}

// $TimeStr = preg_replace('/\.?/','',microtime(true));
// if(!file_exists('./img')) mkdir('./img');

// $pic = './img/'.$TimeStr.'.png';
// if(file_exists($pic)) unlink($pic);

// $qrcode = $resArray['qrCode'];
// QRcode::png ( $qrcode, $pic, 'L', 10, 2 );

header("Location:".$resArray['data']['paymentUrl']);

// $rdata['code'] = 0;
// $rdata['img_path'] = 'http://'.$_SERVER['HTTP_HOST'].'/payType/tudouPay'.ltrim($pic, '.');
// $Db->ajaxReturn($rdata);