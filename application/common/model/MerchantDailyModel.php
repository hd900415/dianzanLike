<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/**
 * 编写：祝踏岚
 * 对每日报表的相关操作
 */

namespace app\common\model;

use think\Model;

class MerchantDailyModel extends Model{
	//表名
	protected $table = 'ly_merchant_daily';

	/**
	 * 每日统筹
	 */
	public function updateReportForm($array=array()){
		foreach ($array as $key => $value) {
			$$key = $value;
		}

		if (!isset($uid) || !$uid) return false;
		// 判断交易类型
		if (!isset($type) || !$type) return false;
		// 是否后台操作1=是；2=否(程序自动运行)
		$isadmin = (isset($isadmin)) ? $isadmin : 2;
		// 获取用户信息
		switch ($type) {
			case 1://充值
				$field = 'recharge';
			break;
			case 2://提现
				$field = 'withdrawal';
			break;
			case 3://订单
				$field = 'order';
			break;
			case 4://买币
				$field = 'buy';
			break;
			case 5://卖币
				$field = 'sell';
			break;
			case 6://会员抢币
				$field = 'rob';
			break;
			case 7://客服回收币
				$field = 'recovery';
			break;
			case 8://活动
				$field = 'activity';
			break;
			case 9://佣金
				$field = 'commission';
			break;
			case 10://返点
				$field = 'rebate';
			break;
			case 11://费用
				$field = 'fee';
			break;
			case 12://订单收益返还
				$field = 'giveback';
			break;

		}
		
		if($isdaily==2) return false; //是否进每日
		
		if(!isset($field)) return false;
		
		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));
		
		$countReport = $this->where(array(['uid','=',$uid],['date','=',$today]))->count();

		//判断当日是否已有数据
		if(!$countReport){
			$res = $this->insertGetId(array(
				'uid'		=>	$uid,
				'username'	=>	$username,
				'user_type'	=>	$user_type,
				'date'		=>	$today,
				$field 		=>	$price
			));
		}else{
			$res = $this->where(array(['uid','=',$uid],['date','=',$today]))->setInc($field,$price);
		}

		if(!$res) return false;
		return true;
	}
}