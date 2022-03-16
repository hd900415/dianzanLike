<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 作用：生成操作日志
 */

namespace app\common\model;

use think\Model;

class ActionlogModel extends Model{
	//表名
	protected $table = 'ly_actionlog';

	/**
	 * 添加操作日志
	 * @param string $username 操作用户名
	 * @param string $log 日志内容
	 * @param integer $isadmin 是否后台用户操作，后台传1
	 */
	public function actionLog($username,$log,$isadmin=2){
		$array = array(
			'username'	=>	$username,
			'time'		=>	time(),
			'ip'		=>	model('Loginlog')->getClientIp(),
			'log'		=>	$log,
			'isadmin'	=>	$isadmin,
		);
		//添加操作记录
		$this->save($array);
	}
}