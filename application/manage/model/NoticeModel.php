<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class NoticeModel extends Model{
	//表名
	protected $table = 'ly_notice';

	/**
	 * 公告
	 */
	public function noticeList(){
		//查询符合条件的数据
		$resultData = $this->order(['add_time'=>'desc','id'=>'desc'])->paginate(16);
		
		//清除缓存
		cache('C_noticedata',NULL);

		//权限
		$power = model('ManageUserRole')->getUserPower(array(['uid','=',session('manage_userid')],['cid','=',1]));

		return array(
			'data'			=>	$resultData,
			'page'			=>	$resultData->render(),//分页
			'power'			=>	$power,
		);
	}

	/**
	 * 公告添加
	 */
	public function noticeAdd(){
		$param = input('post.','','trim');//htmlspecialchars,strip_tags,trim

		//数据验证
		$validate = validate('app\manage\validate\Base');
		if(!$validate->scene('noticeadd')->check($param)){
			//抛出异常
			return $validate->getError();
		}

		if(isset($param['id']) && $param['id']){
			$id = $param['id'];
			unset($param['id']);

			$res = $this->allowField(true)->save($param, ['id'=>$id]);
			if(!$res) return '修改失败';

			//添加操作日志
			model('Actionlog')->actionLog(session('manage_username'),'修改了公告'.$param['title'],1);
		}else{
			$param['add_time'] = time();
			
			$res = $this->allowField(true)->save($param);
			if(!$res) return '添加失败';

			//添加操作日志
			model('Actionlog')->actionLog(session('manage_username'),'添加了公告'.$param['title'],1);
		}

		return 1;
	}

	/**
	 * 公告编辑view
	 */
	public function noticeEditView(){
		$param = input('get.');

		$data = $this->where('id',$param['id'])->find();
		$noticeGroup = model('NoticeGroup')->field('id,group_name')->select();

		return array(
			'data'			=>	$data,
			'noticeGroup'	=>	$noticeGroup
		);
	}

	/**
	 * 公告删除
	 */
	public function noticeDel(){
		if(!request()->isAjax()) return '非法提交';

		$param = input('post.');
		if(!$param) return '提交失败';

		//提取信息备用
		$info = $this->where('id',$param['id'])->find();

		$delRes = $this->where('id',$param['id'])->delete();
		if(!$delRes) return '删除失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'删除公告：'.$info['title'],1);

		return 1;
	}
}