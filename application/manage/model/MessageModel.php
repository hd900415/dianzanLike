<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class MessageModel extends Model{
	//表名
	protected $table = 'ly_message';

	public function secret(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.','','trim');//获取参数
		if (!$param) return '提交失败';

		//数据验证
		// $validate = validate('app\manage\validate\Users');
		// if (!$validate->scene('secretAdd')->check($param)) return $validate->getError();
		
		$param['add_time'] = time();
		$res = $this->insertGetId($param);
		if (!$res) return '提交失败';

		return 1;
	}
}