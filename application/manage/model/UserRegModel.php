<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 对开户规则的相关操作
 */

namespace app\manage\model;
/**
 * 编写：祝踏岚
 * 开户规则处理
 */
use think\Model;

class UserRegModel extends Model{
	//表名
	protected $table = 'ly_user_reg';

	/**
	 * 开户规则
	 */
	public function openRule(){
		//获取参数
		$param = input('get.');

		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();

		//用户名搜素搜
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}

		//查询符合条件的数据
		$resultData = $this->field('ly_user_reg.*,users.username')->join('users','ly_user_reg.uid = users.id','left')->where($where)->order(['vip','min_rebate','max_rebate','max_user','use_num'=>'asc'])->paginate(16,false,['query'=>$pageParam]);

		$powerWhere = [
			['uid','=',session('manage_userid')],
			['pid','=',170],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'openRule'	=>	$resultData,
			'page'		=>	$resultData->render(),//分页
			'power'		=>	$power,//权限
		);
	}
	/**
	 * 开户规则添加
	 */
	public function ruleAdd(){
		//获取参数
		$param = input('post.');

		//数据验证
		$validate = validate('app\manage\validate\Users');
		if(!$validate->scene('ruleadd')->check($param)){
			return $validate->getError();
		}

		if(isset($param['uid']) and $param['uid']){
			$param['types'] = 2;
		}else{
			$param['types'] = 1;
			$param['uid']   = '';
    	}

    	$addRes = $this->insertGetId($param);

    	if(!$addRes) return '添加失败';

    	return 1;
	}
	/**
	 * 开户规则视图
	 */
	public function ruleAddView(){
		//获取参数
		$param = input('get.');

		return isset($param['id']) ? $param['id'] : '';
	}
	/**
	 * 开户规则编辑
	 */
	public function ruleEdit(){
		//获取参数
		$param = input('post.');
		if(!$param) return '提交失败';

		//数据验证
		$validate = validate('app\manage\validate\Users');
		if(!$validate->scene('ruleadd')->check($param)){
			return $validate->getError();
		}

		$updateArray = array(
			'min_rebate'=>$param['min_rebate'],
			'max_rebate'=>$param['max_rebate'],
			'max_user'=>$param['max_user']
		);
		$editRes = $this->where('id',$param['id'])->update($updateArray);
		if(!$editRes) return '操作失败';

		return 1;
	}
	/**
	 * 开户规则编辑视图
	 */
	public function ruleEditView(){
		$param = input('get.');

		return $this->where('id',$param['id'])->find();
	}
}