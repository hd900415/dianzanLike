<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 作用：生成操作日志
 */

namespace app\admin\model;

use think\Model;

class MerchantWithdrawalsModel extends Model{
	//表名
	protected $table = 'ly_merchant_withdrawals';

	/**
	 * 历史明细
	 * @return [type] [description]
	 */
	public function index(){
		$param = input('get.');
		// 查询条件组装
		$where[] = ['uid', '=', session('admin_userid')];
		// 分页参数组装
		$pageParam = array();
		// 订单号
		if(isset($param['order_id']) && $param['order_id']){
			$where[] = array('order_number','=',$param['order_id']);
			$pageParam['order_id'] = $param['order_id'];
		}
		// 状态
		if(isset($param['status']) && $param['status']){
			$where[] = array('state','=',$param['status']);
			$pageParam['status'] = $param['status'];
		}
		// 时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('time','>=',strtotime($dateTime[0]));
			$where[] = array('time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		} else {
			$where[] = array('time','>=',mktime(0,0,0,date('m'),date('d'),date('Y')));
			$where[] = array('time','<=',mktime(23,59,59,date('m'),date('d'),date('Y')));
		}
		// 查询符合条件的数据
		$resultData = $this->where($where)->order('time','desc')->paginate(15,false,['query'=>$pageParam]);
		// 数据集转数组
		$data = $resultData->toArray()['data'];
		// 提现状态
		$withdrawStatus = config('custom.withdrawalsState');
		// 重新组装
		foreach ($data as $key => &$value) {
			$value['statusStr'] = $withdrawStatus[$value['state']];
		}
		// 商户余额
		$balance = model('MerchantTotal')->field('balance,frozen_balance')->where('uid', session('admin_userid'))->find();
		// 商户银行卡
		$bankList = model('MerchantBank')->where('mid', session('admin_userid'))->select()->toArray();		

		return array(
			'where'          =>	$pageParam,
			'data'           =>	$data, // 数据
			'page'           =>	$resultData->render(), // 分页
			'balance'        =>	$balance,
			'bankList'       =>	$bankList, // 数据
			'withdrawStatus' =>	$withdrawStatus,
		);
	}

	/**
	 * 商户提现
	 * @return [type] [description]
	 */
	public function withdrawSub(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');

		$validate = validate('app\admin\validate\Withdraw');
		if(!$validate->scene('withdrawSub')->check($param)) return $validate->getError();
		// 获取商户信息
		$merchantInfo = model('Merchant')
						->field('ly_merchant.cash_fee,merchant_total.balance,merchant_total.frozen_balance')
						->join('merchant_total','ly_merchant.id=merchant_total.uid')
						->where('ly_merchant.id', session('admin_userid'))->find();

		// 获取商户银行卡信息
		$cardInfo = model('MerchantBank')->where('id', $param['card'])->find()->toArray();

		$orderNumber = 'D'.trading_number();
		$tradeNumber = 'L'.trading_number();
		$insertData = array(
			'uid'          => $cardInfo['mid'],
			'order_number' => $orderNumber,
			'card_number'  => $cardInfo['card_number'],
			'card_name'    => $cardInfo['card_name'],
			'bank_id'      => $cardInfo['bank_name'],
			'price'        => $param['price'],
			'time'         => time(),
			'trade_number' => $tradeNumber,
			'fee'          => $merchantInfo['cash_fee']
		);
		$res = $this->insertGetId($insertData);
		if (!$res) return '提交失败，请重新尝试';

		// 冻结金额
		$res2 = model('MerchantTotal')
				->where('uid', $cardInfo['mid'])
				->dec('balance', $param['price'] + $merchantInfo['cash_fee'])
				->inc('frozen_balance', $param['price'] + $merchantInfo['cash_fee'])
				->update();
		if (!$res2) {
			$this->where('id', $res)->delete();
			return '提交失败，请重新尝试';
		}
		
		// 生成流水
		$tradeDetailsArray = array(
			'uid'                    =>	$cardInfo['mid'],
			'types'                  => 2,
			'order_number'           =>	$orderNumber,
			'trade_type'             =>	2,
			'trade_amount'           =>	$param['price'],
			'trade_before_balance'   =>	$merchantInfo['balance'],
			'account_balance'        =>	$merchantInfo['balance'] - $param['price'],
			'account_frozen_balance' => $merchantInfo['frozen_balance'] + $param['price'],
			'isdaily'                => 2
		);
		$res3 = model('common/TradeDetails')->tradeDetails($tradeDetailsArray);
		if (!$res3) {
			$this->where('id', $res)->delete();
			model('MerchantTotal')->where('uid', $cardInfo['mid'])->inc('balance', $param['price'])->dec('frozen_balance', $param['price'])->update();
			return '提交失败，请重新尝试';
		}

		//添加操作日志
		model('Actionlog')->actionLog(session('admin_username'),'申请提现，金额：'.$param['price'],3);

		return 1;
	}
}