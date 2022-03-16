<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class UserLinkModel extends Model{
	//表名
	protected $table = 'ly_userlink';

	public function linkList(){
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		
		//用户名搜索
		if(isset($param['username']) && $param['username']){
			$pageParam['username'] = $param['username'];

			$userId = model('Users')->where('username',trim($param['username']))->value('id');
			if (!$userId) {
				return array(
					'where'		=>	$pageParam,
					'linkList'	=>	array(),//数据
					'page'		=>	'',//分页
					'power'		=>	$power,//权限
				);
			}

			$where[] = array('ly_userlink.sid','=',$userId);
		}
		//查询符合条件的数据
		$resultData = $this->field('users.username,ly_userlink.*')->join('users','ly_userlink.sid=users.id','inner')->where($where)->paginate(14,false,['query'=>$pageParam]);
		//数据集转数组
		$linkList = $resultData->toArray()['data'];

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',2],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'where'		=>	$pageParam,
			'linkList'	=>	$linkList,//数据
			'page'		=>	$resultData->render(),//分页
			'power'		=>	$power,//权限
		);
	}
}