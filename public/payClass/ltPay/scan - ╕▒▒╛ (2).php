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
	case 'alipay':
		$productType =  903;//支付宝转银行卡扫码
		$productName = 'zfb';
		break;
	case 'weixin':
		$productType = 902;//微信扫码
		$productName = 'wechat';
		break;
	case 'ysf':
		$productType = 916;//云闪付
		$productName = 'ysf';
		break;
	case 'alipaya':
		$productType = 904;//支付宝wap
		$productName = 'alipaya';
		break;
/*	case 'alipayb':
		$productType = 'pay_alipaybtb';//支付宝原生企业
		$productName = 'alipayb';
		break;
	case 'weixina':
		$productType = 'pay_wechatbank';//微信转卡
		$productName = 'wechat';
		break;
	case 'weixinb':
		$arr_price = array(20,30,50,100,200);
		if (!in_array($param['price'], $arr_price)) {
			$Db->ajaxReturn('该支付通道仅限20、30、50、100、200的金额！','JSON',JSON_UNESCAPED_UNICODE);
		}
		$productType = 'pay_wechatphone';//微信固额
		$productName = 'person';
		break;
	case 'alipayc':
		$arr_price = array(30,50,100,200,300,500);
		if (!in_array($param['price'], $arr_price)) {
			$Db->ajaxReturn('该支付通道仅限30、50、100、200,300,500的金额！','JSON',JSON_UNESCAPED_UNICODE);
		}
		$productType = 'pay_alipayphone';//支付宝固额
		$productName = 'wechat';
		break;*/
	default:
		$productType = 'error';
		$productName = 'error';
		break;
}


$dataArray['pay_memberid'] = MERCHANTCODE;
//$dataArray['fxnotifystyle'] = 2;

// 支付金额
$dataArray['pay_amount'] = $param['price'];//单位

// 订单号
$param['orderNumber'] = 'C'.time().'U'.$param['uid'];
$dataArray['pay_orderid'] = $param['orderNumber'];

// 方式
$dataArray['pay_bankcode'] = $productType;

// 服务器异步通知地址
$dataArray['pay_notifyurl'] = NOTIFYURL;

$dataArray['pay_callbackurl'] = MERURL;
$dataArray['pay_applydate']        = date("Ymd His");
//币种
//$dataArray['fxdesc']        = $productName;

//$dataArray['fxip'] = $_SERVER['REMOTE_ADDR'];
// 签名
//$signstr = $dataArray['fxid'].$dataArray['fxddh'].$dataArray['fxfee'].$dataArray['fxnotifyurl'].PAYKEY;

$sign = make_sign($dataArray,PAYKEY);
$dataArray['pay_md5sign'] = $sign;

$dataArray['pay_returnType']        = 'html';

$dataArray['clientip'] = $Db->get_client_ip();

//数据验证
$param['paytype'] = FOLDER.'-'.$dataArray['pay_memberid'];
$verifyRes = $Db->verifyScan($param);
//$Db->ajaxReturn($param);
if ($verifyRes != 'Y') $Db->ajaxReturn($verifyRes);
//$data['params'] = json_encode($dataArray);
//print_r($data['params']);
/*$res = $Db->curl_post('http://www.xejpay.com/Pay_Index.html', $dataArray);
$resArray = json_decode($res, true);
//print_r($resArray);

if ($resArray['status'] != 1) {
    $rdata['code'] = 5;
    $rdata['code_dec'] = $resArray;
    $Db->ajaxReturn($rdata,'JSON',JSON_UNESCAPED_UNICODE);
}


header("Location:".$resArray['payurl']);*/

// 提交地址
$submitUrl = 'https://www.ltpay.com.tw/Pay_Index_index.html';//'http://yiapi.lianlianspc.com/gateway/bankgateway';//

?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body onload="document.getElementById('sub').submit();">
    <form action="<?=$submitUrl;?>" method="post" id="sub" accept-charset="utf-8">
        <?php foreach ($dataArray as $key => $value): ?>
        <input type="hidden" name="<?=$key;?>" value="<?=$value;?>">
        <?php endforeach; ?>
    </form>
</body>
</html>