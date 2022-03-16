<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class NoticeGroupModel extends Model{
	//表名
	protected $table = 'ly_notice_group';
	
	/**
	 * 公告分类列表
	 * @return [type] [description]
	 */
	public function groupList(){
		//查询符合条件的数据
		$resultData = $this->order(['addtime'=>'desc','id'=>'desc'])->paginate(16);
		
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
	 * 公告分类添加（编辑）
	 * @return [type] [description]
	 */
	public function groupAdd(){
		$param = input('post.');

		//数据验证
		$validate = validate('app\manage\validate\Base');
		if(!$validate->scene('groupadd')->check($param)){
			//抛出异常
			return $validate->getError();
		}

		if(isset($param['id']) && $param['id']){
			$id = $param['id'];
			unset($param['id']);

			$res = $this->where('id',$id)->update($param);
			if(!$res) return '修改失败';

			//添加操作日志
			model('Actionlog')->actionLog(session('manage_username'),'修改了公告分类'.$param['group_name'],1);
		}else{
			$param['addtime'] = time();

			$res = $this->insertGetId($param);
			if(!$res) return '添加失败';

			//添加操作日志
			model('Actionlog')->actionLog(session('manage_username'),'添加了公告分类'.$param['group_name'],1);
		}

		return 1;
	}

	/**
	 * 公告分类删除
	 */
	public function groupDel(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';

		$info = $this->where('id',$param['id'])->find();

		$delRes = $this->where('id','=',$param['id'])->delete();
		if(!$delRes) return '删除失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'删除公告分类'.$info['group_name'],1);

		return 1;
	}
}