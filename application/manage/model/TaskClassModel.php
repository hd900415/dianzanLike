<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class TaskClassModel extends Model{
	//表名
	protected $table = 'ly_task_class';

	/**
	 * 添加分类
	 * @return [type] [description]
	 */
	public function TaskClassAdd(){		
		$param = input('post.','','trim');//htmlspecialchars,strip_tags,trim
		if (!$param) return '提交失败';
		
		//数据验证
		// $validate = validate('app\manage\validate\Base');
		// if (!$validate->scene('noticeadd')->check($param)) return $validate->getError();
		
		$param['add_time'] = time();
		
		$res = $this->allowField(true)->save($param);
		if(!$res) return '添加失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'添加了项目分类'.$param['group_name'],1);

		return 1;
	}

	/**
	 * 编辑分类
	 * @return [type] [description]
	 */
	public function TaskClassEdit(){
		$param = input('post.','','trim');//htmlspecialchars,strip_tags,trim
		if (!$param) return '提交失败';
		
		//数据验证
		// $validate = validate('app\manage\validate\Base');
		// if (!$validate->scene('noticeadd')->check($param)) return $validate->getError();
		
		$id = $param['id'];
		unset($param['id']);
		$param['add_time'] = time();

		$res = $this->allowField(true)->save($param, ['id'=>$id]);
		if(!$res) return '修改失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'修改了项目分类'.$param['group_name'],1);

		return 1;
	}

	/**
	 * 删除分类
	 * @return [type] [description]
	 */
	public function TaskClassDel(){
		if(!request()->isAjax()) return '非法提交';

		$param = input('post.');
		if(!$param) return '提交失败';

		//提取信息备用
		$info = $this->where('id',$param['id'])->find();

		$delRes = $this->where('id',$param['id'])->delete();
		if(!$delRes) return '删除失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'删除项目分类：'.$info['group_name'],1);

		return 1;
	}
}