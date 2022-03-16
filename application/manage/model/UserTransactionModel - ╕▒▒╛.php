<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 对流水的相关操作
 */

namespace app\manage\model;

use think\Model;

class UserTransactionModel extends Model{
	//表名
	protected $table = 'ly_user_transaction';

	/**
	 * 资金流水列表
	 */
	public function transAction($bitype){
		$param = input('get.');

		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		$where[] = array('bitype','=',$bitype);

		//搜索类型
		if(isset($param['username']) && $param['username']){
			$uid = model('Users')->where('username', trim($param['username']))->value('id');
			$where[] = array('ly_user_transaction.uid','=',$uid);
			$pageParam['username'] = $param['username'];
		}
		//订单号
		if(isset($param['order_number']) && $param['order_number']){
			$where[] = array('ly_user_transaction.order_number','=',$param['order_number']);
			$pageParam['order_number'] = $param['order_number'];
		}
		//交易类型
		if(isset($param['bitype']) && $param['bitype']){
			$where[] = array('ly_user_transaction.bitype','=',$param['bitype']);
			$pageParam['bitype'] = $param['bitype'];
		}
		//交易类型
		if(isset($param['state']) && $param['state']){
			$where[] = array('ly_user_transaction.state','=',$param['state']);
			$pageParam['state'] = $param['state'];
		}
		//交易金额
		if(isset($param['price1']) && $param['price1']){
			$where[] = array('ly_user_transaction.amount','>=',$param['price1']);
			$pageParam['price1'] = $param['price1'];
		}
		//交易金额
		if(isset($param['price2']) && $param['price2']){
			$where[] = array('ly_user_transaction.amount','<=',$param['price2']);
			$pageParam['price2'] = $param['price2'];
		}
		//时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('ly_user_transaction.add_time','>=',strtotime($dateTime[0]));
			$where[] = array('ly_user_transaction.add_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('ly_user_transaction.add_time','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('ly_user_transaction.add_time','<=',$todayEnd);
		}

		//查询符合条件的数据
		$resultData = $this->field('ly_user_transaction.*,users.username')->join('Users','ly_user_transaction.uid = users.id')->where($where)->order(['add_time'=>'desc','id'=>'desc'])->paginate(15,false,['query'=>$pageParam]);
		//数据集转数组
		$pageTotal = ['amountCount'=>0,'feeCount'=>0];
		foreach ($resultData as $key => &$value) {
			$value['statestr']		= config('custom.transactionStatus')[$value['state']];
			$value['statusColor']	= config('custom.color')[$value['state']];
			$pageTotal['amountCount'] += $value['amount'];
			$pageTotal['feeCount']    += $value['fee'];
		}
		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',4],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);
	
		return array(
			'tradeList' =>	$resultData->toArray()['data'],
			'page'      =>	$resultData->render(),//分页
			'pageTotal' =>	$pageTotal,
			'where'     =>	$pageParam,
			'power'     =>	$power,
			'bitype'	=>	$bitype
		);
	}

	/**
	 * 交易详情
	 */
	public function transdateils(){
		
		$param = input('get.');
		$orderInfo = $this->field('ly_user_transaction.*,users.username')->join('users','ly_user_transaction.uid = users.id')->where('ly_user_transaction.id',$param['id'])->find();
		$orderInfo['rusername']	=	'';//收款人
		
		$orderInfo['zname']				=	'';//支付账户名
		$orderInfo['zbankname']			=	'';//银行
		$orderInfo['zcodenumder']		=	'';//账户
		
		$orderInfo['sname']				=	'';//收账户名
		$orderInfo['sbankname']			=	'';//银行
		$orderInfo['scodenumder']		=	'';//账户
		
		switch($orderInfo['bitype']){
			case 1://买
				//买的人
				$banbinfo = model('UserBank')->where('id',$orderInfo['ubankid'])->find();
				$orderInfo['zname']			= $banbinfo['name'];
				$orderInfo['zbankname']		= $banbinfo['bank_name'];
				$orderInfo['zcodenumder']	= $banbinfo['card_no'];
				
				//收款
				$sbanbinfo = model('Recaivables')->where('id',$orderInfo['rbankid'])->find();
				$orderInfo['sname']			= $sbanbinfo['name'];
				$orderInfo['sbankname']		= $sbanbinfo['bank'];
				$orderInfo['scodenumder']	= $sbanbinfo['account'];
			break;
			case 2://卖
				//收款
				$banbinfo = model('UserBank')->where('id',$orderInfo['ubankid'])->find();
				$orderInfo['sname']			= $banbinfo['name'];
				$orderInfo['sbankname']		= $banbinfo['bank_name'];
				$orderInfo['scodenumder']	= $banbinfo['card_no'];
				if($orderInfo['ruid']){
					//买的人
					$sbanbinfo = model('UserBank')->where('id',$orderInfo['rbankid'])->find();
					$orderInfo['zname']			= $sbanbinfo['name'];
					$orderInfo['zbankname']		= $sbanbinfo['bank_name'];
					$orderInfo['zcodenumder']	= $sbanbinfo['card_no'];
				}else{
					//买的人
					$sbanbinfo = model('Recaivables')->where('id',$orderInfo['rbankid'])->find();
					$orderInfo['zname']			= $sbanbinfo['name'];
					$orderInfo['zbankname']		= $sbanbinfo['bank'];
					$orderInfo['zcodenumder']	= $sbanbinfo['account'];
				}
			break;
		}

		$orderInfo['statestr'] = config('custom.transactionStatus')[$orderInfo['state']];

		return $orderInfo;
	}
	//交易订单操作
	public function operationtrans(){
		
		$param 		= input('post.');
		$orderid	=	$param['id'];
		$state		=	$param['state'];
		$orderInfo = $this->where('id',$param['id'])->find();
		switch($orderInfo['bitype']){
			case 1://买
				switch($state){
					case 2://已经付款放币
						//更新已经完成
						$updatadata = array(
							'state'					=>	1,
							'complete_time'		=>	time(),//完成的时间
						);
						
						$is = $this->where('state','=',2)->where('bitype','=',1)->where('id','=',$orderInfo['id'])->update($updatadata);
						if(!$is){
							return '业务错误';
						}
						
						//上分
						$userTotaldata	=	array();
						$userTotaldata	= 	model('UserTotal')->where('uid' , $orderInfo['uid'])->find();
						
						$isruf = model('UserTotal')->where('uid' , $orderInfo['uid'])->Inc('balance', $orderInfo['amount'] - $orderInfo['fee'])->update();
						if(!$isruf){
							return '业务错误';
						}
							
						
						//买家解冻流水
						$financial_data['uid'] 							= $orderInfo['uid'];
						$financial_data['order_number'] 				= $orderInfo['order_number'];
						$financial_data['trade_number'] 				= 'L'.trading_number();
						$financial_data['trade_type'] 					= 4;//买币
						$financial_data['account_frozen_balance']		= $userTotaldata['frozen_balance'];//冻结金额
						$financial_data['trade_before_balance']			= $userTotaldata['balance'];
						$financial_data['trade_amount'] 				= $orderInfo['amount']-$orderInfo['fee'];
						$financial_data['account_balance'] 				= $userTotaldata['balance'] + $orderInfo['amount']-$orderInfo['fee'];
						$financial_data['remarks'] 						= '买币订单交易成功';
						$financial_data['types'] 						= 1;
						$financial_data['front_type'] 					= 1;//转入
						model('TradeDetails')->tradeDetails($financial_data);//解冻流水
						return '交易成功';
						
					break;
				}
			break;
			case 2://卖
					
				switch($state){
					case 5://抢单
						$updatadata = array(
							'state'			=>	3,//待支付
							'aid'			=>	session('manage_userid'),
							'dispose_time'	=>	time(),//出来
						);
						
						$is = $this->where('state','=',5)->where('bitype','=',2)->where('id','=',$orderInfo['id'])->update($updatadata);
						if(!$is){
							return '接单失败';
						}
						return '接单成功';

					break;
					case 3://已支付
						$updatadata = array(
							'state'				=>	2,//已经付款
							'dispose_time'		=>	time(),//处理时间
						);
						
						$isupur = $this->where('state','=',3)->where('bitype','=',2)->where('id','=',$orderInfo['id'])->update($updatadata);
						if(!$isupur){
							return '失败';
						}
						
						return '成功';
					break;
					case 2://用户未按时放币，管理员操作放币！
						//确认收款
						$updatadata = array(
							'state'			=>	1,
							'complete_time'		=>	time(),
						);
						
						$is = $this->where('state','=',2)->where('bitype','=',2)->where('id',$orderid)->update($updatadata);
						if(!$is){
							return '失败';
						}
						
						$uid	=	$orderInfo['uid'];
		
						//卖家  转出 解冻
						$userTotaldata	=	array();
						$userTotaldata	= 	model('UserTotal')->where('uid' , $uid)->find();				
						
						$actualamount	=	$orderInfo['amount'] + $orderInfo['fee'];
						
						//卖家  转出 解冻
						$isuf = model('UserTotal')->where('uid' , $uid)->Dec('balance', $orderInfo['amount'])->Dec('frozen_balance', $actualamount)->update();
						if($isuf){
						
							//转出流水
							$financial_data['uid'] 						= $uid;
							$financial_data['order_number'] 			= $orderInfo['order_number'];
							$financial_data['trade_number'] 			= 'L'.trading_number();
							$financial_data['trade_type'] 				= 5;//卖币
							$financial_data['account_frozen_balance']	= $userTotaldata['frozen_balance'] - $actualamount;//冻结金额
							$financial_data['trade_before_balance']		= $userTotaldata['balance'];
							$financial_data['trade_amount'] 			= $orderInfo['amount'];//转出金额 
							$financial_data['account_balance'] 			= $userTotaldata['balance'] - $orderInfo['amount'];
							$financial_data['remarks'] 					= '用户未按时放币，管理员操作放币！';
							$financial_data['types'] 					= 1;
							$financial_data['front_type'] 				= 2;//转出
							$btid = model('TradeDetails')->tradeDetails($financial_data);//转出流水
							
							$userTotaldataf	= 	model('UserTotal')->where('uid' , $uid)->find();	
							model('UserTotal')->where('uid' , $uid)->Dec('balance', $orderInfo['fee'])->update();
							//转出费
							$financial_dataf['uid'] 					= $uid;
							$financial_dataf['order_number'] 			= $orderInfo['order_number'];
							$financial_dataf['trade_number'] 			= 'L'.trading_number();
							$financial_dataf['trade_type'] 				= 11;//费用
							$financial_dataf['account_frozen_balance']	= $userTotaldataf['frozen_balance'];//冻结金额
							$financial_dataf['trade_before_balance']	= $userTotaldataf['balance'];
							$financial_dataf['trade_amount'] 			= $orderInfo['fee'];//转出金额 
							$financial_dataf['account_balance'] 		= $userTotaldataf['balance'] - $orderInfo['fee'];
							$financial_dataf['remarks'] 				= '用户未按时放币，管理员操作放币！';
							$financial_dataf['types'] 					= 1;
							$financial_data['front_type'] 				= 2;//转出
							$fbtid = model('TradeDetails')->tradeDetails($financial_dataf);//转出流水
						}
		
						$today = mktime(0,0,0,date('m'),date('d'),date('Y'));
												
						Model('UserDaily')->where(array(['uid','=',$orderInfo['uid']],['date','=',$today]))->setInc('recovery',$orderInfo['amount']);
						return '成功';
																			 									
					break;
				}
			break;
		}
	}
}