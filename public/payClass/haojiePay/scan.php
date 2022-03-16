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
	case '8000':
		$productType = '8000';//网银支付
		$productName = 'wyzf';
		break;
	case '8002':
		$productType = '8002';//微信扫码支付
		$productName = 'wxsm';
		break;
	case '8003':
		$productType = '8003';//微信H5
		$productName = 'wechat';
		/*$arr_price = array(30,50,100);
		if (!in_array($param['price'], $arr_price)) {
			$Db->ajaxReturn('该支付通道仅限30,50,100的金额！','JSON',JSON_UNESCAPED_UNICODE);
		}*/
		break;
	case '8006':
		$productType = '8006';//支付宝扫码支付
		$productName = 'zfbsm';
		break;
	case '8007':
		$productType = '8007';//支付宝H5支付
		$productName = 'zfbh5';
		break;
	case '8016':
		$productType = '8016';//支付宝PDD
		$productName = 'zfbpdd';
		break;
	case '8017':
		$productType = '8017';//支付宝转卡
		$productName = 'zfbzk';
		break;
	case '8018':
		$productType = '8018';//支付宝话费H5
		$productName = 'zfbhf';
		break;
	default:
		$productType = 'error';
		$productName = 'error';
		break;
}


$dataArray['mchId'] = MERCHANTCODE;
// appid
$dataArray['appId'] = APPID;
// 支付金额
$dataArray['amount'] = $param['price']*100;//单位分

// 订单号
$param['orderNumber'] = 'C'.time().'U'.$param['uid'];
$dataArray['mchOrderNo'] = $param['orderNumber'];


// 通道id
$dataArray['productId'] = $productType;

$dataArray['currency'] = 'cny';
// 时间戳

//$dataArray['pay_applydate'] = date("Y-m-d H:i:s");;

// 服务器异步通知地址
$dataArray['notifyUrl'] = NOTIFYURL;

$dataArray['retuenUrl'] = MERURL;

$dataArray['subject'] = $productName;
$dataArray['body'] = $productName;
// 签名
$dataArray['sign'] = sign_src($dataArray, PAYKEY);

$dataJsonString = json_encode($dataArray);
$postdata['params'] = $dataJsonString;
//$Db->ajaxReturn($dataArray);
//数据验证
$param['paytype'] = FOLDER.'-'.$dataArray['pay_memberid'];
$verifyRes = $Db->verifyScan($param);
//$Db->ajaxReturn($param);
if ($verifyRes != 'Y') $Db->ajaxReturn($verifyRes);

$res = $Db->curl_post('http://47.254.44.144:3020/api/pay/create_order', $postdata);
$resArray = json_decode($res, true);

if ($resArray['retCode'] != 'SUCCESS') {
	$rdata['code'] = 5;
	$rdata['code_dec'] = $resArray;
	$Db->ajaxReturn($rdata,'JSON',JSON_UNESCAPED_UNICODE);
}
//$paycontent = json_decode($resArray['content'], true);

header("Location:".$resArray['payParams']['payUrl']);


// $rdata['code'] = 0;
// $rdata['img_path'] = 'http://'.$_SERVER['HTTP_HOST'].'/payType/tudouPay'.ltrim($pic, '.');
// $Db->ajaxReturn($rdata);
//$submitUrl = 'https://www.autopay.vip/Pay_Index.html';
?>
<!-- <!DOCTYPE html>
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
</html> -->