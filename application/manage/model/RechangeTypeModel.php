<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class RechangeTypeModel extends Model{
	//表名
	protected $table = 'ly_rechange_type';

	/**
	 * 银行配置
	 */
	public function RechargeList(){
		//查询符合条件的数据
		$RechargeList = $this->field('id,name,type')->order(['type'=>'desc','sort'=>'asc'])->select();
		//获取充值渠道的下属银行列表
		foreach ($RechargeList as $key => &$value) {
			$value['bankList'] = model('Bank')->where('pay_type',$value['id'])->order('id','asc')->select();
		}

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',3],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'data'	=>	$RechargeList,
			'power'	=>	$power,
		);
	}

	/**
	 * 添加充值渠道下属银行view
	 */
	public function rechargeAddView(){
		$param = input('get.');

		$rechargeList = $this->field('id,name')->select();

		return array(
			'rechargeList'	=>	$rechargeList,
			'rid'			=>	$param['rid'],
		);
	}

	/**
	 * 充值渠道
	 */
	public function RechargeType(){
		$param     = input('param.');
		$where     = [];
		$pageParam = [];

		if (isset($param['name']) && $param['name']) {
			$where[]           = ['name','like','%'.$param['name'].'%'];
			$pageParam['name'] = $param['name'];
		}
		if (isset($param['state']) && $param['state']) {
			$where[]           = ['state','=',$param['state']];
			$pageParam['state'] = $param['state'];
		}
		if (isset($param['minPrice']) && $param['minPrice']) {
			$where[]           = ['minPrice','<=',$param['minPrice']];
			$pageParam['minPrice'] = $param['minPrice'];
		}
		if (isset($param['maxPrice']) && $param['maxPrice']) {
			$where[]           = ['maxPrice','>=',$param['maxPrice']];
			$pageParam['maxPrice'] = $param['maxPrice'];
		}
		if (isset($param['mode']) && $param['mode']) {
			$where[]           = ['mode','=',$param['mode']];
			$pageParam['mode'] = $param['mode'];
		}
		if (isset($param['type']) && $param['type']) {
			$where[]           = ['type','=',$param['type']];
			$pageParam['type'] = $param['type'];
		}
		//查询符合条件的数据
		$RechargeList = $this->where($where)->order(['type'=>'desc','sort'=>'asc'])->select()->toArray();

		//部分元素重新赋值
		foreach ($RechargeList as $key => &$value) {
			//获取当日充值总额
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$value['todayTotal'] = model('UserRecharge')->where(array(['dispose_time','>=',$todayStart],['dispose_time','<=',$todayEnd],['state','=',1],['type','=',$value['id']]))->sum('money');
			//充值总额
			$value['allTotal'] = model('UserRecharge')->where(array(['state','=',1],['type','=',$value['id']]))->sum('money');
		}

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',3],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'data'  => $RechargeList,
			'where' => $param,
			'power' => $power,
		);
	}

	/**
	 * 充值渠道添加
	 */
	public function rechargeTypeAdd(){
		$param = input('post.');
		//数据验证
		$validate = validate('app\manage\validate\Bank');
		if(!$validate->scene('rechargeadd')->check($param)){
			//抛出异常
			return $validate->getError();
		}

		$res = $this->allowField(true)->save($param);

		if(!$res) return '添加失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'添加了充值渠道'.$param['name'].'-'.$param['code'],1);

		return 1;
	}

	/**
	 * 充值渠道编辑
	 */
	public function rechargeTypeEdit(){
		$param = input('post.');
		//数据验证
		$validate = validate('app\manage\validate\Bank');
		if(!$validate->scene('rechargeadd')->check($param)){
			//抛出异常
			return $validate->getError();
		}
		$id = $param['id'];
		unset($param['id']);

		$res = $this->allowField(true)->save($param, ['id'=>$id]);
		if(!$res) return '操作失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'编辑充值：'.$param['name'].'-'.$param['code'],1);

		return 1;
	}

	/**
	 * 充值渠道编辑view
	 */
	public function rechargeTypeEditView(){
		$param = input('get.');

		$info = $this->where('id',$param['id'])->find();

		return array(
			'data'	=>	$info,
		);
	}

	/**
	 * 删除充值渠道
	 */
	public function rechargeTypeDel(){
		if(!request()->isAjax()) return '非法提交';

		$param = input('post.');
		if(!$param) return '提交失败';

		//提取信息备用
		$typeInfo = $this->where('id',$param['id'])->find();

		//删除渠道下属银行
		model('Bank')->where('pay_type',$param['id'])->delete();

		$delRes = $this->where('id',$param['id'])->delete();
		if(!$delRes) return '删除失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'删除充值渠道：'.$typeInfo['name'].'-'.$typeInfo['type'],1);

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
		model('Actionlog')->actionLog(session('manage_username'),'将充值渠道'.$info['name'].'设为'.$fieldState,1);

		return 1;
	}
}