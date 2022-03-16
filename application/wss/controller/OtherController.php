<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\wss\controller;

use think\Controller;
use think\Db;

class OtherController extends Controller{
	/**
	 * 清楚数据
	 * @return [type] [description]
	 */	
	public function delTableAll(){
		$tableArray = [
			'ly_actionlog',
			'ly_alipay',
			'ly_homelog',
			'ly_loginlog',
			'ly_merchant',
			'ly_merchant_bank',
			'ly_merchant_daily',
			'ly_merchant_team',
			'ly_merchant_total',
			'ly_merchant_withdrawals',
			'ly_order',
			'ly_qrcode',
			'ly_teammove_log',
			'ly_trade_details',
			'ly_user_activity',
			'ly_user_bank',
			'ly_user_commission',
			'ly_user_daily',
			'ly_user_recharge',
			'ly_user_team',
			'ly_user_total',
			'ly_user_transaction',
			'ly_user_withdrawals',
			'ly_users',
		];
		foreach ($tableArray as $key => $value) {
			Db::table($value)->delete(true);
		}
		exit('操作成功');
	}
}