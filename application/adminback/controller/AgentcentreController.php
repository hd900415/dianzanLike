<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\controller;

use app\admin\controller\Common;

class AgentcentreController extends CommonController{
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
		$data = model('Merchant')->agentCentre();

		return view('', [
			'data' => $data
		]);
	}

	/**
	 * 添加商户
	 * @return [type] [description]
	 */
	public function merAdd(){
		if (request()->isAjax()) return model('Merchant')->merAdd();

		// 获取当前账户费率
		$selfFee = model('Merchant')->field('alipay_fee,wechat_fee,bank_fee')->where('id', session('admin_userid'))->findOrEmpty();
		// 获取平台费率设置
		$setting = model('Setting')->field('m_alipay_fee_max,m_wechat_fee_max,m_bank_fee_max')->where('id','>',0)->findOrEmpty();

		return view('', [
			'merchantType' => config('custom.merchantType'),
			'selfFee'      => $selfFee,
			'setting'      => $setting,
		]);
	}

	/**
	 * 商户编辑
	 * @return [type] [description]
	 */
	public function merEdit(){
		if (request()->isAjax()) return model('Merchant')->merEdit();

		$param = input('get.');
		$fee = model('Merchant')->field('alipay_fee,wechat_fee,bank_fee')->where('id', $param['id'])->findOrEmpty();
		// 获取当前账户费率
		$selfFee = model('Merchant')->field('alipay_fee,wechat_fee,bank_fee')->where('id', session('admin_userid'))->findOrEmpty();
		// 获取平台费率设置
		$setting = model('Setting')->field('m_alipay_fee_max,m_wechat_fee_max,m_bank_fee_max')->where('id','>',0)->findOrEmpty();

		return view('', [
			'id'      => $param['id'],
			'data'    => $fee,
			'selfFee' => $selfFee,
			'setting' => $setting,
		]);
	}

	/**
	 * 商户详情
	 * @return [type] [description]
	 */
	public function merLook(){
		$param = input('get.');

		$dateTime = array(
			// 今日
			'today' => [
				'start' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
				'end'   => mktime(23, 59, 59, date('m'), date('d'), date('Y')),
			],
			// 昨日
			'yesterday' => [
				'start' => mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 86400,
				'end'   => mktime(23, 59, 59, date('m'), date('d'), date('Y')) - 86400,
			],
			// 近七日
			'sevenday' => [
				'start' => mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 86400 * 6,
				'end'   => mktime(23, 59, 59, date('m'), date('d'), date('Y')),
			],
			// 近一个月
			'month' => [
				'start' => mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 86400 * 30,
				'end'   => mktime(23, 59, 59, date('m'), date('d'), date('Y')),
			],
		);
		// 获取商户信息
		$merInfo = model('Merchant')->where(array(['id', '=', $param['id']],['sid','=',session('admin_userid')]))->find();

		$data = [];
		if ($merInfo) {
			// 获取其他信息			
			foreach ($dateTime as $key => $value) {				
				if ($merInfo['types'] == 1) {
					// 商户注册数
					$data[$key]['regNum'] = model('Merchant')
											->join('merchant_team','ly_merchant.id = merchant_team.team')
											->where(array(
												['merchant_team.uid','=',$merInfo['id']],
												['ly_merchant.reg_time','>=',$value['start']],
												['ly_merchant.reg_time','<=',$value['end']]
											))->count();
				}
				// 商户总流水
				$data[$key]['details'] = model('TradeDetails')->where(array(
																	['types','=',2],
																	['state','=',1],
																	['uid','=',$merInfo['id']],
																	['trade_time','>=',$value['start']],
																	['trade_time','<=',$value['end']]
																))->sum('trade_amount');
			}
		}		

		return view('', [
			'merInfo' => $merInfo,
			'data'    => $data,
		]);
	}
}