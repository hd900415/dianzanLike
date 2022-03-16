<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class DrawConfigModel extends Model{
	//表名
	protected $table = 'ly_draw_config';

	/**
	 * 添加任务
	 */
	public function add(){
		if (!request()->isAjax()) return '提交失败';
		$param = input('param.');
		//数据验证
		// $validate = validate('app\manage\validate\Task');
		// if (!$validate->scene('add')->check($param)) return $validate->getError();

		$res = $this->allowField(true)->save($param);
		if (!$res) return '添加失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'), '添加出款商户'.$param['file_name'], 1);

		return 1;
	}

	/**
	 * 编辑任务
	 */
	public function edit(){

		if (!request()->isAjax()) return '提交失败';

		$param = input('param.');
		//数据验证
		// $validate = validate('app\manage\validate\Task');
		// if (!$validate->scene('add')->check($param)) return $validate->getError();

		$id = $param['id'];
		unset($param['id']);

		$res = $this->allowField(true)->save($param, ['id'=>$id]);
		if (!$res) return '修改失败';
		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'), '编辑出款商户'.$param['file_name'], 1);

		return 1;
	}


	/**
	 * 编辑任务
	 */
	public function del(){
		if (!request()->isAjax()) return '提交失败';
		$param = input('param.');
		if (!$param) return '提交失败';

		if (isset($param['data']) && $param['data']) { // 批量删除
			foreach ($param['data'] as $key => $value) {
				$res[] = $this->where('id', $value['id'])->delete();
			}
		} elseif (isset($param['id']) && $param['id']) { // 删除单个
			$res = $this->where('id', $param['id'])->delete();
			if (!$res) return '删除失败';
		} else {
			return '提交失败';
		}

		return 1;
	}

	/**
	 * 开关
	 * @return [type] [description]
	 */
	public function switch(){
		if (!request()->isAjax()) return '提交失败';
		$param = input('param.');
		if (!$param) return '提交失败';

		// 关闭全部出款商户
		if ($param['field'] == 'state' && $param['val'] == 1) $this->where('id', '>', 0)->setField('state', 2);
		//更新
		$res = $this->where('id', $param['id'])->setField($param['field'], $param['val']);
		if (!$res) return '操作失败';

		return 1;
	}

	/**
	 * 出款设置
	 */
	public function setPayment(){
		$param = input('post.');
		if(!$param) return '提交失败';

		if(!isset($param['safe_code']) || !$param['safe_code']) return '请输入安全码';

		$safeCode = model('Manage')->where('id', session('manage_userid'))->value('safe_code');
		if (auth_code($safeCode, 'DECODE') != $param['safe_code']) return '安全码错误';

		// 可提现状态
		model('Setting')->where('id','>',0)->update(['cash_status'=>$param['cash_status']]);
		// 关闭所有商户
		$res = $this->where('id','>',0)->update(['state'=>2]);
		// 开启当前商户
		$res2 = $this->where('id',$param['paymentMerchant'])->update(['state'=>1]);

		if (!$res || !$res2) return '修改失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'修改出款设置',1);

		return 1;
	}
}