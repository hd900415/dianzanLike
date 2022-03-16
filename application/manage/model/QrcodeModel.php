<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class QrcodeModel extends Model{
	//表名
	protected $table = 'ly_qrcode';

	public function qrcodeList(){
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		//用户名搜索
		if(isset($param['username']) && $param['username']){
			// $where[] = array('username','like','%'.trim($param['username']).'%');
			$where[] = array('users.username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		// 收款次数
		if(isset($param['callnumber_min']) && $param['callnumber_min']){
			$where[] = array('callnumber','>=',trim($param['callnumber_min']));
			$pageParam['callnumber_min'] = $param['callnumber_min'];
		}
		// 收款次数
		if(isset($param['callnumber_max']) && $param['callnumber_max']){
			$where[] = array('callnumber','<=',trim($param['callnumber_max']));
			$pageParam['callnumber_max'] = $param['callnumber_max'];
		}
		// 状态
		if(isset($param['status']) && $param['status']){
			$where[] = array('status','=',$param['status']);
			$pageParam['status'] = $param['status'];
		}
		//用户注册时间搜索
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('ly_qrcode.reg_time','>=',strtotime($dateTime[0]));
			$where[] = array('ly_qrcode.reg_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}
		//查询符合条件的数据
		$resultData = $this->field('ly_qrcode.*,users.username')->join('users','ly_qrcode.uid = users.id')->where($where)->order('ly_qrcode.reg_time','desc')->paginate(15,false,['query'=>$pageParam]);
		foreach ($resultData as $key => &$value) {
			$value['qrcodeurl'] = ltrim($value['qrcodeurl'], '.');
		}
		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',2],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'where'      =>	$pageParam,
			'qrcodeList' =>	$resultData->toArray()['data'],//数据
			'page'       =>	$resultData->render(),//分页
			'power'      =>	$power,//权限
		);
	}

	/**
	 * 修改单个字段
	 */
	public function setFieldValue(){
		$param = input('post.');//获取参数
		if (!$param || !isset($param['uid']) || !isset($param['username']) || !isset($param['value'])) return '提交失败';

		//提取信息
		$userName = $this->where('id', '=', $param['uid'])->value('codename');

		$updateArr = [$param['username'] => $param['value']];
		if ($param['username'] == 'status' && $param['value'] == 2) $updateArr['enable'] = 0;

		//更新
		$res = $this->where('id', '=', $param['uid'])->update($updateArr);
		if (!$res) return '操作失败';
		
		switch ($param['username']) {
			case 'enable':
				$logStr = $param['value']==2 ? '非' : '';
				$logContent = $userName.'设为'.$logStr.'启用';
				break;
			case 'status':
				$logStr = $param['value']==2 ? '非' : '';
				$logContent = $userName.'设为'.$logStr.'锁定';
				break;
		}

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'), '将二维码'.$logContent, 1);

		return 1; 
	}
}