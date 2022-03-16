<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

// use think\Model;
use app\common\model\LoginlogModel as L;

class LoginlogModel extends L{
	//表名
	protected $table = 'ly_loginlog';

	/**
	 * 添加操作日志
	 */
	public function loginLog(){
		$ip = request()->ip();
		$address = model('Loginlog')->GetIpLookup($ip);
		$array = array(
			'uid'		=>	session('manage_userid'),
			'username'	=>	session('manage_username'),
			'ip'		=>	$ip,
			'address'	=>	$address ? $address : '',
			'os'		=>	model('Loginlog')->get_os(),
			'time'		=>	time(),
			'browser'	=>	model('Loginlog')->get_broswer(),
			'type'		=>	'后台网页版',
			'isadmin'	=>	1,
		);
		//添加操作记录
		$this->save($array);
	}

	/**
	 * 后台登录日志
	 */
	public function adminLog(){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where = array(['isadmin','=',1]);
		//分页参数组装
		$pageParam = array();

		//用户名
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		//用户名
		if(isset($param['ip']) && $param['ip']){
			$where[] = array('ip','=',trim($param['ip']));
			$pageParam['ip'] = $param['ip'];
		}
		// 时间
		if(isset($param['datetime_range']) && $param['datetime_range']){
			$dateTime = explode(' - ', $param['datetime_range']);
			$where[] = array('time','>=',strtotime($dateTime[0]));
			$where[] = array('time','<=',strtotime($dateTime[1]));
			$pageParam['datetime_range'] = $param['datetime_range'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('time','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('time','<=',$todayEnd);
		}

		//查询符合条件的数据
		$resultData = $this->where($where)->order(['time'=>'desc','id'=>'desc'])->paginate(15,false,['query'=>$pageParam]);

		return array(
			'data'			=>	$resultData,
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$pageParam
		);
	}

	/**
	 * 前台登录日志
	 */
	public function homeLog(){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where = array(['isadmin','=',2]);
		//分页参数组装
		$pageParam = array();

		//用户名
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		//用户名
		if(isset($param['ip']) && $param['ip']){
			$where[] = array('ip','=',trim($param['ip']));
			$pageParam['ip'] = $param['ip'];
		}
		// 时间
		if(isset($param['datetime_range']) && $param['datetime_range']){
			$dateTime = explode(' - ', $param['datetime_range']);
			$where[] = array('time','>=',strtotime($dateTime[0]));
			$where[] = array('time','<=',strtotime($dateTime[1]));
			$pageParam['datetime_range'] = $param['datetime_range'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('time','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('time','<=',$todayEnd);
		}

		//查询符合条件的数据
		$resultData = $this->where($where)->order(['time'=>'desc','id'=>'desc'])->paginate(15,false,['query'=>$pageParam]);

		return array(
			'data'			=>	$resultData,
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$pageParam
		);
	}
}