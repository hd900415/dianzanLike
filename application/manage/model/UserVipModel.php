<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class UserVipModel extends Model{
	//表名
	protected $table = 'ly_user_vip';

	/**
	 * 用户等级添加
	 */
	public function userLevelAdd(){
		if (!request()->isAjax()) return '非法提交';
		$param = input('post.');

		//数据验证
		// $validate = validate('app\manage\validate\Users');
		// if(!$validate->scene('userLevelAdd')->check($param)){
		// 	return $validate->getError();
		// }

		$insertRes = $this->allowField(true)->save($param);
		if(!$insertRes) return '添加失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'添加用户等级'.$param['name'],1);

		return 1;
	}

	/**
	 * 用户等级编辑
	 */
	public function userLevelEdit(){
		if (!request()->isAjax()) return '非法提交';
		$param = input('post.');

		//数据验证
		// $validate = validate('app\manage\validate\Users');
		// if(!$validate->scene('userLevelAdd')->check($param)){
		// 	return $validate->getError();
		// }

		$id = $param['id'];
		unset($param['id']);

		$insertRes = $this->save($param, ['id'=>$id]);
		if(!$insertRes) return '编辑失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'编辑用户等级'.$param['name'],1);

		return 1;
	}
}