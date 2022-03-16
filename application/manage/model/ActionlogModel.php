<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 作用：生成操作日志
 */

namespace app\manage\model;

use think\Model;

class ActionlogModel extends Model{
	//表名
	protected $table = 'ly_actionlog';

	/**
	 * 添加操作日志
	 * @param string $username 操作用户名
	 * @param string $log 日志内容
	 * @param integer $isadmin 是否后台用户操作，后台传1
	 */
	public function actionLog($username,$log,$isadmin=2){
		$array = array(
			'username'	=>	$username,
			'time'		=>	time(),
			'ip'		=>	model('Loginlog')->getClientIp(),
			'log'		=>	$log,
			'isadmin'	=>	$isadmin,
		);
		//添加操作记录
		$this->save($array);
	}

	/**
	 * 后台操作日志
	 */
	public function adminActionLog(){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();

		//用户名
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		//IP
		if(isset($param['ip']) && $param['ip']){
			$where[] = array('ip','=',trim($param['ip']));
			$pageParam['ip'] = $param['ip'];
		}
		// 内容
		if(isset($param['log']) && $param['log']){
			$where[] = array('log','like','%'.$param['log'].'%');
			$pageParam['log'] = $param['log'];
		}
		//用户名
		if(isset($param['isadmin']) && $param['isadmin']){
			$where[] = array('isadmin','=',$param['isadmin']);
			$pageParam['isadmin'] = $param['isadmin'];
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
		$where2   = $where;
		$where[]  = ['isadmin','=',1];
		$where2[] = ['isadmin','=',3];
		
		//查询符合条件的数据
		$resultData = $this->whereOr([$where, $where2])->order(['time'=>'desc','id'=>'desc'])->paginate(14,false,['query'=>$pageParam]);

		return array(
			'data'			=>	$resultData,
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$pageParam
		);
	}
}