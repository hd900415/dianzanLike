<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\controller;

use app\admin\controller\Common;

class BaseinfoController extends CommonController{
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
		// 商户信息及资产
		$info = model('Merchant')->field('ly_merchant.*,merchant_total.balance,frozen_balance')->join('merchant_total','ly_merchant.id = merchant_total.uid')->where('ly_merchant.id', session('admin_userid'))->findOrEmpty();		
		// 商户银行
		$bank = model('MerchantBank')->where('mid', session('admin_userid'))->select()->toArray();
		// 系统设置
		$setting = model('Setting')->field('m_alipay_recharge_min,m_alipay_recharge_max,m_wechat_recharge_min,m_wechat_recharge_max,m_bank_recharge_min,m_bank_recharge_max,m_alipay_fee_max,m_wechat_fee_max,m_bank_fee_max')->where('id','>',0)->findOrEmpty();

		// 获取当前商户的直属商户费率
		$merInfo = model('Merchant')->field('id,alipay_fee,wechat_fee,bank_fee')->where('sid', session('admin_userid'))->select()->toArray();
		// 提取数据
		foreach ($merInfo as $key => $value) {
			$alipayFee[] = $value['alipay_fee'];
			$wechatFee[] = $value['wechat_fee'];
			$bankFee[]   = $value['bank_fee'];
		}
		sort($alipayFee);
		sort($wechatFee);
		sort($bankFee);
		$limit = [
			'alipay' => ['min' => $info['alipay_fee'] - $alipayFee[0], 'max' => $setting['m_alipay_fee_max'] - end($alipayFee)],
			'wechat' => ['min' => $info['wechat_fee'] - $wechatFee[0], 'max' => $setting['m_wechat_fee_max'] - end($wechatFee)],
			'bank'   => ['min' => $info['bank_fee'] - $bankFee[0], 'max' => $setting['m_bank_fee_max'] - end($bankFee)],
		];

		// 商户昨日数据
		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$yesterdayData = model('MerchantDaily')->where(array(['uid','=',$info['id']],['date','>=',$today-86400],['date','<=',$today]))->findOrEmpty();

		return view('', [
			'info'          => $info,
			'bank'          => $bank,
			'setting'       => $setting,
			'limit'         => $limit,
			'yesterdayData' => $yesterdayData,
		]);
	}

	/**
	 * 绑定银行卡
	 * @return [type] [description]
	 */
	public function bindBankcard(){
		if(request()->isAjax()) return model('MerchantBank')->bindBankcard();
		return view();
	}

	/**
	 * 绑定联系电话
	 * @return [type] [description]
	 */
	public function bindPhone(){
		if(request()->isAjax()) return model('Merchant')->bindInfo('phone');
		return view();
	}

	/**
	 * 费率修改
	 * @return [type] [description]
	 */
	public function feeEdit(){
		return model('Merchant')->feeEdit();
	}

	/**
	 * 修改密码
	 * @return [type] [description]
	 */
	public function editPwd(){
		if(request()->isAjax()) return model('Merchant')->editPwd();
		return view();
	}
	/**
	 * 设置交易密码
	 */
	public function setPayPwd(){
		if(request()->isAjax()) return model('Merchant')->setPayPwd();
		return view();
	}
}