<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class ActivityListModel extends Model{
	//表名
	protected $table = 'ly_activity_list';

	/**
	 * 活动列表
	 */
	public function activityList(){
		//查询符合条件的数据
		$resultData = $this->order(['sort'=>'asc','id'=>'desc'])->paginate(16);

		//权限
		$power = model('ManageUserRole')->getUserPower(array(['uid','=',session('manage_userid')],['cid','=',7]));

		return array(
			'data'		=>	$resultData,
			'page'		=>	$resultData->render(),//分页
			'power'		=>	$power,
		);
	}

	/**
	 * 活动添加
	 */
	public function ActivityListAdd(){
		$param = input('post.','','trim');

		//数据验证
		$validate = validate('app\manage\validate\Activity');
		if(!$validate->scene('activityadd')->check($param)){
			//抛出异常
			return $validate->getError();
		}
		if (isset($param['date_range']) && $param['date_range']) {
			$dateTime = explode(' - ', $param['date_range']);
			$param['start_time'] = strtotime($dateTime[0]);
			$param['end_time'] = strtotime($dateTime[1]);
		}

		if(isset($param['id']) && $param['id']){
			$id = $param['id'];
			unset($param['id']);

			$res = $this->allowField(true)->save($param, ['id'=>$id]);
			if(!$res) return '修改失败';

			//添加操作日志
			model('Actionlog')->actionLog(session('manage_username'),'修改了活动'.$param['title'],1);
		}else{
			$res = $this->allowField(true)->save($param);
			if(!$res) return '添加失败';

			//添加操作日志
			model('Actionlog')->actionLog(session('manage_username'),'添加了活动'.$param['title'],1);
		}

		return 1;
	}

	/**
	 * 活动编辑view
	 */
	public function activityEditView(){
		$param = input('get.');

		$data = $this->where('id',$param['id'])->find();

		return array(
			'data'	=>	$data
		);
	}

	/**
	 * 活动删除
	 */
	public function activityDel(){
		if(!request()->isAjax()) return '非法提交';

		$param = input('post.');
		if(!$param) return '提交失败';

		//提取信息备用
		$info = $this->where('id',$param['id'])->find();

		$delRes = $this->where('id',$param['id'])->delete();
		if(!$delRes) return '删除失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'删除活动：'.$info['title'],1);

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
		$info = $this->where('id',$param['id'])->find();

		$res = $this->where('id',$param['id'])->setField($param['field'],$param['val']);

		if($param['val']==1){
			$fieldState = '开启';
		}else{
			$fieldState = '关闭';
		}
		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'将活动'.$info['title'].'设为'.$fieldState,1);

		return 1;
	}
}