<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;
/*
	A-阿大
	权限业务处理
*/

class ManageRoleModel extends Model{
	//表名
	protected $table = 'ly_manage_role';
	/**
		权限列表
	**/
	public function role_list(){
		
		$role = $this->order('sort','ASC')->where('level', '=', 1)->select();
		foreach($role as &$value){
			$value['role2'] = $this->order('sort','ASC')->where('level', '=', 2)->where('cid', '=', $value['id'])->select();
			foreach($value['role2'] as &$value2){
				$value2['role3'] = $this->order('sort','ASC')->where('level', '=', 3)->where('pid', '=', $value2['id'])->select();
			}

		}

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['pid','=',11],
			['level','=',3],
		];

		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'role_list'	=>	$role,
			'power'	=>	$power,
		);

	}
	/*
		添加权限
	*/
	public function add_role(){
			$param = input('post.');//获取参数
			if(!$param){
				return '提交失败';
			}

			//验证数据
			$validate = new \app\manage\validate\role;
	
			if (!$validate->check($param)) {
				return $validate->getError();
			}
			if($param['cid'] and $param['pid']){
				$param['level'] = 3;
			}else if($param['cid']){
				$param['level'] = 2;
			}else{
				$param['level'] = 1;
			}
			//判断权限是否存在
			$is_role = $this->where('role_url','=',$param['role_url'])->count();
			if($is_role){
				return '权限已存在';
			}
			//排序
			$countCid = $this->where('cid', $param['cid'])->count();
			$param['sort'] = $countCid + 1;

			$role_id =  $this->insertGetId(array_filter($param));
			if(!$role_id) return '添加失败';
			//添加到管理权限
			$admins = model('Manage')->select();
			foreach($admins as $key =>$value){
				$admins_role[$key]['uid']     		= $value['id'];
				$admins_role[$key]['role_id'] 		= $role_id;
				$admins_role[$key]['cid']     		= $param['cid'];
				$admins_role[$key]['pid']     		= $param['pid'];
				$admins_role[$key]['sort']     		= $param['sort'];
				$admins_role[$key]['level']     	= $param['level'];
				$admins_role[$key]['role_name'] 	= $param['role_name'];
				$admins_role[$key]['role_url'] 		= $param['role_url'];
			}
			$count = model('ManageUserRole')->insertAll($admins_role);
			if(!$count) return '添加异常';

			return 1;
	}
	
	/*
		获取子权限
	*/
	public function getRoleByIdAdd(){
		$id = (int)input('get.id/d');//获取参数
		if($id){
			$role = $this->field('id,cid,pid')->where('id','=',$id)->find();
			if($role['cid']){
				$role = array(
					'cid'=>$role['cid'],
					'pid'=>$role['id']
				);
			}else{
				$role = array(
					'cid'=>$role['id'],
					'pid'=>0
				);
			}
		}else{
			$role = array(
				'cid'=>0,
				'pid'=>0
			);
		}
		return $role;
	}
	
	/*
		获取权限
	*/
	public function getRoleByIdEdit(){
		$id = (int)input('get.id/d');//获取参数
		$data = $this->where('id',$id)->find();
		return $data;
	}
	
	/*
		修改权限
	*/
	public function role_edit(){
			
			$param = input('post.');//获取参数
			if(!$param){
				return '提交失败';
			}

			//验证数据
			$validate = new \app\manage\validate\role;
	
			if (!$validate->check($param)) {
				return $validate->getError();
			}
			//更新
			
			
			//判断权限是否存在
			$is_role = $this->where('role_url','=',$param['role_url'])->count();
			if($is_role){
				$roleData = [
					'role_name'	      => $param['role_name'],
					'sort'	          => $param['sort'],
				];
			}else{
				$roleData = [
					'role_name'	      => $param['role_name'],
					'role_url'	      => $param['role_url'],
					'sort'	          => $param['sort'],
				];
			}
			
			$is_update = $this->where('id', $param['id'])->update($roleData);
			if($is_update){
				//修改管理员
				model('ManageUserRole')->where('role_id', $param['id'])->update($roleData);
				return 1;
			}

			return '修改失败';
	}
	/**
		删除权限
	**/
	public function role_delete(){
		$id = (int)input('post.id/d');//获取参数
		$count = $this->where('id',$id)->delete();
		if($count){
			//删除子集权限
			$this->where('pid',$id)->delete();
			$this->where('cid',$id)->delete();

			//删除管理的权限
			model('ManageUserRole')->where('role_id',$id)->delete();
			model('ManageUserRole')->where('pid',$id)->delete();
			model('ManageUserRole')->where('cid',$id)->delete();
			return 1;
		}
		return 0;
	}
	

}