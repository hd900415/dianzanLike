<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;
/*
	A-阿大
	管理员权限业务处理
*/

class ManageUserRoleModel extends Model{
	//表名
	protected $table = 'ly_manage_user_role';
	
	//获取权限列表
	public function getAdminsRoleByUsersId($usersid){
		
		//管理员ID
		$role = $this->order('sort','ASC')->where('state', '=', 1)->where('level', '=', 1)->where('uid', '=', $usersid)->select()->toArray();
		foreach($role as &$value){
			$value['role2'] = $this->order('sort','ASC')->where('state', '=', 1)->where('level', '=', 2)->where('cid', '=',$value['role_id'])->where('uid', '=', $usersid)->select()->toArray();
		}
		return $role;
	}
	/**
	 * 检查权限
	 * @param  array  $where 检查条件
	 * @return int    0/1
	 */
	public function checkUsersRole($where=array()){
		$wheres['uid'] 		= $where['uid'];
		$wheres['role_url']	= $where['role_url'];
		$wheres['state']	= 1;
		$count = $this->where($where)->count();
		return $count;
	}

	/**
	 * 获取一个版块的权限
	 * @param  array  $where 查询条件
	 * @return array  
	 */
	public function getUserPower($where=array()){
		$powerTemp = $this->field('role_id,state')->where($where)->select();
		foreach ($powerTemp as $kPower => $vPower) {
			$power[$vPower['role_id']] = $vPower['state'];
		}
		return $power;
	}

}