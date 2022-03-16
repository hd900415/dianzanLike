<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\controller;

use app\admin\controller\Common;

class SurveyController extends CommonController{
	/**
	 * 空操作处理
	 */
	public function _empty(){
		return $this->index();
	}
	/**
	 * 订单明细
	 * @return [type] [description]
	 */
	public function index(){
		// 总资产
		$balance = model('MerchantTotal')->field('balance,frozen_balance')->where('uid', session('admin_userid'))->findOrEmpty();
		// 今日时间
		$todayStart = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$todayEnd   = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
		// 今日订单
		$orderList = model('Order')->where(array(['mid','=',session('admin_userid')],['ordertimes','>=',$todayStart],['ordertimes','<=',$todayEnd]))->select()->toArray();
		// 今日提现
		$withdrawalsList = model('MerchantWithdrawals')->where(array(['uid','=',session('admin_userid')],['time','>=',$todayStart],['time','<=',$todayEnd]))->select()->toArray();
		// 获取配置
		$transactionType = config('custom.transactionType');
		$orderStates     = config('custom.orderStates');
		// 统计
		$count = ['waitPay'=>0, 'waitSure'=>0, 'countRecharge'=>count($orderList), 'countRechargePrice'=>0, 'countWithdrawals'=>count($withdrawalsList), 'countWithdrawalsPrice'=>0];
		// 重新赋值
		foreach ($orderList as $key => &$value) {
			$value['statusStr']    = $orderStates[$value['status']];
			$value['ordertypeStr'] = $transactionType[$value['ordertype']];
			if ($value['status'] == 1) $count['waitPay']++;
			if ($value['status'] == 2) $count['waitSure']++;
			$count['countRechargePrice'] += $value['oamount'];
		}
		foreach ($withdrawalsList as $key => $value) {
			$count['countWithdrawalsPrice'] += $value['price'];
		}
		// 今日手续费
		$count['todayFee'] = model('MerchantDaily')->where(array(['uid','=',session('admin_userid')],['date','>=',$todayStart],['date','<=',$todayEnd]))->value('fee');
		// 进行中订单
		$resultData = model('Order')->where(array(
						['mid','=',session('admin_userid')],
						['ordertimes','>=',$todayStart],
						['ordertimes','<',$todayEnd]
					))->whereIn('status', [1,2])->order('ordertimes','desc')->paginate(15);

		foreach ($resultData as $key => &$value) {
			$value['statusStr']    = $orderStates[$value['status']];
			$value['ordertypeStr'] = $transactionType[$value['ordertype']];
		}

		return view('', [
			'balance'   =>	$balance,
			'count'     =>	$count,
			'orderList' =>	$resultData->toArray()['data'],
			'page'      =>	$resultData->render(), // 分页
		]);
	}
}