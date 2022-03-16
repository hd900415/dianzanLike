<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;

class TaskClassModel extends Model
{
	protected $table = 'ly_task_class';
	
	/**
		获取任务类型列表
	**/
	public function getTaskClassList(){		
		$lang		= (input('post.lan')) ? input('post.lan') : 'id';	// 语言类型
				
		// 分页
		$count	= $this->where(['is_open' => 1])->count();	// 记录数量
		if(!$count){
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
		$page_size = (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		//当前页
		$pageNo = (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		//总页数
		$pageTotal = ceil($count / $page_size); //当前页数大于最后页数，取最后
		//偏移量
		$limitOffset = ($pageNo - 1) * $page_size;
		
		$dataAll = $this->where(['is_open' => 1])->limit($limitOffset, $page_size)->select()->toArray();
		
		//获取成功
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;		// 记录数量
		$data['data_total_page'] 	= $pageTotal;	//总页数
		$data['data_current_page'] 	= $pageNo;		//当前页
		
		foreach ($dataAll as $key => $value) {			
			$data['info'][$key]['classname']	= $value['classname'];
			$data['info'][$key]['remark']		= $value['remark'];
		}
		
		return $data;
	}
	
	
	
	
}