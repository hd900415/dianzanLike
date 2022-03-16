<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class IpWhiteModel extends Model{
	//表名
	protected $table = 'ly_ip_white';

	/**
	 * IP后台IP白名单
	 */
	public function ipWhite(){
		//查询符合条件的数据
		$resultData = $this->order('id','desc')->paginate(16);

		//权限
		$power = model('ManageUserRole')->getUserPower(array(['uid','=',session('manage_userid')],['cid','=',1]));

		return array(
			'data'			=>	$resultData,
			'page'			=>	$resultData->render(),//分页
			'power'			=>	$power,
		);
	}

	/**
	 * IP白名单添加
	 */
	public function ipWhiteAdd(){
		$param = input('post.');

		//数据验证
		$validate = validate('app\manage\validate\Base');
		if(!$validate->scene('ipwhiteadd')->check($param)){
			//抛出异常
			return $validate->getError();
		}

		$res = $this->insertGetId($param);
		if(!$res) return '添加失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'添加了IP白名单'.$param['ip'],1);

		return 1;
	}

	/**
	 * IP删除
	 */
	public function ipWhiteDel(){
		if(!request()->isAjax()) return '非法提交';

		$param = input('post.');
		if(!$param) return '提交失败';

		//提取信息备用
		$info = $this->where('id',$param['id'])->find();

		$delRes = $this->where('id',$param['id'])->delete();
		if(!$delRes) return '删除失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'从后台白名单删除IP：'.$info['ip'],1);

		return 1;
	}
}