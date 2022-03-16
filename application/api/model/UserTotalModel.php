<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;

class UserTotalModel extends Model{
	// 表名
	protected $table = 'ly_user_total';

	/**
	 * 获取用户余额
	 */
	public function getUserBalance(){
		//获取参数
		$token 		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];//uid
		$username 	= $userArr[1];//username
        $lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		$param 		= input('post.');

		$user_id = model('Users')->where('id',$uid)->value('id');
		
		if(!$user_id){
		    
			if($lang=='cn'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '数据获取失败'
			];
			}elseif($lang=='en'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Data acquisition failed'
			];
					}elseif($lang=='id'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Akquisisi data gagal'
			];
					}elseif($lang=='ft'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '數據獲取失敗'
			];
					}elseif($lang=='yd'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'डाटा प्राप्त करने विफल'
			];
					}elseif($lang=='vi'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Lỗi giành dữ liệu'
			];
					}elseif($lang=='es'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Adquisición de datos fallida'
			];
					}elseif($lang=='ja'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'データの取得に失敗しました'
			];
					}elseif($lang=='th'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'ล้มเหลวในการได้รับข้อมูล'
			];
					}		    
		    

			return $data;
		}

		$balance = $this->where('uid',$uid)->value('balance');

		$data['balance'] 	= round($balance,3);
		$data['code'] 		= 1;
			if($lang=='cn') $data['code_dec']	= '成功';
			elseif($lang=='en') $data['code_dec']	= 'success';
			elseif($lang=='id') $data['code_dec']	= 'sukses';
			elseif($lang=='ft') $data['code_dec']	= '成功';
			elseif($lang=='yd') $data['code_dec']	= 'सफलता';
			elseif($lang=='vi') $data['code_dec']	= 'thành công';
			elseif($lang=='es') $data['code_dec']	= 'éxito';
			elseif($lang=='ja') $data['code_dec']	= '成功';
			elseif($lang=='th') $data['code_dec']	= 'ประสบความสำเร็จ';
		
		return $data;			
	}

	/*
		第三方红包扣钱
	*/
	public function ko(){
		//获取参数
		$param 		=	input('post.');
		$token 		=	$param['token'];
		$userArr	=	explode(',',auth_code($token,'DECODE'));
		$uid		=	$userArr[0];//uid
		$username 	=	$userArr[1];//username
		$money		=	$param['money'];
		$type		=	$param['type'];
		
		$balance 	= 	$this->where('uid',$uid)->value('balance');

		switch($type){
			case 'f'://发
			
				if($balance < $money)	return 0;
				
				if($param['fid']){//私发
					$s_hb	=	model('Users')->where('id',$uid)->value('s_hb');
					if($s_hb==2){//不能私发
						return 0;
					}
				}
				
				$vip_level	=	model('Users')->where('id',$uid)->value('vip_level');
					if($vip_level < 2){//不能发
						return 2;
					}

				$is_update_user_b = $this->where('uid',$uid)->setDec('balance', $money);
				
				if(!$is_update_user_b) return 0;
				
				// 产生流水
				$financial_data['uid'] 						= $uid;
				$financial_data['username'] 				= $username;
				$financial_data['order_number'] 			= 'HB'.trading_number();
				$financial_data['trade_number'] 			= 'L'.trading_number();
				$financial_data['trade_type'] 				= 14;
				$financial_data['trade_before_balance']		= $balance;
				$financial_data['trade_amount'] 			= $money;
				$financial_data['account_balance'] 			= $balance - $money;
				$financial_data['remarks'] 					= '发放红包';
				
				model('TradeDetails')->tradeDetails($financial_data);
				
				return 1;

			break;
			case 'q'://抢
				
				$is_update_user_b =  $this->where('uid',$uid)->Inc('balance', $money)->update();
				
				if(!$is_update_user_b) return 0;
				
				// 产生流水
				$financial_data['uid'] 						= $uid;
				$financial_data['username'] 				= $username;
				$financial_data['order_number'] 			= 'HB'.trading_number();
				$financial_data['trade_number'] 			= 'L'.trading_number();
				$financial_data['trade_type'] 				= 15;
				$financial_data['trade_before_balance']		= $balance;
				$financial_data['trade_amount'] 			= $money;
				$financial_data['account_balance'] 			= $balance + $money;
				$financial_data['remarks'] 					= '领取红包';
				
				model('TradeDetails')->tradeDetails($financial_data);
				return 1;
			break;
		}

		return 0;
	}

	
	
}
