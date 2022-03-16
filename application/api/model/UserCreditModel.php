<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 用于获取系统设置数据
 */

namespace app\api\model;

use think\Model;

class UserCreditModel extends Model{
	//表名
	protected $table = 'ly_user_credit';
	
	/**
		获取用户积分信息
	**/
	public function getUserCreditList(){
		//获取参数
		$token 		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		$is_user	= model('Users')->where('id', $uid)->count();
		//检测用户
		if($is_user){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '用户不存在'];
			}elseif($lang=='en'){
				return ['code' => 0, 'code_dec' => 'user does not exist!'];
			}elseif($lang=='id'){
				return ['code' => 0, 'code_dec' => 'pengguna tidak ada'];
			}elseif($lang=='ft'){
				return ['code' => 0, 'code_dec' => '用戶不存在'];
			}elseif($lang=='yd'){
				return ['code' => 0, 'code_dec' => 'उपयोक्ता मौजूद नहीं है'];
			}elseif($lang=='vi'){
				return ['code' => 0, 'code_dec' => 'người dùng không tồn tại'];
			}elseif($lang=='es'){
				return ['code' => 0, 'code_dec' => 'Usuario no existente'];
			}elseif($lang=='ja'){
				return ['code' => 0, 'code_dec' => 'ユーザが存在しません'];
			}elseif($lang=='th'){
				return ['code' => 0, 'code_dec' => 'ผู้ใช้ไม่มี'];
			}
		}
		
		$countNum	= $this->where('uid', $uid)->count();		
		if(!$countNum){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
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
			$data['info'][$key]['credit'] 	= $value['credit'];
			$data['info'][$key]['remarks'] 	= $value['remarks'];
			$data['info'][$key]['time'] 	= date('Y-m-d H:i:s',$value['time']);
		}

		return $data;
	}

}