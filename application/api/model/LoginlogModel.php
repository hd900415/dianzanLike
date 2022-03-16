<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\api\model;

// use think\Model;
use app\common\model\LoginlogModel as L;

class LoginlogModel extends L{
	//表名
	protected $table = 'ly_loginlog';

	/**
	 * [loginLog 获取登陆日志]
	 * @return [array] [登陆日志]
	 */
	public function loginLog(){
		//获取参数
		$post 		= input('param.');
		$user_id	= input('post.user_id/d');

		if (!isset($user_id) or !$user_id) {
			$data['code'] = 0;
			return $data;			
		}
		
		//定义查询条件
		$where[] = ['uid' , '=' , $user_id];

		//开始时间
		if(isset($post['s_date']) && $post['s_date']){
			$where[] = ['time' , '>=' ,strtotime($post['s_date'])] ;
		}else{
			$where[] = ['time' , '>=' ,mktime(0,0,0,date('m'),date('d'),date('Y'))];
		}
		//结束时间
		if(isset($post['e_date']) && $post['e_date']){
			$where[] = ['time' , '<=' ,strtotime($post['e_date']) + 86400];
		}else{
			$where[] = ['time' , '<=' ,time()];
		}

		//登陆日志总记录数
		$count = $this->where($where)->count();

		//每页记录数
		$pageSize = (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		
		//当前页
		$pageNo = (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		
		//总页数
		$pageTotal = ceil($count / $pageSize); //当前页数大于最后页数，取最后
		
		//偏移量
		$limitOffset = ($pageNo - 1) * $pageSize;

		//获取数据
		$login =   $this->order('time DESC')->where($where)->limit($pageSize,$limitOffset)->select();

		if(!$login){
			$data['code'] = 0;
			return $data;
		}

		$data['code'] = 1;
		$data['data_total_nums'] = $count;
		$data['data_total_page'] = $pageTotal;
		$data['data_current_page'] = $pageNo;

		foreach ($login as $key => $value) {
			$data['loginlog'][$key]['ip'] 		= $value['ip'];
			$data['loginlog'][$key]['address'] 	= $value['address'];
			$data['loginlog'][$key]['os'] 		= $value['os'];
			$data['loginlog'][$key]['time'] 	= date('Y-m-d H:i:s',$value['time']);
			$data['loginlog'][$key]['browser'] 	= $value['browser'];
			$data['loginlog'][$key]['type'] 	= $value['type'];
		}
		
		return $data;
	}
}