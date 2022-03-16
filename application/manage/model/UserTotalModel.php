<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 */

namespace app\manage\model;

use think\Model;

class UserTotalModel extends Model{
	//表名
	protected $table = 'ly_user_total';

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
		$balanceBefore = $this->field('balance,total_balance,username')->join('users','ly_user_total.uid=users.id','left')->where('ly_user_total.uid','=',$param['id'])->findOrEmpty();
		// 金额判断
		if ($balanceBefore['balance'] + $param['price'] < 0) return '操作金额不正确';
		//更新余额与统计
		$res = $this->where('uid',$param['id'])->inc('balance',$param['price'])->inc('total_balance',$param['price'])->update();
		if(!$res) return '操作失败';

		$orderNumber = 'C'.trading_number();
		$tradeNumber = 'L'.trading_number();

		switch ($param['transaction_type']) {
			case '1':
				$rechargeArray = [
					'uid'          => $param['id'],
					'order_number' => $orderNumber,
					'money'        => $param['price'],
					'state'        => 1,
					'add_time'     => time(),
					'aid'          => session('manage_userid'),
					'dispose_time' => time(),
					'remarks'      => $param['explain']
				];
				model('UserRecharge')->insert($rechargeArray);
				break;

			case '2':
				$rechargeArray = [
					'uid'          => $param['id'],
					'order_number' => $orderNumber,
					'price'        => $param['price'],
					'examine'      => 1,
					'state'        => 1,
					'time'         => time(),
					'set_time'     => time(),
					'aid'          => session('manage_userid'),
					'trade_number' => $tradeNumber,
					'remarks'      => $param['explain']
				];
				model('UserWithdrawals')->insert($rechargeArray);
				break;
		}

		//生成流水
		$tradeDetails = array(
			'uid'                   => $param['id'],
			'order_number'          => $orderNumber,
			'trade_number'          => $tradeNumber,
			'trade_type'            => $param['transaction_type'],
			'trade_before_balance'  => $balanceBefore['balance'],
			'trade_amount'          => $param['price'],
			'account_balance'       => $balanceBefore['balance'] + $param['price'],
			'account_total_balance' => $balanceBefore['total_balance'] + $param['price'],
			'remarks'               => isset($param['explain']) && $param['explain'] ? $param['explain'] : '管理员操作',
			'types'                 => 1,
		);
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