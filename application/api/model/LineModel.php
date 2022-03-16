<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;

class LineModel extends Model{
	/**
	 * [getNotice 获取线路数组]
	 * @return [type] [description]
	 */
	public function getLineArray(){
		$data	= json_encode(['http://103.101.207.193:8090','http://103.101.207.193:8090','http://103.101.207.193:8090','http://103.101.207.193:8090','http://103.101.207.193:8090']);		
		
		return $data;
	}
	
	
}