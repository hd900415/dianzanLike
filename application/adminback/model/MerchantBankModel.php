<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\model;

use think\Model;

class MerchantBankModel extends Model{
	//表名
	protected $table = 'ly_merchant_bank';

	public function bindBankcard(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');

		$validate = validate('app\admin\validate\Info');
		if(!$validate->scene('bindBankcard')->check($param)) return $validate->getError();

		$insertData = array(
			'mid'         =>	session('admin_userid'),
			'card_name'   =>	$param['card_name'],
			'bank_name'   =>	$param['bank_name'],
			'card_number' =>	$param['card_number'],
			'bind_time'   =>	time(),
		);
		$res = $this->allowField(true)->insertGetId($insertData);
		if (!$res) return '绑定失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('admin_username'),'绑定银行卡，持卡人：'.$param['card_name'].'，卡号：'.$param['card_number'],3);
		
		return 1;
	}
}