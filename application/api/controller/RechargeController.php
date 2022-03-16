<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\controller;

use think\Controller;

class RechargeController extends Controller{
	//初始化方法
	protected function initialize(){
	 	parent::initialize();
		header('Access-Control-Allow-Origin:*');
    }

	/** 充值————生成充值订单信息 **/
	public function newRechargeOrder(){
		$data = model('UserRecharge')->newRechargeOrder();
		return json($data);
	}
	
	// 获得最新充值信息
	public function getNewRechargeInfo(){
		$data = model('UserRecharge')->getNewRechargeInfo();
		return json($data);
	}

	public function getRechargeInfo(){
		$data = model('UserRecharge')->getRechargeInfo();
		return json($data);
	}
	
	
	/** 获取充值订单列表 **/
	public function getRechargeOrderList(){
		$data = model('UserRecharge')->getRechargeOrderList();
		return json($data);
	}
	
	
	/** 获取充值订单信息 **/
	public function getRechargeOrderInfo(){
		$data = model('UserRecharge')->getRechargeOrderInfo();
		return json($data);
	}
	
	//确定付款 确认收款
	public function affirmOrder(){
		$data = model('UserRecharge')->affirmOrder();
		return json($data);
	}
	
	//推送最新订单到订单大厅
	public function pushNewOrder(){
		$data = model('UserRecharge')->pushNewOrder();
		$data['code']	=	1;
		return json($data);
	}
	
	//资金明显 流水
	public function FundDetails(){
		$data = model('UserRecharge')->FundDetails();
		$data['code']	=	1;
		return json($data);
	}
	
	// 用户使用该接口升级VIP等级的充值订单
	public function rechargeVipLevel(){
		$data = model('UserRecharge')->rechargeVipLevel();
		return json($data);
	}

}