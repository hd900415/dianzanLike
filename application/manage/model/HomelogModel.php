<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class HomelogModel extends Model{
	//表名
	protected $table = 'ly_homelog';

	/**
	 * 前台操作日志
	 */
	public function homeLog(){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where = array();
		$whereOr = &$where;
		$whereOr2 = &$where;
		//分页参数组装
		$pageParam = array();

		//用户名
		if(isset($param['username']) && $param['username']){
			$userId = model('Users')->where('username',trim($param['username']))->value('id');
			$where[] = array('uid','=',$userId);
			$pageParam['username'] = $param['username'];
		}
		//IP
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
		// 内容
		if(isset($param['log']) && $param['log']){
			$whereOr[] = array('params','like','%'.$param['log'].'%');
			$whereOr2[] = array('values','like','%'.$param['log'].'%');
			$pageParam['log'] = $param['log'];
		}
		
		//查询符合条件的数据
		if(isset($param['log']) && $param['log']){
			$resultData = $this->field('ly_homelog.*,users.username')->join('users','ly_homelog.uid=users.id','left')->whereOr([$whereOr, $whereOr2])->order(['time'=>'desc','id'=>'desc'])->paginate(15,false,['query'=>$pageParam]);
		}else{
			$resultData = $this->field('ly_homelog.*,users.username')->join('users','ly_homelog.uid=users.id','left')->where($where)->order(['time'=>'desc','id'=>'desc'])->paginate(15,false,['query'=>$pageParam]);
		}		
		
		return array(
			'data'			=>	$resultData,
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$pageParam
		);
	}

	/**
	 * 投注日志
	 */
	public function betLog(){
		$param = input('get.');

		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		//用户名
		if(isset($param['username']) && $param['username']){
			$where[] = array('users.username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		//IP
		if(isset($param['ip']) && $param['ip']){
			$where[] = array('ip','=',$param['ip']);
			$pageParam['ip'] = $param['ip'];
		}
		// 时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('time','>=',strtotime($dateTime[0]));
			$where[] = array('time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('time','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('time','<=',$todayEnd);
		}
		//查询符合条件的数据
		$resultData = $this->field('ly_homelog.*,users.username')->join('users','ly_homelog.uid = users.id')->where($where)->whereIn('func','beting,betRunIng')->order(['time'=>'desc','id'=>'desc'])->paginate(16,false,['query'=>$pageParam]);
		//数据集转数组
		// $betList = $resultData->toArray()['data'];
		
		return array(
			'resultData'	=>	$resultData,
			'where'			=>	$pageParam,
			'page'			=>	$resultData->render(),//分页
		);
	}
}