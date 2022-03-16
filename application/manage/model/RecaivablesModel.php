<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 用于对收款账号的操作
 */

namespace app\manage\model;

use think\Model;

class RecaivablesModel extends Model{
	//表名
	protected $table = 'ly_recaivables';

	/**
	 * 收款账号
	 */
	public function receivables(){
		//查询符合条件的数据
		$resultData = $this->field('ly_recaivables.*,bank.bank_name,rechange_type.name as rname')->join('bank','ly_recaivables.bid = bank.id','left')->join('rechange_type','ly_recaivables.type = rechange_type.id','left')->order('ly_recaivables.id','asc')->paginate(15);
		//数据集转数组
		$data = $resultData->toArray()['data'];
		//部分元素重新赋值
		foreach ($data as $key => &$value) {
			$value['open_level'] = ($value['open_level']) ? json_decode($value['open_level']) : array();
		}

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',3],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);	

		return array(
			'data'		=>	$data,
			'page'		=>	$resultData->render(),//分页
			'power'		=>	$power,
		);
	}

	/**
	 * 账号开关
	 */
	public function receivablesOnoff(){
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
		model('Actionlog')->actionLog(session('manage_username'),'将收款账号'.$info['account'].'设为'.$fieldState,1);

		return 1;
	}

	/**
	 * 收款账号删除
	 */
	public function receivablesDel(){
		if(!request()->isAjax()) return '非法提交';

		$param = input('post.');
		if(!$param) return '提交失败';

		//提取信息备用
		$info = $this->where('id',$param['id'])->find();

		$res = $this->where('id',$param['id'])->delete();
		if(!$res) return '操作失败';

		if ($info['qrcode']) unlink('.'.$info['qrcode']);

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'将收款账号'.$info['account'].'删除',1);

		return 1;
	}

	/**
	 * 收款账户添加view
	 */
	public function receivablesAddView(){
		$rechargeList = model('RechangeType')->field('id,name')->select()->toArray();
		$bankList = model('Bank')->field('id,bank_name')->group('bank_name')->select()->toArray();
		
		return array(
			'rechargeList'	=>	$rechargeList,
			'bankList'		=>	$bankList,
		);
	}	

	/**
	 * 收款账户添加
	 */
	public function receivablesAdd(){
		$param = input('post.');
		if(!$param) return '提交失败';

		//数据验证
		$validate = validate('app\manage\validate\Bank');
		if(!$validate->scene('receivablesAdd')->check($param)){
			//抛出异常
			return $validate->getError();
		}

		$res = $this->insertGetId($param);
		if(!$res) return '操作失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'添加收款账号'.$param['account'],1);

		return 1;
	}

	/**
	 * 收款二维码添加view
	 */
	public function receivablesQrcodeAddView(){
		$rechargeList = model('RechangeType')->field('id,name')->select()->toArray();

		return array(
			'rechargeList'	=>	$rechargeList,
		);
	}

	/**
	 * 收款二维码添加
	 * @return [type] [description]
	 */
	public function receivablesQrcodeAdd(){
		$param = input('post.');
		if(!$param) return '提交失败';

		//数据验证
		$validate = validate('app\manage\validate\Bank');
		if(!$validate->scene('receivablesQrcodeAdd')->check([
			'qrcodeType'		=>	$param['type'],
			'qrcodeName'		=>	$param['name'],
			'qrcodeAccount'		=>	$param['account'],
			'qrcode'			=>	$param['qrcode'],
		])){
			//抛出异常
			return $validate->getError();
		}

		$res = $this->allowField(true)->save($param);
		if(!$res) return '操作失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'添加收款二维码'.$param['account'],1);

		return 1;
	}

	/**
	 * 收款账号开放等级
	 * @return [type] [description]
	 */
	public function openLevel(){
		$param = input('post.');
		if(!$param) return '提交失败';

		$info = $this->field('open_level,name')->where('id', $param['id'])->find();
		$openLevelArray = ($info['open_level']) ? json_decode($info['open_level']) : array();

		switch ($param['state']) {
			case 1:
				array_push($openLevelArray, $param['value']);				
				break;
			
			case 2:
				$key = array_search($param['value'], $openLevelArray);
				// unset($openLevelArray[$key]);
				array_splice($openLevelArray, $key, 1);
				break;
		}
		// print_r(json_encode($openLevelArray));die;

		$res = $this->where('id', $param['id'])->setField('open_level', json_encode($openLevelArray));
		if(!$res) return '操作失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'为收款二维码账号'.$info['name'].'开放VIP'.$param['value'], 1);
		
		return 1;
	}

	/**
	 * 收款账号编辑view
	 */
	public function receivablesEditView(){
		$param = input('get.');
		if(!$param) return '提交失败';

		$rechargeList = model('RechangeType')->field('id,name')->select()->toArray();
		$bankList = model('Bank')->field('id,bank_name')->group('bank_name')->select()->toArray();
		$data = $this->where('id', $param['id'])->find();

		return array(
			'rechargeList'	=>	$rechargeList,
			'bankList'		=>	$bankList,
			'data'			=>	$data,
		);
	}

	/**
	 * 收款账号编辑
	 */
	public function receivablesEdit(){
		$param = input('post.');
		if(!$param) return '提交失败';

		//数据验证
		$validate = validate('app\manage\validate\Bank');
		if(!$validate->scene('receivablesAdd')->check($param)){
			//抛出异常
			return $validate->getError();
		}

		$res = $this->where('id', $param['id'])->update($param);
		if (!$res) return '操作失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'编辑收款账号'.$param['account'],1);

		return 1;
	}

	/**
	 * 收款二维码编辑view
	 */
	public function receivablesQrcodeEditView(){
		$param = input('get.');
		if(!$param) return '提交失败';

		$rechargeList = model('RechangeType')->field('id,name')->select()->toArray();
		$data = $this->where('id', $param['id'])->find();

		return array(
			'rechargeList'	=>	$rechargeList,
			'data'			=>	$data,
		);
	}

	/**
	 * 收款二维码编辑
	 */
	public function receivablesQrcodeEdit(){
		$param = input('post.');
		if(!$param) return '提交失败';

		//数据验证
		$validate = validate('app\manage\validate\Bank');
		if(!$validate->scene('receivablesQrcodeAdd')->check([
			'qrcodeType'		=>	$param['type'],
			'qrcodeName'		=>	$param['name'],
			'qrcodeAccount'		=>	$param['account'],
			'qrcode'			=>	$param['qrcode'],
		])){
			//抛出异常
			return $validate->getError();
		}

		$res = $this->where('id', $param['id'])->update($param);
		if (!$res) return '操作失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'编辑收款二维码'.$param['account'],1);

		return 1;
	}
}