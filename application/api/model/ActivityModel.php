<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;

class ActivityModel extends Model{
	//表名
	protected $table = 'ly_activity_list';

	/**
	 * [getNotice 获取活动列表]
	 * @return [type] [description]
	 */
	public function getActivityList(){
		//每页显示记录
		$pageSize	= input('post.page_size/d');
		$pageSize	= (empty($pageSize)) ? 10 : $pageSize;
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		//当前的页号
		$pageNo		= input('post.page_no/d');
		$pageNo		= (empty($pageNo)) ? 1 : $pageNo;
		
		//总页数
		$activityList	= $this->where('state',1)->select();	// 获取活动列表
		
		if(empty($activityList)){

            if($lang=='cn'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '活动没有开启'
			];
			}elseif($lang=='en'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'The event was not activated'
			];   
			}elseif($lang=='id'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Peristiwa tidak diaktifkan'
			];  
			}elseif($lang=='ft'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '活動沒有開啟'
			];   
			}elseif($lang=='yd'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'घटना सक्रिय नहीं किया गया'
			];   
			}elseif($lang=='vi'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Sự kiện không được kích hoạt'
			];   
			}elseif($lang=='es'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Actividad no iniciada'
			];    
			}elseif($lang=='ja'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'イベントが開かれていません'
			];   
			}elseif($lang=='th'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'กิจกรรมไม่เปิด'
			];   
			}

			return $data;
		}		
		$activityList	= $activityList->toArray();			// 转换为数组
		
		$activityNum	= count($activityList);				// 统计活动数量
		
		$pageTotal		= ceil($activityNum / $pageSize);	//当前页数大于最后页数，取最后	
		
		$limitOffset	= ($pageNo - 1) * $pageSize;		// 偏移量
		
		// 分页读取
		$activityData	= $this->where('state',1)
							->order('sort','DESC')
							->limit($limitOffset, $pageSize)
							->select()->toArray();
		
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $activityNum;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;		
		foreach ($activityData as $key => $value) {
			$activityData[$key]['id']    	= $value['id'];
			$activityData[$key]['title']    = $value['title'];			// 活动标题
			$activityData[$key]['name']		= $value['name'];			// 活动简述
			$activityData[$key]['cover_img']	= $value['cover_img'];	// 活动封面图
			$activityData[$key]['start_time']	= date('Y-m-d H:i:s',$value['start_time']);
			$activityData[$key]['end_time']		= date('Y-m-d H:i:s',$value['end_time']);
		}
		
		$data['info'] = $activityData;
		
		return $data;
	}
	
	
	/**
	 * [getNotice 获取用户活动记录列表]
	 * @return [type] [description]
	 */
	public function getUserActivityList(){
		$token		=	input('post.token/s');
		$userArr	=	explode(',',auth_code($token,'DECODE'));
		$uid		=	$userArr[0];
		$username	=	$userArr[1];
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		$is_user	= model('Users')->where(['id'=>$uid,'username'=>$username])->count();
		if(!$is_user){
		    
            if($lang=='cn'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '用户不存在'
			];
			}elseif($lang=='en'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'user does not exist'
			];   
			}elseif($lang=='id'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'pengguna tidak ada'
			];  
			}elseif($lang=='ft'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '用戶不存在'
			];   
			}elseif($lang=='yd'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'उपयोक्ता मौजूद नहीं है'
			];   
			}elseif($lang=='vi'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'người dùng không tồn tại'
			];   
			}elseif($lang=='es'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Usuario no existente'
			];    
			}elseif($lang=='ja'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'ユーザが存在しません'
			];   
			}elseif($lang=='th'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'ผู้ใช้ไม่มี'
			];   
			}
		    
			return $data;
		}
		
		//每页显示记录
		$pageSize	= input('post.page_size/d');
		$pageSize	= (empty($pageSize)) ? 10 : $pageSize;
		
		//当前的页号
		$pageNo		= input('post.page_no/d');
		$pageNo		= (empty($pageNo)) ? 1 : $pageNo;
		
		//总记录数量
		$activityList	= model('UserActivity')->where(['uid'=>$uid,'state'=>1])->select();	// 获取用户未领取的活动列表
		
		if(!$activityList){
		    
            if($lang=='cn'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '用户无活动记录'
			];
			}elseif($lang=='en'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'The user has no activity record'
			];   
			}elseif($lang=='id'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Pengguna tidak memiliki catatan aktivitas'
			];  
			}elseif($lang=='ft'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '用戶無活動記錄'
			];   
			}elseif($lang=='yd'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'प्रयोक्ता को कोई सक्रिया रेकॉर्ड नहीं है'
			];   
			}elseif($lang=='vi'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Người dùng không có ghi chép hoạt động'
			];   
			}elseif($lang=='es'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Registro de usuario inactivo'
			];    
			}elseif($lang=='ja'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'ユーザはアクティブなレコードがありません。'
			];   
			}elseif($lang=='th'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'ผู้ใช้ไม่มีบันทึกกิจกรรม'
			];   
			}

			return $data;
		}		
		$activityList	= $activityList->toArray();			// 转换为数组
		
		$activityNum	= count($activityList);				// 统计活动数量
		
		$pageTotal		= ceil($activityNum / $pageSize);	//当前页数大于最后页数，取最后	
		
		$limitOffset	= ($pageNo - 1) * $pageSize;		// 偏移量
		
		// 分页读取
		$activityData	= model('UserActivity')->where(['uid'=>$uid,'state'=>1])	// 未领取的活动
							->order('date','DESC')									// 按日期降序排列
							->limit($limitOffset, $pageSize)
							->select()->toArray();
		
		if(!$activityData){
            if($lang=='cn'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '用户无活动记录'
			];
			}elseif($lang=='en'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'The user has no activity record'
			];   
			}elseif($lang=='id'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Pengguna tidak memiliki catatan aktivitas'
			];  
			}elseif($lang=='ft'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '用戶無活動記錄'
			];   
			}elseif($lang=='yd'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'प्रयोक्ता को कोई सक्रिया रेकॉर्ड नहीं है'
			];   
			}elseif($lang=='vi'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Người dùng không có ghi chép hoạt động'
			];   
			}elseif($lang=='es'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'Registro de usuario inactivo'
			];    
			}elseif($lang=='ja'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'ユーザはアクティブなレコードがありません。'
			];   
			}elseif($lang=='th'){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> 'ผู้ใช้ไม่มีบันทึกกิจกรรม'
			];   
			}
			return $data;
		}
		
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $activityNum;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;
		foreach ($activityData as $key => $value) {
			$activityData[$key]['id']    	= $value['id'];
			$activityData[$key]['date']		= date('Y-m-d H:i:s',$value['date']);
			$activityData[$key]['orderNum']	= $value['order_number'];
			$activityData[$key]['betTotal']	= $value['bet_total'];
			$activityData[$key]['rebate']	= $value['rebate'];
			$activityData[$key]['price']	= $value['price'];
		}
		
		$data['info'] = $activityData;
		
		return $data;
	}

}