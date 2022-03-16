<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 用于对银行配置的操作
 */

namespace app\manage\model;

use think\Model;

class BankModel extends Model{
	//表名
	protected $table = 'ly_bank';

	/**
	 * 添加充值渠道下属银行
	 */
	public function rechargeBankAdd(){
		$param = input('post.');
		//数据验证
		$validate = validate('app\manage\validate\Bank');
		if(!$validate->scene('bankadd')->check($param)){
			//抛出异常
			return $validate->getError();
		}

		$param['add_time'] = time();

		$res = $this->insertGetId($param);

		if(!$res) return '添加失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'添加了银行配置'.$param['bank_name'].'-'.$param['bank_code'],1);

		return 1;
	}

	/**
	 * 编辑充值渠道下属银行
	 */
	public function rechargeBankEdit(){
		$param = input('post.');
		//数据验证
		$validate = validate('app\manage\validate\Bank');
		if(!$validate->scene('bankadd')->check($param)){
			//抛出异常
			return $validate->getError();
		}

		$res = $this->where('id',$param['id'])->update($param);
		if(!$res) return '操作失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'编辑银行配置：'.$param['bank_name'].'-'.$param['bank_code'],1);

		return 1;
	}

	/**
	 * 编辑充值渠道下属银行view
	 */
	public function rechargeBankEditView(){
		$param = input('get.');

		$info = $this->field('ly_bank.*,rechange_type.name')->join('rechange_type','ly_bank.pay_type=rechange_type.id','left')->where('ly_bank.id',$param['id'])->find();

		return array(
			'data'	=>	$info,
		);
	}

	/**
	 * 删除充值渠道下属银行
	 */
	public function rechargeBankDel(){
		if(!request()->isAjax()) return '非法提交';

		$param = input('post.');
		if(!$param) return '提交失败';

		//提取信息备用
		$bankInfo = $this->where('id',$param['id'])->find();

		$delRes = $this->where('id',$param['id'])->delete();
		if(!$delRes) return '删除失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'删除银行配置：'.$bankInfo['bank_name'].'-'.$bankInfo['bank_code'],1);

		return 1;
	}

	/**
	 * 开关
	 */
	public function onOff(){
		if(!request()->isAjax()) return '非法提交';

		$param = input('post.');
		if(!$param) return '提交失败';

		//提取信息备用
		$bankInfo = $this->where('id',$param['id'])->find();

		$res = $this->where('id',$param['id'])->setField($param['field'],$param['val']);

		switch ($param['field']) {
			case 'q_state':
				$fieldName = '取款';
				break;
			case 'c_state':
				$fieldName = '充值';
				break;
		}
		if($param['val']==1){
			$fieldState = '开启';
		}else{
			$fieldState = '关闭';
		}
		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'将'.$bankInfo['bank_name'].'-'.$bankInfo['bank_code'].'的'.$fieldName.$fieldState,1);

		return 1;
	}
}