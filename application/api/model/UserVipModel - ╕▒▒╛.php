<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 用于获取系统设置数据
 */

namespace app\api\model;

use think\Model;

class UserVipModel extends Model{
	//表名
	protected $table = 'ly_user_vip';
	
	// 用户购买vip
	public function userBuyVip(){
		
		$param		= input('post.');
		$userArr	= explode(',',auth_code($param['token'],'DECODE'));
		$uid		= $userArr[0];
		$username	= $userArr[1];
		
		$lang		= (input('post.lang')) ? input('post.lang') : 'cn';	// 语言类型
		$grade		= input('post.grade/d');	// 购买的VIP等级

		// 检测VIP等级
		if($grade < 2){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '充值的VIP等级错误'];
			}else{
				return ['code' => 0, 'code_dec' => 'Wrong VIP level for recharging!'];
			}
		}
		
		// 检测充值的VIP等级
		$GradeInfo	= model('UserGrade')->where('grade', $grade)->find();
		if(!$GradeInfo){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => 'VIP等级不存在'];
			}else{
				return ['code' => 0, 'code_dec' => 'VIP level does not exist!'];
			}
		}
		$amount	= $GradeInfo['amount'];//
		
		$vip_level	=	model('Users')->where('id', $uid)->value('vip_level');
		
		//不等购买低于会员等级的vip
		if($grade < $vip_level){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '充值的VIP等级不能小于原VIP等级'];
			}else{
				return ['code' => 0, 'code_dec' => 'The recharge VIP level cannot be less than the original VIP level!'];
			}
		}

		$uservipdata	= $this->where(array(['uid','=',$uid],['state','=',1],['etime','>=',time()]))->find();
		
		$in = $is_in	=	$is_up	=	0;
		$start_time = strtotime(date("Y-m-d",time()));//当天的时间错

		if($uservipdata){
			//等级相同续费
			if($uservipdata['grade'] == $grade && $grade == $vip_level){
				//更新结束时间
				$arr1 = array(
					'etime'	=>	$uservipdata['etime']	+	365 * 24 * 3600,
				);
				$amount		= $GradeInfo['amount'];//续费金额
				
			}else{
				//更新结束时间
				$arr1 = array(
					'en_name'	=>	$GradeInfo['en_name'],
					'name'		=>	$GradeInfo['name'],
					'grade'		=>	$grade,
					'stime'		=>	$start_time,
					'etime'		=>	$start_time	+	365 * 24 * 3600,
				);

				$amount		=	$GradeInfo['amount'] - model('UserGrade')->where('grade', $vip_level)->value('amount');
			}
		}else{//没有vip
			$newData	= [
				'username'	=> $username,
				'uid'		=> $uid,
				'state'		=> 1,
				'name'		=> $GradeInfo['name'],
				'en_name'	=> $GradeInfo['en_name'],
				'grade'		=> $grade,
				'stime'		=> $start_time,
				'etime'		=> $start_time + 365 * 24 * 3600,
			];
			$in = 1;
		}

		// 检测用户的余额
		$userBalance	= model('UserTotal')->where('uid', $uid)->value('balance');	// 获取用户的余额
		if($amount > $userBalance){
			if($lang=='cn'){
				return ['code' => 2,'amount'=>$amount-$userBalance, 'code_dec' => '用户余额不足'];
			}else{
				return ['code' => 2,'amount'=>$amount-$userBalance, 'code_dec' => 'Insufficient user balance!'];
			}
		}
		
		if($in){
			$is_in	= 	$this->insertGetId($newData);//添加会员
		}else{
			$is_up	=	$this->where('id' , $uservipdata['id'])->update($arr1);
		}
		
		$is = $is_up + $is_in;
		if(!$is){
			if($is_in){
				$this->where('id', $new_id)->delete();
			}
			if($is_up){
				//更新结束时间
				$arr3 = array(
					'en_name'	=>	$uservipdata['en_name'],
					'name'		=>	$uservipdata['name'],
					'grade'		=>	$uservipdata['grade'],
					'stime'		=>	$uservipdata['stime'],
					'etime'		=>	$uservipdata['etime'],
				);
				$this->where('id' , $uservipdata['id'])->update($arr3);
			}
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => 'VIP充值失败'];
			}else{
				return ['code' => 0, 'code_dec' => 'VIP recharge failed!'];
			}
		}

		// 扣减用户汇总表的用户余额
		$isDecBalance	= model('UserTotal')->where('uid', $uid)->setDec('balance', $amount);
		if(!$isDecBalance){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => 'VIP充值失败'];
			}else{
				return ['code' => 0, 'code_dec' => 'VIP recharge failed!'];
			}
			
			if($is_in){
				$this->where('id', $new_id)->delete();
			}
			
			if($is_up){
				//更新结束时间
				$arr3 = array(
					'en_name'	=>	$uservipdata['en_name'],
					'name'		=>	$uservipdata['name'],
					'grade'		=>	$uservipdata['grade'],
					'stime'		=>	$uservipdata['stime'],
					'etime'		=>	$uservipdata['etime'],
				);
				$this->where('id' , $uservipdata['id'])->update($arr3);
			}

		}

		// 流水
		$order_number = 'B'.trading_number();
		$trade_number = 'L'.trading_number();
		
		$financial_data['uid'] 						= $uid;
		$financial_data['username'] 				= $username;
		$financial_data['order_number'] 			= $order_number;
		$financial_data['trade_number'] 			= $trade_number;
		$financial_data['trade_type'] 				= 9;
		$financial_data['trade_before_balance']		= $userBalance;
		$financial_data['trade_amount'] 			= $amount;
		$financial_data['account_balance'] 			= $userBalance - $amount;
		$financial_data['remarks'] 					= '购买VIP';
		$financial_data['types'] 					= 1;	// 用户1，商户2

		model('TradeDetails')->tradeDetails($financial_data);
		
		//更新会员等级
		model('Users')->where('id', $uid)->update(array('vip_level'=>$grade));
		
		//推荐返佣
		$userinfo = model('Users')->where('id', $uid)->find();
		
		if($userinfo['is_spread']==0){
			if($userinfo['sid']){
				//上级推荐返佣
				if($userinfo['sid']){
					$rebatearr = array(
						'num'			=>	1,
						'uid'			=>	$userinfo['id'],
						'sid'			=>	$userinfo['sid'],
						'order_number'	=>	$order_number,
						'spread'		=>	$GradeInfo['spread'],
					);
					$this->setspread($rebatearr);
				}
			}
			model('Users')->where('id', $uid)->update(array('is_spread'=>1));
		}

		if($lang=='cn'){
			return ['code' => 1, 'code_dec' => 'VIP充值成功'];
		}else{
			return ['code' => 1, 'code_dec' => 'VIP recharge succeeded!'];
		}
	}
	
	
	// 获取用户购买vip记录列表
	public function getUserBuyVipList(){
		//获取参数
		$token 		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];
		$lang		= (input('post.lang')) ? input('post.lang') : 'cn';	// 语言类型
		
		$is_user	= model('Users')->where('id', $uid)->count();
		//检测用户
		if($is_user){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '用户不存在'];
			}else{
				return ['code' => 0, 'code_dec' => 'user does not exist!'];
			}
		}
		
		$countNum	= $this->where('uid', $uid)->count();		
		if(!$countNum){
			$data['code'] = 0;
			$data['code_dec']	= '暂无记录';
			return $data;
		}

		//每页记录数
		$pageSize	= (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		//当前页
		$pageNo		= (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		//总页数
		$pageTotal	= ceil($countNum / $pageSize); //当前页数大于最后页数，取最后
		//偏移量
		$limitOffset	= ($pageNo - 1) * $pageSize;
			
		$userBuyVipList	= $this->where('uid', $uid)->order('stime desc')->limit($limitOffset, $pageSize)->select();
		if(is_object($userBuyVipList)) $userBuyVipListArray = $userBuyVipList->toArray();
		
		//获取成功
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $countNum;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;
		
		//数组重组赋值
		foreach ($userBuyVipListArray as $key => $value) {			
			$data['info'][$key]['id'] 		= $value['id'];
			$data['info'][$key]['uid'] 		= $value['uid'];
			$data['info'][$key]['username'] = $value['username'];
			$data['info'][$key]['name'] 	= $value['name'];
			$data['info'][$key]['en_name'] 	= $value['en_name'];
			$data['info'][$key]['grade'] 	= $value['grade'];
			$data['info'][$key]['state'] 	= $value['state'];
			$data['info'][$key]['stime'] 	= date('Y-m-d H:i:s',$value['stime']);
			$data['info'][$key]['etime'] 	= date('Y-m-d H:i:s',$value['etime']);
		}

		return $data;
	}
	
	public function setspread($param){
		if($param['num']<4){//上三级
		
			$spread_arr 		=	explode(',',$param['spread']);
			
			$rebate_amount		=	$spread_arr[$param['num']-1];
			
			if($rebate_amount>0){
				
				$userinfo = model('Users')->field('ly_users.id,ly_users.username,ly_users.sid,ly_users.vip_level,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.id', $param['sid'])->find();
				
				if($userinfo){
					$GradeInfo_user	= model('UserGrade')->where('grade', $userinfo['vip_level'])->find();
					$spread_user 		=	explode(',',$GradeInfo_user['spread']);
					$rebate_user		=   $spread_user[$param['num']-1];
					$rebate_real		=	($rebate_user < $rebate_amount)?$rebate_user:$rebate_amount;
					if ($rebate_real > 0) {
						$is_up_to = model('UserTotal')->where('uid', $userinfo['id'])->setInc('balance', $rebate_real);

						if($is_up_to){
							model('UserTotal')->where('uid', $userinfo['id'])->setInc('total_balance', $rebate_real);
							// 流水
							$financial_data_p['uid'] 					= $userinfo['id'];
							$financial_data_p['sid']					= $param['uid'];
							$financial_data_p['username'] 				= $userinfo['username'];
							$financial_data_p['order_number'] 			= 'D'.trading_number();
							$financial_data_p['trade_number'] 			= 'L'.trading_number();
							$financial_data_p['trade_type'] 			= 8;
							$financial_data_p['trade_before_balance']	= $userinfo['balance'];
							$financial_data_p['trade_amount'] 			= $rebate_real;
							$financial_data_p['account_balance'] 		= $userinfo['balance'] + $rebate_real;
							$financial_data_p['remarks'] 				= '推荐返佣';
							$financial_data_p['types'] 					= 1;	// 用户1，商户2

							model('common/TradeDetails')->tradeDetails($financial_data_p);
						}
					}
				}
				if($userinfo['sid']){
					$rebatearr = array(
						'num'			=>	$param['num']+1,
						'uid'			=>	$userinfo['id'],
						'sid'			=>	$userinfo['sid'],
						'order_number'	=>	$param['order_number'],
						'spread'		=>	$param['spread'],
					);
					$this->setspread($rebatearr);
				}
			}
		}
	}
}
