<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class SlideModel extends Model{
	//表名
	protected $table = 'ly_slide';

	/**
	 * 添加幻灯片
	 */
	public function add(){
		if (!request()->isAjax()) return '提交失败';
		$param = input('param.');

		if (!$param) return '提交失败';

		$insertData = [];
		foreach ($param['img_path'] as $key => $value) {
			$insertData[$key]['img_path'] = $value;
			$insertData[$key]['lang'] = $param['lang'];
		}

		$res = $this->allowField(true)->saveAll($insertData);
		if (!$res) return '提交失败';

		return 1;
	}

	/**
	 * 添加幻灯片
	 */
	public function del(){
		if (!request()->isAjax()) return '提交失败';
		$param = input('param.');
		if (!$param || !isset($param['id']) || !$param['id']) return '提交失败';

		$res = $this->where('id', $param['id'])->delete();
		if (!$res) return '提交失败';

		return 1;
	}

	/**
	 * 设置字段值
	 */
	public function setField(){
		if (!request()->isAjax()) return '提交失败';
		$param = input('param.');
		if (!$param || !isset($param['id']) || !$param['id'] || !isset($param['field']) || !$param['field'] || !isset($param['value']) || !$param['value']) return '提交失败';

		$res = $this->where('id', $param['id'])->setField($param['field'], $param['value']);
		if (!$res) return '提交失败';

		return 1;
	}
}