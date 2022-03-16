<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 对流水的相关操作
 */
namespace app\common\model;

use think\Model;

class TradeDetailsModel extends Model{
	//表名
	protected $table = 'ly_trade_details';

	/**
	 * 添加流水
	 * @param  array  $array 流水数据
	 * @return bool          添加结果
	 */
	public function tradeDetails($array=array()){
		
		if (!$array) return false;
		if (!$array['uid']) return false;

		//类型 1                                      =会员 2=商户
		$tradeDetailsData['types'] = (isset($array['types']) && $array['types']) ? $array['types'] : 1;		
		//获取用户信息
		$userInfo = model('Users')->field('realname,username,vip_level,user_type')->where('id',$array['uid'])->findOrEmpty();
		$tradeDetailsData['username']  = (isset($userInfo['username']) && $userInfo['username']) ? $userInfo['username'] : $userInfo['username'];//会员名
		$tradeDetailsData['vip_level'] = (isset($array['vip_level']) && $array['vip_level']) ? $array['vip_level'] : $userInfo['vip_level'];//会员等级
		$tradeDetailsData['user_type'] = (isset($array['user_type']) && $array['user_type']) ? $array['user_type'] : $userInfo['user_type'];//会员等级

		$tradeDetailsData['uid']                    = $array['uid'];		
		$tradeDetailsData['sid']                    = (isset($array['sid']) && $array['sid']) ? $array['sid'] : 0;//被动会员ID		
		$tradeDetailsData['order_number']           = (isset($array['order_number']) && $array['order_number']) ? $array['order_number'] : 'D'.trading_number();//订单号		
		$tradeDetailsData['trade_number']           = (isset($array['trade_number']) && $array['trade_number']) ? $array['trade_number'] : 'L'.trading_number();//交易单号
		$tradeDetailsData['trade_time']             = (isset($array['trade_time']) && $array['trade_time']) ? $array['trade_time'] : time();//订单号		
		$tradeDetailsData['isadmin']                = (isset($array['isadmin']) && $array['isadmin']) ? $array['isadmin'] : 2;		
		
		//交易金额
		$tradeDetailsData['trade_amount']           = (isset($array['trade_amount']) && $array['trade_amount']) ? $array['trade_amount'] : 0;
		//交易前金额
		$tradeDetailsData['trade_before_balance']   = (isset($array['trade_before_balance']) && $array['trade_before_balance']) ? $array['trade_before_balance'] : 0;	
		//交易后金额
		$tradeDetailsData['account_balance']        = (isset($array['account_balance']) && $array['account_balance']) ? $array['account_balance'] : 0;
		//冻结金额
		$tradeDetailsData['account_total_balance'] = (isset($array['account_total_balance']) && $array['account_total_balance']) ? $array['account_total_balance'] : 0;	
		//交易说明
		$tradeDetailsData['remarks']                = (isset($array['remarks']) && $array['remarks']) ? $array['remarks'] : '';		
		//状态
		$tradeDetailsData['state']                  = (isset($array['state']) && $array['state']) ? $array['state'] : 1;
		//交易类型
		$tradeDetailsData['trade_type']             = (isset($array['trade_type']) && $array['trade_type']) ? $array['trade_type'] : 0;//交易类型ID 
		if (!$tradeDetailsData['trade_type']) return false;	

		//流水入库
		$tid = $this->insertGetId($tradeDetailsData);
		if(!$tid) return false;
		// 是否统计进每日
		$tradeDetailsData['isdaily'] = (isset($array['isdaily']) && $array['isdaily']) ? $array['isdaily'] : 1;

		//判断是否并进入每日报表
		return model('common/UserDaily')->updateReportForm([
			'uid'		=>	$tradeDetailsData['uid'],
			'username'	=>	$tradeDetailsData['username'],
			'type'		=>	$tradeDetailsData['trade_type'],
			'user_type'	=>	$tradeDetailsData['user_type'],
			'price'		=>	$tradeDetailsData['trade_amount'],
			'isadmin'	=>	$tradeDetailsData['isadmin'],
			'isdaily'	=>	$tradeDetailsData['isdaily'],
		]);

		return $tid;
	}
}