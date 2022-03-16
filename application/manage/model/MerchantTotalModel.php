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

		//数据验证
		$validate = validate('app\manage\validate\Users');
		if(!$validate->scene('capital')->check([
			'artificialPrice'		=>	(isset($param['price'])) ? $param['price'] : '',
			'artificialType'		=>	(isset($param['transaction_type'])) ? $param['transaction_type'] : '',
			'artificialSafeCode'	=>	(isset($param['safe_code'])) ? $param['safe_code'] : '',
		])){
			return $validate->getError();
		}
		//获取操作前余额
		$balanceBefore = $this->field('balance,frozen_balance,username')->join('merchant','ly_merchant_total.uid=merchant.id','left')->where('ly_merchant_total.uid','=',$param['id'])->findOrEmpty();
		// 金额判断
		if ($param['transaction_type'] == 13 && $balanceBefore['balance'] - abs($param['price']) < 0) {
			return '操作金额不正确';
		} else if ($param['transaction_type'] == 14 && $balanceBefore['frozen_balance'] - abs($param['price']) < 0) {
			return '操作金额不正确';
		} else if ($balanceBefore['balance'] + $param['price'] < 0) {
			return '操作金额不正确';
		}
		//更新余额与统计
		switch ($param['transaction_type']) {
			// 冻结
			case 13:
				$res = $this->where('uid','=',$param['id'])->inc('frozen_balance',abs($param['price']))->dec('balance',abs($param['price']))->update();
				break;
			// 解冻
			case 14:
				$res = $this->where('uid','=',$param['id'])->inc('balance',abs($param['price']))->dec('frozen_balance',abs($param['price']))->update();
				break;
			
			default:
				$res = $this->where('uid','=',$param['id'])->inc('balance',$param['price'])->update();				
				break;
		}
		if(!$res) return '操作失败';
		//生成流水
		$orderNumber = 'C'.trading_number();
		$tradeNumber = 'L'.trading_number();
		if ($param['transaction_type'] == 13) { // 冻结
			$accountFrozenBbalance = $balanceBefore['frozen_balance'] + abs($param['price']);
		} else if ($param['transaction_type'] == 14) { // 解冻
			$accountFrozenBbalance = $balanceBefore['frozen_balance'] - abs($param['price']);
		} else {
			$accountFrozenBbalance = $balanceBefore['frozen_balance'];
		}
		$tradeDetails = array(
			'uid'                    =>	$param['id'],
			'order_number'           =>	$orderNumber,
			'trade_number'           =>	$tradeNumber,
			'trade_type'             =>	$param['transaction_type'],
			'trade_before_balance'   =>	$balanceBefore['balance'] + $balanceBefore['frozen_balance'],
			'trade_amount'           =>	$param['price'],
			'account_frozen_balance' => $accountFrozenBbalance,
			'account_balance'        =>	(in_array($param['transaction_type'], [13,14])) ? $balanceBefore['balance'] + $balanceBefore['frozen_balance'] : $balanceBefore['balance'] + $param['price'],
			'remarks'                =>	isset($param['explain']) && $param['explain'] ? $param['explain'] : '管理员操作',
			'types'                  =>	2,
		);
		if ($param['transaction_type'] == 13) $tradeDetails['front_type'] = 3;
		if ($param['transaction_type'] == 14) $tradeDetails['front_type'] = 4;
		if (in_array($param['transaction_type'], [13,14])) $tradeDetails['isdaily'] = 2;
		model('TradeDetails')->tradeDetails($tradeDetails);

		//添加操作日志
		$transactionType = config('custom.transactionType')[$param['transaction_type']];
		model('Actionlog')->actionLog(session('manage_username'),'操作用户名为'.$balanceBefore['username'].'的资金，金额：'.$param['price'].'，类型：'.$transactionType,1);

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