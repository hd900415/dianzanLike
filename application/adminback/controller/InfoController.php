<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\controller;

use app\admin\controller\Common;

class InfoController extends CommonController{
	/**
	 * 空操作处理
	 */
	public function _empty(){
		return $this->index();
	}
	/**
	 * 平台信息
	 * @return [type] [description]
	 */
	public function index(){
		$info    = model('Merchant')->where('id', session('admin_userid'))->find()->toArray();
		$bank    = model('MerchantBank')->where('mid', session('admin_userid'))->select()->toArray();
		$setting = model('Setting')->field('m_alipay_recharge_min,m_alipay_recharge_max,m_wechat_recharge_min,m_wechat_recharge_max,m_bank_recharge_min,m_bank_recharge_max')->where('id','>',0)->find();

		return view('', [
			'info'    => $info,
			'bank'    => $bank,
			'setting' => $setting,
		]);
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
	 * 绑定邮箱
	 * @return [type] [description]
	 */
	public function bindMail(){
		if(request()->isAjax()) return model('Merchant')->bindInfo('mail');
		return view();
	}
	/**
	 * 生成APIKEY
	 */
	public function setApiKey(){
		return model('Merchant')->setApiKey();
	}
	/**
	 * 查看APIKEY
	 * @return [type] [description]
	 */
	public function lookApiKey(){
		if(!request()->isAjax()) return '非法提交';
		$apiKey = model('Merchant')->where('id', session('admin_userid'))->value('merchantkey');
		return $apiKey;
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

	/**
	 * 绑定银行卡
	 * @return [type] [description]
	 */
	public function bindBankcard(){
		if(request()->isAjax()) return model('MerchantBank')->bindBankcard();
		return view();
	}

	/**
	 * 资质认证
	 * @return [type] [description]
	 */
	public function verifySub(){
		return model('merchant')->verifySub();
	}

	public function lookMe(){
		if(request()->isAjax()) return model('Merchant')->lookMe();

		$data = model('Merchant')->where('id', session('admin_userid'))->find();

		return view('', [
			'data' => $data
		]);
	}
}