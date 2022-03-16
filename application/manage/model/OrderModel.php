<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class OrderModel extends Model{
	//表名
	protected $table = 'ly_order';

	/**
	 * 投注列表
	 */
	public function betList($bitype){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();


		$where[] = array('bitype','=',$bitype);

		// 订单编号
		if(isset($param['id']) && $param['id']){
			$where[] = array('id','=',$param['id']);
			$pageParam['id'] = $param['id'];
		}
		// 用户名
		if(isset($param['username']) && $param['username']){
			$userId = model('Users')->where('username', trim($param['username']))->value('id');
			$where[] = array('uid','=',$userId);
			$pageParam['username'] = $param['username'];
		}
		// 卖币用户
		if(isset($param['sellusername']) && $param['sellusername']){
			$userId = model('Users')->where('username', trim($param['sellusername']))->value('id');
			$where[] = array('juid','=',$userId);
			$pageParam['sellusername'] = $param['sellusername'];
		}
		// 商户名
		if(isset($param['merchantid']) && $param['merchantid']){
			$userId = model('Merchant')->where('username', trim($param['merchantid']))->value('id');
			$where[] = array('mid','=',$userId);
			$pageParam['merchantid'] = $param['merchantid'];
		}
		// 状态
		if(isset($param['state']) && $param['state']){
			$where[] = array('status','=',$param['state']);
			$pageParam['state'] = $param['state'];
		}
		// 支付方式
		if(isset($param['payway']) && $param['payway']){
			$where[] = array('payway','=',$param['payway']);
			$pageParam['payway'] = $param['payway'];
		}
		// 订单号
		if(isset($param['order_number']) && $param['order_number']){
			$where[] = array('orderid','=',trim($param['order_number']));
			$pageParam['order_number'] = $param['order_number'];
		}
		// 订单号
		if(isset($param['jorderid']) && $param['jorderid']){
			$where[] = array('jorderid','=',trim($param['jorderid']));
			$pageParam['jorderid'] = $param['jorderid'];
		}
		// 投注时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('ordertimes','>=',strtotime($dateTime[0]));
			$where[] = array('ordertimes','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('ordertimes','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('ordertimes','<=',$todayEnd);
		}

		//查询符合条件的数据
		$resultData = $this->where($where)->order(['ordertimes'=>'desc','id'=>'desc'])->paginate(15,false,['query'=>$pageParam]);
		//数据集转数组
		$betList = $resultData->toArray()['data'];
		/**
		 * 部分元素重新赋值
		 */
		$orderState      = config('custom.orderStates');//订单状态
		$transactionType = config('custom.transactionType');//交易类型
		$payway          = config('custom.payway');
		$orderColor      = config('manage.color');
		//分页统计
		$pageTotal = array('oamount'=>0, 'oactualamount'=>0, 'feeamount'=>0);
		foreach ($betList as $key => &$value) {
			$value['statusStr']         = $orderState[$value['status']];
			$value['statusColor']       = $orderColor[$value['status']];
			$value['ordertypeStr']      = $transactionType[$value['ordertype']];
			$value['paywayStr']         = $payway[$value['payway']];
			$pageTotal['oamount']       += $value['oamount'];
			$pageTotal['oactualamount'] += $value['oactualamount'];
			$pageTotal['feeamount']     += $value['feeamount'];
			if($value['bitype']==2){
				$value['mName']             = model('Merchant')->where('id', $value['mid'])->value('username');
			}else{
				$value['mName']             = model('Users')->where('id', $value['juid'])->value('username');
			}
			$value['uName']             = model('Users')->where('id', $value['uid'])->value('username');
		}
		//权限
		$power = model('ManageUserRole')->getUserPower(array(['uid','=',session('manage_userid')],['cid','=',4]));

		return array(
			'betList'         =>	$betList,
			'pageTotal'       =>	$pageTotal,
			'page'            =>	$resultData->render(),//分页
			'where'           =>	$pageParam,
			'orderState'      =>	$orderState,
			'transactionType' =>	$transactionType,
			'power'           =>	$power,
			'bitype'		  =>    $bitype,
		);
	}
	/**
	 * 订单详情
	 */
	public function orderDetails(){
		//获取参数
		$param                                       = input('get.');
		//获取订单数据
		$orderInfo                                   = $this->where('id',$param['id'])->find()->toArray();
		//获取用户信息
		if ($orderInfo['uid']) $orderInfo['uName']  = model('Users')->where('id', $orderInfo['uid'])->value('username');
		if ($orderInfo['mid']) $orderInfo['mName'] = model('Merchant')->where('id', $orderInfo['mid'])->value('username');
		//部分数据重新赋值
		$orderInfo['paywayStr']                      = config('custom.payway')[$orderInfo['payway']];
		$orderInfo['statusStr']                      = config('custom.orderStates')[$orderInfo['status']];
		$orderInfo['ordertypeStr']                   = config('custom.transactionType')[$orderInfo['ordertype']];
		
		return $orderInfo;
	}

	/**
	 * 订单删除
	 */
	public function orderDel(){
		//获取参数
		$param = input('post.');
		//获取彩种存放的表
		$table = model('PlayClass')->getOne([['class','=',$param['lottery_type']]],'bet_table');
		//修改订单状态
		$editRes = model($table)->where([['order_number','=',$param['order_number']],['state','=',1]])->setField('state',4);
		if(!$editRes) return '操作失败。ERR:1';
		$editRes2 = $this->where([['order_number','=',$param['order_number']],['state','=',1]])->setField('state',4);
		if(!$editRes2) return '操作失败。ERR:2';

		//获取订单信息
		$betInfo = model($table)->field('no,lottery_type,play,price,uid')->where([['order_number','=',$param['order_number']],['state','=',4]])->find();
		//获取用余额
		$userBalance = model('UserTotal')->field('balance')->where('uid',$betInfo['uid'])->find();
		//更新用户余额和撤单金额
		$updateRes = model('UserTotal')->where('uid',$betInfo['uid'])->inc('balance',$betInfo['price'])->inc('total_cancel',$betInfo['price'])->update();
		if(!$updateRes) return '操作失败。ERR:3';

		$tradeDetails = array(
			'uid'					=>	$betInfo['uid'],
			'order_number'			=>	$param['order_number'],
			'trade_type'			=>	4,
			'trade_before_balance'	=>	$userBalance['balance'],
			'trade_amount'			=>	$betInfo['price'],
			'account_balance'		=>	$userBalance['balance'] + $betInfo['price'],
			'remarks'				=>	'管理员删除订单',
			'isadmin'				=>	1,
		);
		model('TradeDetails')->tradeDetails($tradeDetails);
		model('Actionlog')->actionLog(session('manage_username'),'删除订单号为：'.$param['order_number'].'的投注订单',1);

		return 1;
	}
	
	//订单操作
	public function operationdan(){
		
		$param 		= input('post.');
		$orderid	=	$param['id'];
		$state		=	$param['state'];
		$orderdata = $this->where('id',$param['id'])->find();
		if(!$orderdata){
			return '操作失败';
		}
		switch($state){
			case 7://待支付 取消 解冻
				//交易金额 用户余额
				
				//未支付解冻
				$updatadata3 = array(
					'status'			=>	4,
					'message'			=>	'未支付解冻',
					'completetimes'		=>	time(),
				);
				
				$is = $this->where('id','=',$orderdata['id'])->where('status','=',7)->update($updatadata3);
				if(!$is){
					return '操作失败';
				}

				$userTotaldata	= 	model('UserTotal')->where('uid' , $orderdata['uid'])->find();
				
				$is_update_user_b = model('UserTotal')->where('uid',$orderdata['uid'])->Dec('frozen_balance', $orderdata['oamount'])->update();

				if(!$is_update_user_b){
					return '操作失败';
				}
				//解冻
				$financial_dataj['uid'] 					=	$orderdata['uid'];
				$financial_dataj['order_number'] 			=	$orderdata['orderid'];
				$financial_dataj['trade_number'] 			=	'L'.trading_number();
				$financial_dataj['trade_type'] 				=	3;
				$financial_dataj['account_frozen_balance']	=	$userTotaldata['frozen_balance'] - $orderdata['oamount'];//冻结金额
				$financial_dataj['trade_before_balance']	=	$userTotaldata['balance'];
				$financial_dataj['trade_amount'] 			=	$orderdata['oamount'];
				$financial_dataj['account_balance'] 		=	$userTotaldata['balance'];
				$financial_dataj['remarks'] 				=	'卖币订单交易成功';
				$financial_dataj['types'] 					=	1;//会员流水
				$financial_dataj['front_type'] 				=	4;//解冻
				$financial_dataj['isdaily'] 				=	2;//不进每日
				model('TradeDetails')->tradeDetails($financial_dataj);
				return '操作成功';
			break;
		}
	}

	/**
	 * 同IP投注统计
	 */
	public function betIpIntersect(){
		// 获取参数
		$param = input('get.');
		// 查询条件组装
		$where = array();
		// 分页参数组装
		$pageParam = array();

		// 时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('buy_time','>=',strtotime($dateTime[0]));
			$where[] = array('buy_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('buy_time','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('buy_time','<=',$todayEnd);
		}
		// 获取多账号投注IP
		$notIntersect = $this->where($where)->group('ip,username')->column('ip');
		$Intersect = array_unique(array_diff_assoc($notIntersect, array_unique($notIntersect)));
		// 查询符合条件的数据
		$resultData = $this->field('username,ip')->where($where)->whereIn('ip',$Intersect)->group('username,ip')->order('ip', 'desc')->paginate(15,false,['query'=>$pageParam]);
		// 数据集转数组
		$betIpIntersect = $resultData->toArray()['data'];

		return array(
			'betIpIntersect' =>	$betIpIntersect,
			'page'           =>	$resultData->render(),//分页
			'where'          =>	$pageParam,
		);
	}

	/**
	 * 抢单会员
	 * @return [type] [description]
	 */
	public function robOrderUser(){
		// 获取参数
		$param = input('get.');
		// 查询条件组装
		$where = array();
		// 分页参数
		$pageUrl = "";

		// 用户名
		if(isset($param['username']) && $param['username']){
			$userId = model('Users')->where('username', trim($param['username']))->value('id');
			$pageUrl .= '&username='.$param['username'];
		}

		/**
		 *====这个步骤是必须的====
		 *这里填写Register服务的ip和Register端口，注意端口不是gateway端口
		 *ip不能是0.0.0.0，端口在start_register.php中可以找到
		 *这里假设GatewayClient和Register服务都在一台服务器上，ip填写127.0.0.1。
		 *如果不在一台服务器则填写真实的Register服务的内网ip(或者外网ip)
		 **/
		Gateway::$registerAddress = '127.0.0.1:1238';
		$robOrderUserArr = Gateway::getUidListByGroup('sell');
		
		// 今日时间
		$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$where[] = array('ordertimes','>=',$todayStart * 1000);			// 精确到毫秒
		$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
		$where[] = array('ordertimes','<=',$todayEnd * 1000 + 999);		// 精确到毫秒

		$data = array();
		// 获取其他信息
		foreach ($robOrderUserArr as $key => $value) {
			// 搜索用户
			if ((isset($userId) && $userId) && $value != $userId) unset($robOrderUserArr[$key]);

			$userInfo = model('Users')->field('username,uid')->where('id', $value)->findOrEmpty();
			$data[$value]['uid']      = $userInfo['uid'];
			$data[$value]['username'] = $userInfo['username'];
			$data[$value]['id']       = $value;
			// 今日订单数据
			$orderInfo = model('Order')->field('status,oactualamount')->where($where)->where('uid', $value)->select()->toArray();
			// 抢单笔数
			$data[$value]['countOrder']  = count($orderInfo);

			$data[$value]['countStatus'] = 0; // 成功笔数
			$data[$value]['countPrice']  = 0; // 抢单金额
			foreach ($orderInfo as $key2 => $value2) {
				$data[$value]['countPrice'] += $value2['oactualamount'];
				if ($value2['status'] == 2 || $value2['status'] == 3) $data[$value]['countStatus']++;
			}
			// 抢单收益
			$data[$value]['giveback'] = model('UserDaily')->where(['uid'=>$value, 'date'=>$todayStart])->value('giveback');
		}

		//分页
		$pageNum  = isset($param['page']) && $param['page'] ? $param['page'] : 1 ;
		$pageInfo = model('ArrPage')->page($data, 15, $pageNum, $pageUrl);
		$page     = $pageInfo['links'];
		$source   = $pageInfo['source'];

		return array(
			'data'  =>	$source,
			'page'  =>	$page,
			'where' =>	$param,
		);
	}
	
	//补单操作
	public function repairorder(){
		$param 		= input('post.');
		$orderdata = $this->where('id',$param['id'])->find();
		
		if(!$orderdata){
			return '操作失败';
		}
		
/*		if($param['oactualamount']>$orderdata['oamount']){
			return '请填写正确的补单金额';
		}
*/		
		$uid 		= $orderdata['uid'];
		$orderid	= $orderdata['orderid'];
		
		$merchantdata	= model('Merchant')->field('ly_merchant_total.balance,ly_merchant_total.frozen_balance,ly_merchant.*')
				->join('ly_merchant_total','ly_merchant.id=ly_merchant_total.uid')
				->where(array('ly_merchant.id'=>$orderdata['mid']))
				->find();	
		
		//获取用户的 商家的返点
		$usersdata 		=	model('Users')->field('ly_user_total.balance,ly_user_total.frozen_balance,ly_users.*')
				->join('ly_user_total','ly_users.id=ly_user_total.uid')
				->where(array('ly_users.id'=>$uid))
				->find();
				
		if($usersdata['balance']- $usersdata['frozen_balance'] < $param['oactualamount']){
			return '余额不足！';
		}
		//补单金额
		$price			=	$param['oactualamount'];
		
		//费率
		switch($orderdata['payway']){
			case 'AliPay'://支付宝
				$upayway		=   $usersdata['alipay_fee'];
				$mpayway		=	$merchantdata['alipay_fee'];
				$payway			=	'alipay_fee';
			break;
			case 'WechatPay'://微信
				$upayway		=   $usersdata['wechat_fee'];
				$mpayway		=	$merchantdata['wechat_fee'];
				$payway			=	'wechat_fee';
			break;
			case 'BankPay'://银行
				$upayway		=   $usersdata['bank_fee'];
				$mpayway		=	$merchantdata['bank_fee'];
				$payway			=	'bank_fee';
			break;
		}
		
		//商户费用
		$feeamount 		= $price * $mpayway/100;
		//订单收益返还
		$rebateamount 	= $price * $upayway/100;
		if($price<100){
			$rebateamount 	= 100 * $upayway/100;
		}
		
		
		//更新实际支付金额
		$updatadata7 = array(
			'status'			=>	2,//已支付
			'oactualamount'		=>	$price,//实际支付金额
			'feeratio'			=>	$mpayway,
			'feeamount'			=>	$feeamount,
			'rebateratio'		=>	$upayway,
			'rebateamount'		=>	$rebateamount,
			'paytimes'			=>	match_msectime(),//完成时间\
			'message'			=>	'补单成功',
		);
		$isoru = $this->where('orderid',$orderid)->where('uid',$uid)->where('status',4)->update($updatadata7);

		if(!$isoru){
			return '业务错误';
		}
		
		//交易金额 用户余额
		$userTotaldata	= 	model('UserTotal')->where('uid' , $uid)->find();
		
		$is_update_user_b = model('UserTotal')->where('uid',$uid)->Dec('balance', $orderdata['oamount'])->update();
		
		if($is_update_user_b){
			//卖币订单交易成功 生成流水
			$financial_data['uid'] 						= $uid;
			$financial_data['order_number'] 			= $orderdata['orderid'];
			$financial_data['trade_number'] 			= 'L'.trading_number();
			$financial_data['trade_type'] 				= 3;//订单
			$financial_data['account_frozen_balance']	= model('UserTotal')->where('uid' , $uid)->value('frozen_balance');//冻结金额
			$financial_data['trade_before_balance']		= $userTotaldata['balance'];
			$financial_data['trade_amount'] 			= $price;
			$financial_data['account_balance'] 			= $userTotaldata['balance'] - $price;
			$financial_data['remarks'] 					= '卖币订单交易成功,'.session('manage_username').'管理员操作补单成功';
			$financial_data['types'] 					= 1;
			$financial_data['payway'] 					= $orderdata['payway'];
			model('common/TradeDetails')->tradeDetails($financial_data);
		}
		
		//订单收益返还
		if($rebateamount>0){
			$userTotaldata_f	= 	model('UserTotal')->where('uid' , $uid)->find();
			$is_update_user_f = model('UserTotal')->where('uid',$uid)->Inc('balance', $rebateamount)->update();
			if($is_update_user_f){
				//订单收益返还 生成流水
				$financial_dataf['uid'] 					= $uid;
				$financial_dataf['order_number'] 			= $orderdata['orderid'];
				$financial_dataf['trade_number'] 			= 'L'.trading_number();
				$financial_dataf['trade_type'] 				= 12;//返还
				$financial_dataf['account_frozen_balance']	= model('UserTotal')->where('uid' , $uid)->value('frozen_balance');//冻结金额
				$financial_dataf['trade_before_balance']	= $userTotaldata_f['balance'];
				$financial_dataf['trade_amount'] 			= $rebateamount;
				$financial_dataf['account_balance'] 		= $userTotaldata_f['balance'] + $rebateamount;
				$financial_dataf['remarks'] 				= '订单收益返还,'.session('manage_username').'管理员操作补单成功';
				$financial_dataf['payway'] 					= $orderdata['payway'];
				$financial_dataf['types'] 					= 1;
				model('common/TradeDetails')->tradeDetails($financial_dataf);
			}
		}
		
		//商户余额
		$merchantbalance = model('MerchantTotal')->where('uid' , $merchantdata['id'])->value('balance');
		
		$is_update_m_b = model('MerchantTotal')->where('uid',$merchantdata['id'])->Inc('total_order', $price)->Inc('balance', $price)->update();
		if($is_update_m_b){
		
			//商户订单流水
			$financial_datam['uid'] 					= $merchantdata['id'];
			$financial_datam['username'] 				= $merchantdata['username'];
			$financial_datam['order_number'] 			= $orderdata['orderid'];
			$financial_datam['trade_number'] 			= 'L'.trading_number();
			$financial_datam['trade_type'] 				= 3;//转入
			$financial_datam['account_frozen_balance']	= 0;
			$financial_datam['trade_before_balance']	= $merchantbalance;
			$financial_datam['trade_amount'] 			= $price;
			$financial_datam['account_balance'] 		= $merchantbalance + $price;
			$financial_datam['remarks'] 				= '补单成功,卖币订单交易成功';
			$financial_datam['types'] 					= 2;
			$financial_datam['payway'] 					= $orderdata['payway'];
			model('common/TradeDetails')->tradeDetails($financial_datam);
		}
		
		if($feeamount>0){
			//商户费用
			$merchantbalancem	= model('MerchantTotal')->where('uid' , $merchantdata['id'])->value('balance');
			$isupdateMF  = model('MerchantTotal')->where('uid',$merchantdata['id'])->Dec('balance', $feeamount)->update();
			if($isupdateMF){
				//商户费用流水
				$financial_datamf['uid'] 					= $merchantdata['id'];
				$financial_datamf['username'] 				= $merchantdata['username'];
				$financial_datamf['order_number'] 			= $orderdata['orderid'];
				$financial_datamf['trade_number'] 			= 'L'.trading_number();
				$financial_datamf['account_frozen_balance']	= 0;
				$financial_datamf['trade_type'] 			= 11;//转出
				$financial_datamf['trade_before_balance']	= $merchantbalancem;
				$financial_datamf['trade_amount'] 			= $feeamount;
				$financial_datamf['account_balance'] 		= $merchantbalancem - $feeamount;
				$financial_datamf['remarks'] 				= '补单成功,平台手续费';
				$financial_datamf['types'] 					= 2;
				$financial_datamf['payway'] 				= $orderdata['payway'];
				model('common/TradeDetails')->tradeDetails($financial_datamf);
			}
		}

		//用户返点
		if($usersdata['sid']){
			$rakebackdata = array(
				'order_number'	=> $orderdata['orderid'],//订单号
				'order_price'	=> $price,//交易金额
				'payway'		=> $payway,//是否方式
				'fee_lv'		=> $upayway,//商户的费率
				'id'			=> $usersdata['sid'],//上级商户id
				'raketype'		=> 1,//返点类型 1会员2商户
			);
			model('api/Order')->rakeback($rakebackdata);
		}
		
		//上级商户返点
		if($merchantdata['sid']){
			$rakebackdata = array(
				'order_number'	=> $orderdata['orderid'],//订单号
				'order_price'	=> $price,//交易金额
				'payway'		=> $payway,//是否方式
				'fee_lv'		=> $mpayway,//商户的费率
				'id'			=> $merchantdata['sid'],//上级商户id
				'raketype'		=> 2,//返点类型 1会员2商户
			);
			model('api/Order')->rakeback($rakebackdata);
		}
		
		return 1;
	}

	/**
	 * 上传合同
	 * @return [type] [description]
	 */
	public function investPact(){
		$param = input('post.','','trim');//htmlspecialchars,strip_tags,trim
		if (!$param) return '提交失败';
		
		//数据验证
		// $validate = validate('app\manage\validate\Base');
		// if (!$validate->scene('noticeadd')->check($param)) return $validate->getError();
		
		$id = $param['id'];
		unset($param['id']);

		$res = $this->allowField(true)->save($param, ['id'=>$id]);
		if(!$res) return '修改失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'修改了投资合同',1);

		return 1;
	}
}