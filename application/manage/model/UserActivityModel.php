<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class UserActivityModel extends Model{
	//表名
	protected $table = 'ly_user_activity';

	/**
	 * 活动记录
	 */
	public function activityRecord(){
		//获取参数
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
		// 时间
		if(isset($param['datetime_range']) && $param['datetime_range']){
			$dateTime = explode(' - ', $param['datetime_range']);
			$where[] = array('date','>=',strtotime($dateTime[0]));
			$where[] = array('date','<=',strtotime($dateTime[1]));
			$pageParam['datetime_range'] = $param['datetime_range'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('date','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('date','<=',$todayEnd);
		}

		//查询符合条件的数据
		$resultData = $this->field('ly_user_activity.*,users.username')->join('users','ly_user_activity.uid = users.id','left')->where($where)->order(['date'=>'desc','id'=>'desc'])->paginate(16,false,['query'=>$pageParam]);

		return array(
			'data'			=>	$resultData,
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$pageParam
		);
	}
}