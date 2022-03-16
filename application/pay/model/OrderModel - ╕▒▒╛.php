<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\pay\model;
use think\Model;
use think\Cache;
use GatewayClient\Gateway;

class OrderModel extends Model{

	protected $table = 'ly_order';

	//创建订单接口
	//返回支付页面 paymentUrl
	//直接创建订单
	public function createOrder(){

		//非Post提交
		if(!request()->isPost()){
			$data	= [
				'code'		=> 401,
				'message'	=> '参数错误'
			];
			return $data;
		}
		//status           订单状态;1=已创建,2=已支付,3=完成,4=取消 5过期 6 锁定 7 待支付
		$param			= input('param.');

		if(!isset($param['jUserIp']) or !isset($param['orderType']) or !isset($param['payWay']) or !isset($param['amount']) or !isset($param['currency']) or !isset($param['jUserId'])  or !isset($param['notifyUrl']) or !isset($param['orderType']) or !isset($param['signatureVersion']) or !isset($param['merchantId']) or !isset($param['timestamp']) or !isset($param['jOrderId']) or !isset($param['signature'])){
			$data	= [
				'code'		=> 402,
				'message'	=> '参数错误'
			];
			return $data;
		}

		if(!$param['jUserIp'] or !$param['orderType'] or !$param['payWay'] or !$param['amount'] or !$param['currency'] or !$param['jUserId']  or !$param['notifyUrl'] or !$param['orderType'] or !$param['signatureVersion'] or !$param['merchantId'] or !$param['timestamp'] or !$param['jOrderId']){
			$data	= [
				'code'		=> 402,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		if (!is_null(cache($param['jUserIp'].'_'.$param['jUserIp'])) && time() - cache($param['jUserIp'].'_'.$param['jUserIp']) < 5 ){
			$data	= [
				'code'		=> 411,
				'message'	=> '订单提交频繁！'
			];
			return $data;
		}
		
		cache($param['jUserIp'].'_'.$param['jUserIp'], time());

		//订单类型
		if($param['orderType']!=1){
			$data	= [
				'code'		=> 403,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//支付方式
		$payway = array('AliPay','WechatPay','BankPay');
		if(!in_array($param['payWay'],$payway)){
			$data	= [
				'code'		=> 404,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//订单金额
		if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $param['amount'])) {
			$data	= [
				'code'		=> 405,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		// 商户信息
		$merchantdata = model('Merchant')->field('id,merchantkey,order_alipay_min,order_alipay_max,order_wechat_min,order_wechat_max,order_bank_min
,order_bank_max,is_lock,alipay_state,wechat_state,bank_state')->where('merchantid',$param['merchantId'])->where('status',1)->where('is_lock',1)->findOrEmpty();

		if (!$merchantdata) return ['code'=>409,'message'=>'权限错误'];//商户是否存在

		switch($param['payWay']){
			case 'AliPay':
				$m_order_min	=	'order_alipay_min';
				$m_order_max	=	'order_alipay_max';
				$s_order_min	=	'm_alipay_recharge_min';
				$s_order_max	=	'm_alipay_recharge_max';
				if($merchantdata['alipay_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
			case 'WechatPay':
				$m_order_min	=	'order_wechat_min';
				$m_order_max	=	'order_wechat_max';
				$s_order_min	=	'm_wechat_recharge_min';
				$s_order_max	=	'm_wechat_recharge_max';
				if($merchantdata['wechat_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
			case 'BankPay':
				$m_order_min	=	'order_bank_min';
				$m_order_max	=	'order_bank_max';
				$s_order_min	=	'm_bank_recharge_min';
				$s_order_max	=	'm_bank_recharge_max';
				if($merchantdata['bank_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
		}

		// 平台配置
		$settingdata =	model('Setting')->field('order_alipay_min,order_alipay_max,order_wechat_min,order_wechat_max,order_bank_min,order_bank_max,m_alipay_recharge_min,m_alipay_recharge_max,m_wechat_recharge_min,m_wechat_recharge_max,m_bank_recharge_min,m_bank_recharge_max')->where('id','>',0)->findOrEmpty();

		if ($merchantdata[$m_order_min]) {
			if ($param['amount'] < $merchantdata[$m_order_min]) return ['code'=>406,'message'=>'参数错误'];
		} else {
			if ($param['amount'] < $settingdata[$s_order_min]) return ['code'=>406,'message'=>'参数错误'];
		}
		if ($merchantdata[$m_order_max]) {
			if ($param['amount'] > $merchantdata[$m_order_max]) return ['code'=>406,'message'=>'参数错误'];
		} else {
			if ($param['amount'] > $settingdata[$s_order_max]) return ['code'=>406,'message'=>'参数错误'];
		}
		
		//支付货币类型
		$currency = array('CNY');
		if(!in_array($param['currency'],$currency)){
			$data	= [
				'code'		=> 407,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//后台通知回调URL
		if((!preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', $param['notifyUrl']) && !preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', urldecode($param['notifyUrl']))) || !$param['notifyUrl']) {
			$data	= [
				'code'		=> 408,
				'message'	=> '参数错误'
			];
			return $data;
		}
		

		$shiwu = $this->where('mid','=',$merchantdata['id'])->where('juid','=',$param['jUserId'])->where('jip','=',$param['jUserIp'])->where('status','=',7)->where('ordertimes','>',match_msectime()-60*15*1000)->count();

		if($shiwu>4){
			$data	= [
				'code'		=> 413,
				'message'	=> '您有多笔订单未付款，请15分钟后再试！'
			];
			return $data;
		}
		
		$wu = $this->where('mid','=',$merchantdata['id'])->where('juid','=',$param['jUserId'])->where('jip','=',$param['jUserIp'])->where('status','=',7)->where('ordertimes','>',match_msectime()-60*5*1000)->count();
		if($wu>2){
			$data	= [
				'code'		=> 412,
				'message'	=> '您有多笔订单未付款，请5分钟后再试！'
			];
			return $data;
		}

		//重组订单数据
		$orderdata = array(
			'merchantId'			=>	$param['merchantId'],
			'timestamp'				=>	$param['timestamp'],
			'signatureMethod'		=>	$param['signatureMethod'],
			'signatureVersion'		=>	$param['signatureVersion'],
			'jUserIp'				=> 	$param['jUserIp'],
			'jExtra'				=>	$param['jExtra'],
			'orderType'				=>	$param['orderType'],
			'payWay'				=>	$param['payWay'],
			'amount'				=>	$param['amount'],
			'currency'				=>	$param['currency'],
			'jUserId'				=>	$param['jUserId'],
			'notifyUrl'				=>	$param['notifyUrl'],
			'jOrderId'				=>	$param['jOrderId'],
		);
	
		//签名
		ksort($orderdata);

		$orderstr = http_build_query($orderdata);
		
		$sha256str  =  hash_hmac('sha256', $orderstr, $merchantdata['merchantkey']);
		
		//判断签名
		if($sha256str != $param['signature']){
			$data['code']		= 410;
			$data['message']	= '参数错误';
			return $data;
		}
		
		$order_number = 'D'.trading_number();
		$trade_number = 'L'.trading_number();
		//进库 数据重组
		$total_data = array(
			'juid'				=>	$orderdata['jUserId'],//订单uID
			'mid'				=>	$merchantdata['id'],//接入商户id
			'orderid'			=>	$order_number,//订单编号
			'tid'				=>	$trade_number,//流水号
			'status'			=>	1,
			'jorderid'			=>	$orderdata['jOrderId'],//商户订单编号
			'jip'				=>  $orderdata['jUserIp'],//商户的客户IP
			'ordertype'			=>  $orderdata['orderType'],// 订单类型 1=充值订单;2=提现订单
			'timestamp'			=>  $orderdata['timestamp'],// 商户请求时间
			'ordertimes'		=>  match_msectime(),// 订单时间
			'payway'			=>	$orderdata['payWay'],// 支付方式;AliPay(支付宝);WechatPay(微信)
			'oamount'			=>	$orderdata['amount'],//  订单金额
			'currency'			=>	$orderdata['currency'],//  支付货币类型
			'notifyurl'			=>	base64_encode($orderdata['notifyUrl']),//后台通知回调URL
			'jextra'			=>	base64_encode($orderdata['jExtra']),//后台通知回调URL
			'signature'			=>	$param['currency'],//  签名
			'mkey'				=>	$merchantdata['merchantkey'],
			'bitype'			=>	2,//生成卖比订单
			'clienttype'		=>	1,
			'orderip'			=>	'',
		);
		
		$is_insert = $this->insertGetId($total_data);
		//进数据库
		if(!$is_insert){
			$data['code']		= 501;
			$data['message']	= '内部服务错误';
			return $data;
		}

		$ApiOrderModel = model('api/Order');
		//获取未接的订单 推送到抢单大厅
		$ApiOrderModel->pushNewOrder();
		
		$data['code']			= 0;
		$data['message']		= '成功';
		$data['signature']		= $sha256str;

		$payment = '&orderid='.$total_data['orderid'].'&amount='.$total_data['oamount'];

		$isHttps = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')) ? 'https' : 'http';	// 获取传输协议
		$returndata = array(
			'orderId'			=>	$total_data['orderid'],//商户必须在创建订单
			'orderType'			=>	$total_data['ordertype'],//订单类型 1：充值订单
			'paymentUrl'		=>	$isHttps.'://'.$_SERVER['HTTP_HOST'].'/pay/tips.php?payway='.$total_data['payway'].'&amount='.$total_data['oamount'].'&id='.$total_data['orderid'].'&payment='.base64_encode($payment),//支付页面 这个很重要，受理成功后，需要打开这个地址，让客户进行后续操作
		);
		$data['data']	=	$returndata;
		
		return $data;
		
	}
	
	//商户提交订单接口 
	//返回支付页面 paymentUrl 加密商户提交数据 商户跳转到平台支付页面 由支付平台提交 创建订单
	//不创建订单
	public function newCreateOrder(){
		
		//非Post提交
		if(!request()->isPost()){
			$data	= [
				'code'		=> 401,
				'message'	=> '参数错误'
			];
			return $data;
		}
		//status           订单状态;1=已创建,2=已支付,3=完成,4=取消 5过期 6 锁定 7 待支付
		$param			= input('param.');
		
		if(!isset($param['jUserIp']) or !isset($param['orderType']) or !isset($param['payWay']) or !isset($param['amount']) or !isset($param['currency']) or !isset($param['jUserId'])  or !isset($param['notifyUrl']) or !isset($param['orderType']) or !isset($param['signatureVersion']) or !isset($param['merchantId']) or !isset($param['timestamp']) or !isset($param['jOrderId']) or !isset($param['signature'])){
			$data	= [
				'code'		=> 402,
				'message'	=> '参数错误'
			];
			return $data;
		}

		if(!$param['jUserIp'] or !$param['orderType'] or !$param['payWay'] or !$param['amount'] or !$param['currency'] or !$param['jUserId']  or !$param['notifyUrl'] or !$param['orderType'] or !$param['signatureVersion'] or !$param['merchantId'] or !$param['timestamp'] or !$param['jOrderId']){
			$data	= [
				'code'		=> 402,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		if (!is_null(cache($param['jUserIp'].'_'.$param['jUserIp'])) && time() - cache($param['jUserIp'].'_'.$param['jUserIp']) < 5 ){
			$data	= [
				'code'		=> 411,
				'message'	=> '订单提交频繁！'
			];
			return $data;
		}
		cache($param['jUserIp'].'_'.$param['jUserIp'], time());

		//订单类型
		if($param['orderType']!=1){
			$data	= [
				'code'		=> 403,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//支付方式
		$payway = array('AliPay','WechatPay','BankPay');
		if(!in_array($param['payWay'],$payway)){
			$data	= [
				'code'		=> 404,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//订单金额
		if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $param['amount'])) {
			$data	= [
				'code'		=> 405,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		// 商户信息
		$merchantdata = model('Merchant')->field('id,merchantkey,order_alipay_min,order_alipay_max,order_wechat_min,order_wechat_max,order_bank_min
,order_bank_max,alipay_state,wechat_state,bank_state')->where('merchantid',$param['merchantId'])->where('status',1)->where('is_lock',1)->findOrEmpty();

		if (!$merchantdata) return ['code'=>409,'message'=>'权限错误'];//商户是否存在

		switch($param['payWay']){
			case 'AliPay':
				$m_order_min	=	'order_alipay_min';
				$m_order_max	=	'order_alipay_max';
				$s_order_min	=	'm_alipay_recharge_min';
				$s_order_max	=	'm_alipay_recharge_max';
				if($merchantdata['alipay_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
			case 'WechatPay':
				$m_order_min	=	'order_wechat_min';
				$m_order_max	=	'order_wechat_max';
				$s_order_min	=	'm_wechat_recharge_min';
				$s_order_max	=	'm_wechat_recharge_max';
				if($merchantdata['wechat_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
			case 'BankPay':
				$m_order_min	=	'order_bank_min';
				$m_order_max	=	'order_bank_max';
				$s_order_min	=	'm_bank_recharge_min';
				$s_order_max	=	'm_bank_recharge_max';
				if($merchantdata['bank_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
		}

		// 平台配置
		$settingdata =	model('Setting')->field('order_alipay_min,order_alipay_max,order_wechat_min,order_wechat_max,order_bank_min,order_bank_max,m_alipay_recharge_min,m_alipay_recharge_max,m_wechat_recharge_min,m_wechat_recharge_max,m_bank_recharge_min,m_bank_recharge_max')->where('id','>',0)->findOrEmpty();

		if ($merchantdata[$m_order_min]) {
			if ($param['amount'] < $merchantdata[$m_order_min]) return ['code'=>406,'message'=>'参数错误'];
		} else {
			if ($param['amount'] < $settingdata[$s_order_min]) return ['code'=>406,'message'=>'参数错误'];
		}
		if ($merchantdata[$m_order_max]) {
			if ($param['amount'] > $merchantdata[$m_order_max]) return ['code'=>406,'message'=>'参数错误'];
		} else {
			if ($param['amount'] > $settingdata[$s_order_max]) return ['code'=>406,'message'=>'参数错误'];
		}
		
		//支付货币类型
		$currency = array('CNY');
		if(!in_array($param['currency'],$currency)){
			$data	= [
				'code'		=> 407,
				'message'	=> '参数错误'
			];
			return $data;
		}
		//后台通知回调URL
		if((!preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', $param['notifyUrl']) && !preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', urldecode($param['notifyUrl']))) || !$param['notifyUrl']) {
			$data	= [
				'code'		=> 408,
				'message'	=> '参数错误'
			];
			return $data;
		}
		

		$shiwu = $this->where('mid','=',$merchantdata['id'])->where('juid','=',$param['jUserId'])->where('jip','=',$param['jUserIp'])->where('status','=',7)->where('ordertimes','>',match_msectime()-60*15*1000)->count();

		if($shiwu>4){
			$data	= [
				'code'		=> 413,
				'message'	=> '您有多笔订单未付款，请15分钟后再试！'
			];
			return $data;
		}
		
		$wu = $this->where('mid','=',$merchantdata['id'])->where('juid','=',$param['jUserId'])->where('jip','=',$param['jUserIp'])->where('status','=',7)->where('ordertimes','>',match_msectime()-60*5*1000)->count();
		if($wu>2){
			$data	= [
				'code'		=> 412,
				'message'	=> '您有多笔订单未付款，请5分钟后再试！'
			];
			return $data;
		}

		//重组订单数据
		$orderdata = array(
			'merchantId'			=>	$param['merchantId'],
			'timestamp'				=>	$param['timestamp'],
			'signatureMethod'		=>	$param['signatureMethod'],
			'signatureVersion'		=>	$param['signatureVersion'],
			'jUserIp'				=> 	$param['jUserIp'],
			'jExtra'				=>	$param['jExtra'],
			'orderType'				=>	$param['orderType'],
			'payWay'				=>	$param['payWay'],
			'amount'				=>	$param['amount'],
			'currency'				=>	$param['currency'],
			'jUserId'				=>	$param['jUserId'],
			'notifyUrl'				=>	$param['notifyUrl'],
			'jOrderId'				=>	$param['jOrderId'],
		);
	
		//签名
		ksort($orderdata);

		$orderstr = http_build_query($orderdata);
		
		$sha256str  =  hash_hmac('sha256', $orderstr, $merchantdata['merchantkey']);
		
		//判断签名
		if($sha256str != $param['signature']){
			$data['code']		= 410;
			$data['message']	= '参数错误';
			return $data;
		}
		
		//返回api前端
		$data['code']			= 0;
		$data['message']		= '成功';
		
		$data['signature']		= $sha256str;
		$orderurl				= $orderstr.'&signature='.$sha256str;
		//加密 商户提交数据
		$payment 				= auth_code($orderurl,'ENCODE');

		$isHttps = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')) ? 'https' : 'http';	// 获取传输协议
		
		$returndata = array(
			'orderType'			=>	$param['orderType'],//订单类型 1：充值订单
			'amount'			=>	$param['amount'],//订单金额
			'paymentUrl'		=>	$isHttps.'://'.$_SERVER['HTTP_HOST'].'/pay/?payWay='.$param['payWay'].'&amount='.$param['amount'].'&payment='.base64_encode($payment),//支付页面 这个很重要，受理成功后，需要打开这个地址，让客户进行后续操作
		);

		$data['data']	=	$returndata;
		
		return $data;
	}

	
	//第三方post访问
	//支付平台提交
	//支付平台创建订单
	public function postCreateOrder(){

		//非Post提交
		if(!request()->isPost()){
			$data	= [
				'code'		=> 401,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//status           订单状态;1=已创建,2=已支付,3=完成,4=取消 5过期 6 锁定 7 待支付

		$param			= input('param.');
		
		$cacheIp = request()->ip();//客户端IP

		if (!is_null(cache($cacheIp.'_'.$cacheIp)) && time() - cache($cacheIp.'_'.$cacheIp) < 5 ){
			$data	= [
				'code'		=> 411,
				'message'	=> '订单提交频繁！'
			];
			return $data;
		}
		cache($cacheIp.'_'.$cacheIp, time());

		if(!isset($param['jUserIp']) or !isset($param['orderType']) or !isset($param['payWay']) or !isset($param['amount']) or !isset($param['currency']) or !isset($param['jUserId'])  or !isset($param['notifyUrl']) or !isset($param['orderType']) or !isset($param['signatureVersion']) or !isset($param['merchantId']) or !isset($param['timestamp']) or !isset($param['jOrderId']) or !isset($param['signature'])){
			$data	= [
				'code'		=> 402,
				'message'	=> '参数错误'
			];
			return $data;
		}

		if(!$param['jUserIp'] or !$param['orderType'] or !$param['payWay'] or !$param['amount'] or !$param['currency'] or !$param['jUserId']  or !$param['notifyUrl'] or !$param['orderType'] or !$param['signatureVersion'] or !$param['merchantId'] or !$param['timestamp'] or !$param['jOrderId']){
			$data	= [
				'code'		=> 402,
				'message'	=> '参数错误'
			];
			return $data;
		}

		//订单类型
		if($param['orderType']!=1){
			$data	= [
				'code'		=> 403,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//支付方式
		$payway = array('AliPay','WechatPay','BankPay');
		if(!in_array($param['payWay'],$payway)){
			$data	= [
				'code'		=> 404,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//订单金额
		if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $param['amount'])) {
			$data	= [
				'code'		=> 405,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		// 商户信息
		$merchantdata = model('Merchant')->field('id,merchantkey,order_alipay_min,order_alipay_max,order_wechat_min,order_wechat_max,order_bank_min
,order_bank_max,alipay_state,wechat_state,bank_state')->where('merchantid',$param['merchantId'])->where('status',1)->where('is_lock',1)->findOrEmpty();

		if (!$merchantdata) return ['code'=>409,'message'=>'权限错误'];//商户是否存在

		switch($param['payWay']){
			case 'AliPay':
				$m_order_min	=	'order_alipay_min';
				$m_order_max	=	'order_alipay_max';
				$s_order_min	=	'm_alipay_recharge_min';
				$s_order_max	=	'm_alipay_recharge_max';
				if($merchantdata['alipay_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
			case 'WechatPay':
				$m_order_min	=	'order_wechat_min';
				$m_order_max	=	'order_wechat_max';
				$s_order_min	=	'm_wechat_recharge_min';
				$s_order_max	=	'm_wechat_recharge_max';
				if($merchantdata['wechat_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
			case 'BankPay':
				$m_order_min	=	'order_bank_min';
				$m_order_max	=	'order_bank_max';
				$s_order_min	=	'm_bank_recharge_min';
				$s_order_max	=	'm_bank_recharge_max';
				if($merchantdata['bank_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
		}

		// 平台配置
		$settingdata =	model('Setting')->field('order_alipay_min,order_alipay_max,order_wechat_min,order_wechat_max,order_bank_min,order_bank_max,m_alipay_recharge_min,m_alipay_recharge_max,m_wechat_recharge_min,m_wechat_recharge_max,m_bank_recharge_min,m_bank_recharge_max')->where('id','>',0)->findOrEmpty();

		if ($merchantdata[$m_order_min]) {
			if ($param['amount'] < $merchantdata[$m_order_min]) return ['code'=>406,'message'=>'参数错误'];
		} else {
			if ($param['amount'] < $settingdata[$s_order_min]) return ['code'=>406,'message'=>'参数错误'];
		}
		if ($merchantdata[$m_order_max]) {
			if ($param['amount'] > $merchantdata[$m_order_max]) return ['code'=>406,'message'=>'参数错误'];
		} else {
			if ($param['amount'] > $settingdata[$s_order_max]) return ['code'=>406,'message'=>'参数错误'];
		}
		
		//支付货币类型
		$currency = array('CNY');
		if(!in_array($param['currency'],$currency)){
			$data	= [
				'code'		=> 407,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//后台通知回调URL
		if((!preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', $param['notifyUrl']) && !preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', urldecode($param['notifyUrl']))) || !$param['notifyUrl']) {
			$data	= [
				'code'		=> 408,
				'message'	=> '参数错误'
			];
			return $data;
		}
		

		$shiwu = $this->where('mid','=',$merchantdata['id'])->where('juid','=',$param['jUserId'])->where('jip','=',$param['jUserIp'])->where('status','=',7)->where('ordertimes','>',match_msectime()-60*15*1000)->count();

		if($shiwu>4){
			$data	= [
				'code'		=> 413,
				'message'	=> '您有多笔订单未付款，请15分钟后再试！'
			];
			return $data;
		}
		
		$wu = $this->where('mid','=',$merchantdata['id'])->where('juid','=',$param['jUserId'])->where('jip','=',$param['jUserIp'])->where('status','=',7)->where('ordertimes','>',match_msectime()-60*5*1000)->count();
		if($wu>2){
			$data	= [
				'code'		=> 412,
				'message'	=> '您有多笔订单未付款，请5分钟后再试！'
			];
			return $data;
		}

		//重组订单数据
		$orderdata = array(
			'merchantId'			=>	$param['merchantId'],
			'timestamp'				=>	$param['timestamp'],
			'signatureMethod'		=>	$param['signatureMethod'],
			'signatureVersion'		=>	$param['signatureVersion'],
			'jUserIp'				=> 	$param['jUserIp'],
			'jExtra'				=>	$param['jExtra'],
			'orderType'				=>	$param['orderType'],
			'payWay'				=>	$param['payWay'],
			'amount'				=>	$param['amount'],
			'currency'				=>	$param['currency'],
			'jUserId'				=>	$param['jUserId'],
			'notifyUrl'				=>	$param['notifyUrl'],
			'jOrderId'				=>	$param['jOrderId'],
		);
	
		//签名
		ksort($orderdata);

		$orderstr = http_build_query($orderdata);
		
		$sha256str  =  hash_hmac('sha256', $orderstr, $merchantdata['merchantkey']);
		
		//判断签名
		if($sha256str != $param['signature']){
			$data['code']		= 410;
			$data['message']	= '参数错误';
			return $data;
		}

		$order_number = 'D'.trading_number();
		$trade_number = 'L'.trading_number();
		//进库 数据重组
		$total_data = array(
			'juid'				=>	$orderdata['jUserId'],//订单uID
			'mid'				=>	$merchantdata['id'],//接入商户id
			'orderid'			=>	$order_number,//订单编号
			'tid'				=>	$trade_number,//流水号
			'status'			=>	1,
			'jorderid'			=>	$orderdata['jOrderId'],//商户订单编号
			'jip'				=>  $orderdata['jUserIp'],//商户的客户IP
			'ordertype'			=>  $orderdata['orderType'],// 订单类型 1=充值订单;2=提现订单
			'timestamp'			=>  $orderdata['timestamp'],// 商户请求时间
			'ordertimes'		=>  match_msectime(),// 订单时间
			'payway'			=>	$orderdata['payWay'],// 支付方式;AliPay(支付宝);WechatPay(微信)
			'oamount'			=>	$orderdata['amount'],//  订单金额
			'currency'			=>	$orderdata['currency'],//  支付货币类型
			'notifyurl'			=>	base64_encode($orderdata['notifyUrl']),//后台通知回调URL
			'jextra'			=>	base64_encode($orderdata['jExtra']),//后台通知回调URL
			'signature'			=>	$param['currency'],//  签名
			'mkey'				=>	$merchantdata['merchantkey'],
			'bitype'			=>	2,//生成卖比订单
			'clienttype'		=>	2,
			'orderip'			=>	request()->ip(),//客户端IP
		);

		$is_insert = $this->insertGetId($total_data);
		//进数据库
		if(!$is_insert){
			$data['code']		= 501;
			$data['message']	= '内部服务错误';
			return $data;
		}

		$ApiOrderModel = model('api/Order');
		//获取未接的订单 推送到抢单大厅
		$ApiOrderModel->pushNewOrder();
		
		$data['code']			= 0;
		$data['message']		= '成功';
		$data['signature']		= $sha256str;

		$payment = '&orderid='.$total_data['orderid'].'&amount='.$total_data['oamount'];

		$isHttps = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')) ? 'https' : 'http';	// 获取传输协议
		$returndata = array(
			'orderId'			=>	$total_data['orderid'],//商户必须在创建订单
			'orderType'			=>	$total_data['ordertype'],//订单类型 1：充值订单
			'paymentUrl'		=>	$isHttps.'://'.$_SERVER['HTTP_HOST'].'/#/paymentTips?payway='.$total_data['payway'].'&payment='.base64_encode($payment),//支付页面 这个很重要，受理成功后，需要打开这个地址，让客户进行后续操作
		);
		$data['data']	=	$returndata;
		
		return $data;
		
	}
	//第三方get访问
	//支付平台提交
	//支付平台创建订单
	public function getCreateOrder(){
		
		//非Post提交
		if(!request()->isPost()){
			$data	= [
				'code'		=> 401,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//status           订单状态;1=已创建,2=已支付,3=完成,4=取消 5过期 6 锁定 7 待支付

		$param			= input('param.');
		
		$payment		=  (isset($param['payment']) && $param['payment']) ? $param['payment'] : '';
		
		if(!$payment){
			$data	= [
				'code'		=> 401,
				'message'	=> '参数错误'
			];
			return $data;
		}

		$cacheIp = request()->ip();//客户端IP
		
		if (!is_null(cache($cacheIp.'_'.$cacheIp)) && time() - cache($cacheIp.'_'.$cacheIp) < 5 ){
			$data	= [
				'code'		=> 411,
				'message'	=> '订单提交频繁！'
			];
			return $data;
		}
		cache($cacheIp.'_'.$cacheIp, time());
		
		//解码 解密
		$paymentsrt = base64_decode($payment);
		$paramstr  = auth_code($paymentsrt,'DECODE');
		@parse_str($paramstr,$param);
			
			

		if(!isset($param['jUserIp']) or !isset($param['orderType']) or !isset($param['payWay']) or !isset($param['amount']) or !isset($param['currency']) or !isset($param['jUserId'])  or !isset($param['notifyUrl']) or !isset($param['orderType']) or !isset($param['signatureVersion']) or !isset($param['merchantId']) or !isset($param['timestamp']) or !isset($param['jOrderId']) or !isset($param['signature'])){
			$data	= [
				'code'		=> 402,
				'message'	=> '参数错误'
			];
			return $data;
		}

		if(!$param['jUserIp'] or !$param['orderType'] or !$param['payWay'] or !$param['amount'] or !$param['currency'] or !$param['jUserId']  or !$param['notifyUrl'] or !$param['orderType'] or !$param['signatureVersion'] or !$param['merchantId'] or !$param['timestamp'] or !$param['jOrderId']){
			$data	= [
				'code'		=> 402,
				'message'	=> '参数错误'
			];
			return $data;
		}

		//订单类型
		if($param['orderType']!=1){
			$data	= [
				'code'		=> 403,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//支付方式
		$payway = array('AliPay','WechatPay','BankPay');
		if(!in_array($param['payWay'],$payway)){
			$data	= [
				'code'		=> 404,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		//订单金额
		if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $param['amount'])) {
			$data	= [
				'code'		=> 405,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		// 商户信息
		$merchantdata = model('Merchant')->field('id,merchantkey,order_alipay_min,order_alipay_max,order_wechat_min,order_wechat_max,order_bank_min
,order_bank_max,alipay_state,wechat_state,bank_state')->where('merchantid',$param['merchantId'])->where('status',1)->where('is_lock',1)->findOrEmpty();

		if (!$merchantdata) return ['code'=>409,'message'=>'权限错误'];//商户是否存在

		switch($param['payWay']){
			case 'AliPay':
				$m_order_min	=	'order_alipay_min';
				$m_order_max	=	'order_alipay_max';
				$s_order_min	=	'm_alipay_recharge_min';
				$s_order_max	=	'm_alipay_recharge_max';
				if($merchantdata['alipay_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
			case 'WechatPay':
				$m_order_min	=	'order_wechat_min';
				$m_order_max	=	'order_wechat_max';
				$s_order_min	=	'm_wechat_recharge_min';
				$s_order_max	=	'm_wechat_recharge_max';
				if($merchantdata['wechat_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
			case 'BankPay':
				$m_order_min	=	'order_bank_min';
				$m_order_max	=	'order_bank_max';
				$s_order_min	=	'm_bank_recharge_min';
				$s_order_max	=	'm_bank_recharge_max';
				if($merchantdata['bank_state']==2) return ['code'=>409,'message'=>'权限错误'];//商户是否可以提交
			break;
		}


		if (!$merchantdata) return ['code'=>409,'message'=>'权限错误'];
		
		// 平台配置
		$settingdata =	model('Setting')->field('order_alipay_min,order_alipay_max,order_wechat_min,order_wechat_max,order_bank_min,order_bank_max,m_alipay_recharge_min,m_alipay_recharge_max,m_wechat_recharge_min,m_wechat_recharge_max,m_bank_recharge_min,m_bank_recharge_max')->where('id','>',0)->findOrEmpty();

		if ($merchantdata[$m_order_min]) {
			if ($param['amount'] < $merchantdata[$m_order_min]) return ['code'=>406,'message'=>'参数错误'];
		} else {
			if ($param['amount'] < $settingdata[$s_order_min]) return ['code'=>406,'message'=>'参数错误'];
		}
		if ($merchantdata[$m_order_max]) {
			if ($param['amount'] > $merchantdata[$m_order_max]) return ['code'=>406,'message'=>'参数错误'];
		} else {
			if ($param['amount'] > $settingdata[$s_order_max]) return ['code'=>406,'message'=>'参数错误'];
		}
		
		//支付货币类型
		$currency = array('CNY');
		if(!in_array($param['currency'],$currency)){
			$data	= [
				'code'		=> 407,
				'message'	=> '参数错误'
			];
			return $data;
		}
		//后台通知回调URL
		if((!preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', $param['notifyUrl']) && !preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', urldecode($param['notifyUrl']))) || !$param['notifyUrl']) {
			$data	= [
				'code'		=> 408,
				'message'	=> '参数错误'
			];
			return $data;
		}
		

		$shiwu = $this->where('mid','=',$merchantdata['id'])->where('juid','=',$param['jUserId'])->where('jip','=',$param['jUserIp'])->where('status','=',7)->where('ordertimes','>',match_msectime()-60*15*1000)->count();

		if($shiwu>4){
			$data	= [
				'code'		=> 413,
				'message'	=> '您有多笔订单未付款，请15分钟后再试！'
			];
			return $data;
		}
		
		$wu = $this->where('mid','=',$merchantdata['id'])->where('juid','=',$param['jUserId'])->where('jip','=',$param['jUserIp'])->where('status','=',7)->where('ordertimes','>',match_msectime()-60*5*1000)->count();
		if($wu>2){
			$data	= [
				'code'		=> 412,
				'message'	=> '您有多笔订单未付款，请5分钟后再试！'
			];
			return $data;
		}

		//重组订单数据
		$orderdata = array(
			'merchantId'			=>	$param['merchantId'],
			'timestamp'				=>	$param['timestamp'],
			'signatureMethod'		=>	$param['signatureMethod'],
			'signatureVersion'		=>	$param['signatureVersion'],
			'jUserIp'				=> 	$param['jUserIp'],
			'jExtra'				=>	$param['jExtra'],
			'orderType'				=>	$param['orderType'],
			'payWay'				=>	$param['payWay'],
			'amount'				=>	$param['amount'],
			'currency'				=>	$param['currency'],
			'jUserId'				=>	$param['jUserId'],
			'notifyUrl'				=>	$param['notifyUrl'],
			'jOrderId'				=>	$param['jOrderId'],
		);
	
		//签名
		ksort($orderdata);

		$orderstr = http_build_query($orderdata);
		
		$sha256str  =  hash_hmac('sha256', $orderstr, $merchantdata['merchantkey']);
		
		//判断签名
		if($sha256str != $param['signature']){
			$data['code']		= 410;
			$data['message']	= '参数错误';
			return $data;
		}

		$order_number = 'D'.trading_number();
		$trade_number = 'L'.trading_number();
		//进库 数据重组
		$total_data = array(
			'juid'				=>	$orderdata['jUserId'],//订单uID
			'mid'				=>	$merchantdata['id'],//接入商户id
			'orderid'			=>	$order_number,//订单编号
			'tid'				=>	$trade_number,//流水号
			'status'			=>	1,
			'jorderid'			=>	$orderdata['jOrderId'],//商户订单编号
			'jip'				=>  $orderdata['jUserIp'],//商户的客户IP
			'ordertype'			=>  $orderdata['orderType'],// 订单类型 1=充值订单;2=提现订单
			'timestamp'			=>  $orderdata['timestamp'],// 商户请求时间
			'ordertimes'		=>  match_msectime(),// 订单时间
			'payway'			=>	$orderdata['payWay'],// 支付方式;AliPay(支付宝);WechatPay(微信)
			'oamount'			=>	$orderdata['amount'],//  订单金额
			'currency'			=>	$orderdata['currency'],//  支付货币类型
			'notifyurl'			=>	base64_encode($orderdata['notifyUrl']),//后台通知回调URL
			'jextra'			=>	base64_encode($orderdata['jExtra']),//后台通知回调URL
			'signature'			=>	$param['currency'],//  签名
			'mkey'				=>	$merchantdata['merchantkey'],
			'bitype'			=>	2,//生成卖比订单
			'clienttype'		=>	2,
			'orderip'			=>	request()->ip(),//客户端IP
		);
		
		$is_insert = $this->insertGetId($total_data);
		//进数据库
		if(!$is_insert){
			$data['code']		= 501;
			$data['message']	= '内部服务错误';
			return $data;
		}

		$ApiOrderModel = model('api/Order');
		//获取未接的订单 推送到抢单大厅
		$ApiOrderModel->pushNewOrder();
		
		$data['code']			= 0;
		$data['message']		= '成功';
		$data['signature']		= $sha256str;

		$payment = '&orderid='.$total_data['orderid'].'&amount='.$total_data['oamount'];

		$isHttps = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')) ? 'https' : 'http';	// 获取传输协议
		$returndata = array(
			'orderId'			=>	$total_data['orderid'],//商户必须在创建订单
			'orderType'			=>	$total_data['ordertype'],//订单类型 1：充值订单
			'paymentUrl'		=>	$isHttps.'://'.$_SERVER['HTTP_HOST'].'/#/paymentTips?payway='.$total_data['payway'].'&payment='.base64_encode($payment),//支付页面 这个很重要，受理成功后，需要打开这个地址，让客户进行后续操作
		);
		$data['data']	=	$returndata;
		
		return $data;
	}

	//交易查询接口
	public function queryOrder(){

		//非Post提交
		if(!request()->isPost()){
			$data	= [
				'code'		=> 402,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		$param = input('param.');
		
		if(!$param['merchantId'] or !$param['timestamp'] or !$param['signatureMethod'] or !$param['signatureVersion'] or !$param['signature'] or !$param['orderId']){
			$data	= [
				'code'		=> 402,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		
		$merchantdata = model('Merchant')->field('merchantkey')->where('merchantid',$param['merchantId'])->where('status',1)->find();
		if(!$merchantdata){
			$data	= [
				'code'		=> 401,
				'message'	=> '权限错误'
			];
			return $data;
		}
		
		//重组订单数据
		$signaturedata = array(
			'merchantId'			=>	$param['merchantId'],
			'timestamp'				=>	$param['timestamp'],
			'signatureMethod'		=>	$param['signatureMethod'],
			'signatureVersion'		=>	$param['signatureVersion'],
			'orderId'				=>	$param['orderId'],
		);
		//签名
		ksort($signaturedata);

		$signature = http_build_query($signaturedata);
		
		$signaturestr  =  hash_hmac('sha256', $signature, $merchantdata['merchantkey']);
		
		//判断签名
		if($signaturestr != $param['signature']){
			$data['code']		= 402;
			$data['message']	= '参数错误';
			return $data;
		}
		
		$orderdata		=	$this->where('orderid',$param['orderId'])->where('bitype',2)->where('mid',$param['merchantId'])->find();
		if(!$orderdata){
			$data['code']		= 402;
			$data['message']	= '参数错误';
			return $data;
		}
		
		$data['code']					=	0;
		$data['message']				=	'ok';
		$qorderdata					= array(
			'orderId'					=>	$orderdata['orderid'],
			'jOrderId'					=>	$orderdata['jorderid'],
			'orderType'					=>	$orderdata['ordertype'],
			'currency'					=>	$orderdata['currency'],
			'amount'					=>	$orderdata['oamount'],
			'actualAmount'				=>	$orderdata['oactualamount'],
			'fee'						=>	$orderdata['feeamount'],
			'payWay'					=>	$orderdata['payway'],
			'jExtra'					=>	base64_decode($orderdata['jextra']),
			'status'					=>	($orderdata['status']==7 or $orderdata['status']==6) ? 1 : $orderdata['status'],
		);

		//签名
		ksort($qorderdata);

		$qorderstr 	= http_build_query($qorderdata);
		
		$signature  =  hash_hmac('sha256', $qorderstr, $merchantdata['merchantkey']);
		
		$qorderdata	['signature']	=	$signature;
		
		$data['data']				=	$qorderdata;	
		
		return	$data;  

	}
	
	//获取订单状态
	public function timingOrder(){
		//非Post提交
		if(!request()->isPost()){
			$data	= [
				'code'		=> 0,
				'message'	=> '参数错误'
			];
			return $data;
		}
		
		$param 						=  input('param.');
		
		$qrcodeurl					=	'';
		
		$orderdata		=	$this->field('payid,ordertimes')
						->where(array('orderid'=>$param['orderId']))
						->find();
		if($orderdata){
			//解锁 1
			$updatadata3 = array(
				'orderip'	=>	request()->ip(),
			);
			$this->where('orderid',$param['orderId'])->where('orderip','')->update($updatadata3);
		}

		$qrcodeurl		= '';
		$wutime			= 0;
		if($orderdata['payid']){
			$qrcodeurl  =	model('Qrcode')->where('id',$orderdata['payid'])->value('paywayurl');
		}
		
		$wutime		=	strtotime(match_msecdate($orderdata['ordertimes']))+300-time();
		
		$data = [
			'qrcodeurl'	=>	$qrcodeurl,
			'wutime'	=>	($wutime <= 0) ? 0 : $wutime,
		];
		
		
		return $data;
	}
	
	//定时执行
	public function expireOrder(){

		$expiretimes2	=	match_msectime()-1000*600;
		
		//过期 1
		$updatadata3 = array(
			'status'			=>	4,
			'message'			=>	'未支付,订单过期',
			'completetimes'		=>	time(),
		);

		
		//待付款的订单，如果30分钟无人付款，倒计时结束需要自动取消订单
		$selldata = $this->where('ordertimes','<',$expiretimes2)->where('status','=',7)->select()->toArray();
		
		//过期 取消卖比订单
		$updatadata = array(
			'state'			=>	4,
			'add_time'		=>	time(),
		);
		foreach($selldata as $key => $value){
			//订单表
			$isu = 0;
			$isut = 0;
			$isu = $this->where('id','=',$value['id'])->where('status','=',7)->update($updatadata3);
			if($isu){
				switch($value['bitype']){
					case 1://买
						$userTotaldata	=	array();
						$userTotaldata	= 	model('UserTotal')->where('uid' , $value['juid'])->find();
						//解冻 卖币人的 进流水 更新卖币状态
						Model('UserTransaction')->where('uid' , $value['juid'])->where('id','=',$value['jorderid'])->where('state','=',5)->where('bitype','=',2)->update($updatadata);
						
						$oamount		= 	0;
						$oamount	=	$value['oamount'] + Model('UserTransaction')->where('uid' , $value['juid'])->where('id','=',$value['jorderid'])->value('fee');
						$isut = model('UserTotal')->where('uid' , $value['juid'])->Dec('frozen_balance', $oamount)->update();
						
						if($isut){
							//取消
							$financial_data['uid'] 						= $value['juid'];
							$financial_data['order_number'] 			= $value['orderid'];
							$financial_data['trade_number'] 			= 'L'.trading_number();
							$financial_data['trade_type'] 				= 5;//卖币
							$financial_data['account_frozen_balance']	= $userTotaldata['frozen_balance'] - $oamount;//冻结金额
							$financial_data['trade_before_balance']		= $userTotaldata['balance'];
							$financial_data['trade_amount'] 			= $oamount;
							$financial_data['account_balance'] 			= $userTotaldata['balance'];
							$financial_data['remarks'] 					= '卖币订单,未支付交易过期';
							$financial_data['types'] 					= 1;
							$financial_data['front_type'] 				= 4;//解冻
							$financial_data['isdaily'] 					= 2;//不进每日
							model('TradeDetails')->tradeDetails($financial_data);
						}
					break;
					case 2://卖
						$userTotaldata	=	array();
						$userTotaldata	= 	model('UserTotal')->where('uid' , $value['uid'])->find();
						//解冻 卖币 进流水
						$isut = model('UserTotal')->where('uid' , $value['uid'])->Dec('frozen_balance', $value['oamount'])->update();
						if($isut){
							//取消
							$financial_data['uid'] 						= $value['uid'];
							$financial_data['order_number'] 			= $value['orderid'];
							$financial_data['trade_number'] 			= 'L'.trading_number();
							$financial_data['trade_type'] 				= 5;//卖币
							$financial_data['account_frozen_balance']	= $userTotaldata['frozen_balance'] - $value['oamount'];//冻结金额
							$financial_data['trade_before_balance']		= $userTotaldata['balance'];
							$financial_data['trade_amount'] 			= $value['oamount'];
							$financial_data['account_balance'] 			= $userTotaldata['balance'];
							$financial_data['remarks'] 					= '卖币订单,未支付交易过期';
							$financial_data['types'] 					= 1;
							$financial_data['front_type'] 				= 4;//解冻
							$financial_data['isdaily'] 					= 2;//不进每日
							model('TradeDetails')->tradeDetails($financial_data);
						}
					break;
				}
			}
		}
	}
}