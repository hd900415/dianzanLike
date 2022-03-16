<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;
/*
	A-阿大
	权限业务处理
*/

class AdminRoleModel extends Model{
	//表名
	protected $table = 'ly_admin_role';
	/**
		权限列表
	**/
	public function role_list(){
		$role = $this->where('level', '=', 1)->order('sort','asc')->select();
		foreach($role as &$value){
			$value['role2'] = $this->where(array(['level', '=', 2], ['pid', '=', $value['id']]))->order('sort','asc')->select();
		}

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',1]
		];

		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'role_list' =>	$role,
			'power'     =>	$power,
		);

	}
	/*
		添加权限
	*/
	public function add_role(){
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';

		//验证数据
		$validate = validate('app\manage\validate\role');
		if (!$validate->check($param)) return $validate->getError();

		$param['level'] = (isset($param['pid']) && $param['pid']) ? 2 : 1;
		//判断权限是否存在
		$is_role = $this->where('role_url','=',$param['role_url'])->count();
		if ($is_role) return '权限已存在';
		//排序
		$countCid = $this->where('pid', $param['pid'])->count();
		$param['sort'] = $countCid + 1;

		$role_id =  $this->insertGetId(array_filter($param));
		if(!$role_id) return '添加失败';

		return 1;
	}
	
	/*
		获取子权限
	*/
	public function getRoleByIdAdd(){
		
		$role = array('pid'=>0);

		$id = input('get.id/d');//获取参数
		if ($id) $role = array('pid'=>$id);

		return $role;
	}
	
	/*
		获取权限
	*/
	public function getRoleByIdEdit(){
		$id = input('get.id/d');//获取参数
		$data = $this->where('id',$id)->find();
		return $data;
	}
	
	/*
		修改权限
	*/
	public function role_edit(){
			
		$param = input('post.');//获取参数
		if (!$param) return '提交失败';

		//验证数据
		$validate = validate('app\manage\validate\role');
		if (!$validate->check($param)) return $validate->getError();
		
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
		if (!$is_update) return '修改失败';

		return 1;
	}
	/**
		删除权限
	**/
	public function role_delete(){
		$id = (int)input('post.id/d');//获取参数
		$count = $this->where('id',$id)->delete();
		if (!$count) return 0;
		//删除子集权限
		$this->where('pid',$id)->delete();

		return 1;
	}
	
	public function setMerchantRole(){
		if (!request()->isAjax()) return '非法提交';

		$param = input('post.');
		if (!isset($param['id']) || !$param['id']) return '缺少参数';
		if (!isset($param['types']) || !$param['types']) return '缺少参数';
		if (!isset($param['state']) || !$param['state']) return '缺少参数';

		$res = $this->where('id','=',$param['id'])->update([$param['types']=>$param['state']]);
		if (!$res) return '操作失败';

		$res2 = $this->where('pid','=',$param['id'])->update([$param['types']=>$param['state']]);

		return 1;
	}
}