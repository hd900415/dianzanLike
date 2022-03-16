<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 用于获取系统设置数据
 */

namespace app\common\model;

use think\Model;

class SettingModel extends Model{
	//表名
	protected $table = 'ly_setting';
	
	/**
	 * 获取一条记录
	 * @param  string $fields 需要获取的字段
	 * @return array          数据
	 */
	public function getFieldsById($fields=''){
		return $this->field($fields)->find();
	}

	/**
	 * 设置修改
	 */
	public function settingEdit(){
		if(!request()->isAjax()) return '非法提交';

		$param = input('post.','','trim');
		if(!$param) return '提交失败';

/*		//数据验证
		$validate = validate('app\manage\validate\Base');
		if(!$validate->scene('settingedit')->check($param)){
			//抛出异常
			return $validate->getError();
		}
*/
		$res = $this->allowField(true)->save($param, ['id'=>1]);
		if(!$res) return '修改失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'修改了基本配置',1);

		return 1;
	}
}