<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 对用户银行列表的相关操作
 */

namespace app\manage\model;

use think\Model;

class UserBankModel extends Model{
	//表名
	protected $table = 'ly_user_bank';

	/**
	 * 用户银行列表
	 */
	public function userBank(){
		$param = input('get.');//获取参数
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		//用户名搜索
		if(isset($param['username']) && $param['username']){
			$where[] = array('users.username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		//账户名搜索
		if(isset($param['name']) && $param['name']){
			$where[] = array('name','=',trim($param['name']));
			$pageParam['name'] = $param['name'];
		}
		//账号搜索
		if(isset($param['card_no']) && $param['card_no']){
			$where[] = array('card_no','=',trim($param['card_no']));
			$pageParam['card_no'] = $param['card_no'];
		}
		//绑定时间搜索
		if(isset($param['datetime_range']) && $param['datetime_range']){
			$dateTime = explode(' - ', $param['datetime_range']);
			$where[] = array('ly_user_bank.add_time','>=',strtotime($dateTime[0]));
			$where[] = array('ly_user_bank.add_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime_range'] = $param['datetime_range'];
		}
		//查询符合条件的数据
		$resultData = $this->where($where)->field('ly_user_bank.*,users.username')->join('users','ly_user_bank.uid = users.id','left')->order('users.id','desc')->paginate(16,false,['query'=>$pageParam]);
		//数据集转数组
		$userBank = $resultData->toArray()['data'];
		$adminColor = config('manage.adminColor');
		//部分元素重新赋值
		foreach ($userBank as $key => &$value) {
			$value['add_time']    = date('Y-m-d H:i:s',$value['add_time']);
			$value['statusColor'] = $adminColor[$value['status']];
		}

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',2],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'where'		=>	$pageParam,
			'userBank'	=>	$userBank,//数据
			'page'		=>	$resultData->render(),//分页
			'power'		=>	$power
		);
	}
}