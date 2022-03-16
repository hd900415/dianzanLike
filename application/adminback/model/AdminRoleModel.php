<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 */

namespace app\admin\model;

use think\Model;

class AdminRoleModel extends Model{
	//表名
	protected $table = 'ly_admin_role';

	//获取权限列表
	public function getAdminsRoleByUsersId(){
		if (is_numeric(session('admin_types'))) {
			// 获取商户类型
			$merType = model('Merchant')->where('id', session('admin_userid'))->value('types');
			$fieldStr = ($merType == 1) ? 'agent' : 'basic';
		} else {
			$fieldStr = 'user';
		}
		
		//管理员ID
		$role = $this->where(array([$fieldStr, '=', 1], ['level', '=', 1]))->order('sort','asc')->select()->toArray();
		
		return $role;
	}
	/**
	 * 检查权限
	 * @param  array  $where 检查条件
	 * @return int    0/1
	 */
	public function checkUsersRole($roleUrl){
		if (is_numeric(session('admin_types'))) {
			// 获取商户类型
			$merType = model('Merchant')->where('id', session('admin_userid'))->value('types');
			$fieldStr = ($merType == 1) ? 'agent' : 'basic';
		} else {
			$fieldStr = 'user';
		}

		$where = array(
			['role_url', '=', $roleUrl],
			[$fieldStr, '=', 1]
		);
		$count = $this->where($where)->count();
		return $count;
	}

	/**
	 * 获取一个版块的权限
	 * @param  array  $where 查询条件
	 * @return array  
	 */
	public function getUserPower($where=array()){
		if (is_numeric(session('admin_types'))) {
			// 获取商户类型
			$merType = model('Merchant')->where('id', session('admin_userid'))->value('types');
			$fieldStr = ($merType == 1) ? 'agent' : 'basic';
		} else {
			$fieldStr = 'user';
		}

		$powerTemp = $this->field('id',$fieldStr)->where($where)->select();
		foreach ($powerTemp as $kPower => $vPower) {
			$power[$vPower['id']] = $vPower[$fieldStr];
		}
		return $power;
	}
}