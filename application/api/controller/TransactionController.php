<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\controller;

use app\api\controller\BaseController;

class TransactionController extends BaseController{

	/**
	 * 提现
	 */
	public function draw(){
		$data = model('UserWithdrawals')->draw();
		return json($data);
	}


	/**
	 * 提现记录
	 */
	public function getDrawRecord(){
		$data = model('UserWithdrawals')->getUserWithdrawalsList();
		return json($data);
	}
	
	/**
	 * 渠道充值
	 */
	public function getRechargetype(){
		$data = model('RechangeType')->getRechargetype();
		return json($data);
	}

	/**
	 * 充值记录
	 */
	public function getRechargeRecord(){
		$data = model('UserRecharge')->getUserRechargeList();
		return json($data);
	}

	//资金明显 流水
	public function FundDetails(){
		$data = model('UserTransaction')->FundDetails();
		return json($data);
	}
	//转账
	public function Transfer(){
		$data = model('UserTransaction')->Transfer();
		return json($data);
	}
	
}