<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 代理迁移相关操作
 */

namespace app\manage\model;

use think\Model;

class TeammoveLogModel extends Model{
	//表名
	protected $table = 'ly_teammove_log';

	/**
	 * 代理迁移视图
	 */
	public function teamMoveView(){
		//查询符合条件的数据
		$resultData = $this->field('ly_teammove_log.*,manage.username')->join('manage','ly_teammove_log.aid = manage.id','left')->order('addtime','desc')->paginate(10);
		//数据集转数组
		$teammoveLog = $resultData->toArray()['data'];
		foreach ($teammoveLog as $key => &$value) {
			$value['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
		}
		return array(
			'teammoveLog'	=>	$teammoveLog,
			'page'			=>	$resultData->render(),//分页
		);
	}
}