<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\common\model;

use think\Model;

class UserTeamModel extends Model{
	//表名
	protected $table = 'ly_user_team';


	/**
	 * 获取在线人数
	 * @param  string $uid [description]
	 * @return [type]      [description]
	 */
	public function getTeamUsers($uid=''){
		if ($uid) {
			$array = model('UserTeam')->where('uid', $uid)->column('team');
		} else {
			$array = model('Users')->column('id');
		}
		
		$online = 0;
		$userList = model('Users')->column('id');
		foreach ($array as $key => $value) {			
			if (cache('C_token_'.$value)) $online++;
		}

		return $online;
	}


}