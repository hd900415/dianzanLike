<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 用于获取系统设置数据
 */

namespace app\index\model;

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
	
}