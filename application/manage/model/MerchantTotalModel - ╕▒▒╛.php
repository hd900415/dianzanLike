<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 */

namespace app\manage\model;

use think\Model;

class MerchantTotalModel extends Model{
	//表名
	protected $table = 'ly_merchant_total';	

	/**
	 * 用户资金
	 */
	public function capital(){
		$param = input('post.');//获取参数
		if(!$param) return '非法提交';
		//参数过滤
		$array = array_filter($param);

		//数据验证
		$validate = validate('app\manage\validate\Users');
		if(!$validate->scene('capital')->check([
			'artificialPrice'		=>	(isset($array['price'])) ? $array['price'] : '',
			'artificialType'		=>	(isset($array['transaction_type'])) ? $array['transaction_type'] : '',
			'artificialSafeCode'	=>	(isset($array['safe_code'])) ? $array['safe_code'] : '',
		])){
			return $validate->getError();
		}
		//获取操作前余额
		$balanceBefore = $this->field('balance,frozen_balance,username')->join('merchant','ly_merchant_total.uid=merchant.id','left')->where('ly_merchant_total.uid','=',$array['id'])->find();
		if($balanceBefore['balance'] + $array['price'] < 0) return '操作金额不正确';
		//更新余额与统计
		$res = $this->where('uid','=',$array['id'])->inc('balance',$array['price'])->update();
		if(!$res) return '操作失败';
		//生成流水
		$orderNumber = 'C'.trading_number();
		$tradeNumber = 'L'.trading_number();
		$remarks = 
		$tradeDetails = array(
			'uid'                    =>	$array['id'],
			'order_number'           =>	$orderNumber,
			'trade_number'           =>	$tradeNumber,
			'trade_type'             =>	$array['transaction_type'],
			'trade_before_balance'   =>	$balanceBefore['balance'],
			'trade_amount'           =>	$array['price'],
			'account_frozen_balance' => $balanceBefore['frozen_balance'],
			'account_balance'        =>	$balanceBefore['balance'] + $array['price'],
			'remarks'                =>	isset($array['explain']) && $array['explain'] ? $array['explain'] : '管理员操作',
			'types'                  =>	1,
		);
		model('TradeDetails')->tradeDetails($tradeDetails);
		if(!$res){
			$res = $this->where('uid','=',$array['id'])->dec('balance',$array['price'])->dec($userFundsType,$array['price'])->update();
			return '操作失败';
		}
		// switch ($array['transaction_type']) {
		// 	case 1:
		// 		//添加充值记录
		// 		$rechargeArray = [
		// 			'uid'			=>	$array['id'],
		// 			'order_number'	=>	$orderNumber,
		// 			'money'			=>	$array['price'],
		// 			'state'			=>	1,
		// 			'add_time'		=>	time(),
		// 			'aid'			=>	session('manage_userid'),
		// 			'dispose_time'	=>	time(),
		// 			'remarks'		=>	$remarks,
		// 		];
		// 		model('UserRecharge')->insertGetId($rechargeArray);
		// 		break;
		// 	case 2:
		// 		//添加提现记录
		// 		$withdrawalsModelArray = [
		// 			'uid'			=>	$array['id'],
		// 			'order_number'	=>	$orderNumber,
		// 			'price'			=>	abs($array['price']),
		// 			'time'			=>	time(),
		// 			'trade_number'	=>	$tradeNumber,
		// 			'examine'		=>	1,
		// 			'state'			=>	1,
		// 			'aid'			=>	session('manage_userid'),
		// 			'remarks'		=>	$remarks,
		// 			'set_time'		=>	time(),
		// 		];
		// 		model('UserWithdrawals')->insertGetId($withdrawalsModelArray);
		// 		break;
		// }

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'操作用户名为'.$balanceBefore['username'].'的资金，金额：'.abs($array['price']).'，类型：'.config('custom.transactionType')[$array['transaction_type']],1);

		return 1;
	}

	/**
	 * 资金视图
	 */
	public function capitalView(){
		$uid = input('get.id');//获取参数
		//获取用户月
		$balance = $this->field('balance')->where('uid','=',$uid)->find();

		return array(
			'id'		=>	$uid,
			'balance'	=>	$balance,
		);
	}
}