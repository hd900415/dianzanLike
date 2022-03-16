<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class UserRechargeModel extends Model{
	//表名
	protected $table = 'ly_user_recharge';

	/**
	 * 充值记录
	 */
	public function rechargeList(){
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();

		//用户名搜索
		if(isset($param['username']) && $param['username']){
			$uid = model('Merchant')->where('username', trim($param['username']))->value('id');
			$where[] = array('uid','=',$uid);
			$pageParam['username'] = $param['username'];
		}
		//订单号搜索
		if(isset($param['order_number']) && $param['order_number']){
			$where[] = array('order_number','=',trim($param['order_number']));
			$pageParam['order_number'] = $param['order_number'];
		}
		//状态搜索
		if(isset($param['state']) && $param['state']){
			$where[] = array('state','=',$param['state']);
			$pageParam['state'] = $param['state'];
		}
		// 时间
		if(isset($param['datetime_range']) && $param['datetime_range']){
			$dateTime = explode(' - ', $param['datetime_range']);
			$where[] = array('add_time','>=',strtotime($dateTime[0]));
			$where[] = array('add_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime_range'] = $param['datetime_range'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('add_time','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('add_time','<=',$todayEnd);
		}

		//查询符合条件的数据
		// $resultData = $this->field('ly_user_recharge.*,users.username,recaivables.name,account,bank')->join('Recaivables','ly_user_recharge.actualamount = recaivables.id')->join('users','ly_user_recharge.uid = users.id')->where($where)->order('ly_user_recharge.add_time','desc')->paginate(15,false,['query'=>$pageParam]);
		$resultData = $this->field('ly_user_recharge.*,users.username')->join('users','ly_user_recharge.uid = users.id')->where($where)->order('ly_user_recharge.add_time','desc')->paginate(15,false,['query'=>$pageParam]);

		$stateColor = config('manage.color');
		$pageTotal['countMoney'] = 0;
		foreach ($resultData as $key => &$value) {
			// 状态颜色
			$value['stateColor'] = $stateColor[$value['state']];
			//分页统计
			$pageTotal['countMoney'] += $value['amount'];
		}

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',3],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'data'			=>	$resultData->toArray()['data'],
			'page'			=>	$resultData->render(),//分页
			'pageTotal'		=>	$pageTotal,
			'where'			=>	$pageParam,
			'power'			=>	$power,
		);
	}

	/**
	 * 充值订单审核view
	 */
	public function rechargeDisposeView(){
		$param = input('get.');

		$data = $this->field('ly_user_recharge.*,rechange_type.name')->join('rechange_type','ly_user_recharge.type=rechange_type.id')->where('ly_user_recharge.id',$param['id'])->find();
		// ->join('recaivables','ly_user_recharge.rid=recaivables.id','left')
		
		if ($data['daozhang_money'] <= 0) {
		    $data['daozhang_money'] = $data['money'];
		}
		
		return array(
			'data'	=>	$data
		);
	}

	/**
	 * 充值订单处理
	 */
	public function rechargeDispose(){
		$param = input('post.');
		if(!$param) return '提交失败';

		$controlAuditTime = cache('CA_rechargeDisposeTime'.session('manage_userid')) ? cache('CA_rechargeDisposeTime'.session('manage_userid')) : time()-2;
		if(time() - $controlAuditTime < 2){
			return ' 2 秒内不能重复提交';
		}
		cache('CA_rechargeDisposeTime'.session('manage_userid'), time(), 10);

		$orderNumber = $param['order_number'];
		unset($param['order_number']);
		$param['aid'] = session('manage_userid');
		$param['dispose_time'] = time();
		$res = $this->where(array(['order_number','=',$orderNumber],['state','=',3]))->update($param);
		if(!$res) return '操作失败1';

		if($param['state'] == 1){




			//获取订单信息
			$orderInfo = $this->field('uid,money,order_number')->where('order_number',$orderNumber)->find();

            if ($param['daozhang_money']) {
                $orderInfo['money'] = $param['daozhang_money'];
            }
			//获取用户余额
			$balance = model('UserTotal')->field('balance')->where('uid',$orderInfo['uid'])->find();
			//更新用户金额信息
			$res2 = model('UserTotal')->where('uid',$orderInfo['uid'])->inc('total_recharge',$orderInfo['money'])->inc('balance',$orderInfo['money'])->update();
			if(!$res2) return '操作失败2';

			$tradeDetailsArray = array(
				'uid'					=>	$orderInfo['uid'],
				'order_number'			=>	$orderInfo['order_number'],
				'trade_type'			=>	1,
				'trade_before_balance'	=>	$balance['balance'],
				'trade_amount'			=>	$orderInfo['money'],
				'account_balance'		=>	$balance['balance'] + $orderInfo['money'],
				'remarks'				=>	'订单 '.$orderInfo['order_number'].' 充值成功，充值资金：'.$orderInfo['money'] . ', 到账金额：'.$param['daozhang_money'],
				'isadmin'				=>	1,
			);
			$res3 = model('common/TradeDetails')->tradeDetails($tradeDetailsArray);
			if(!$res3) return '操作失败3';
		}

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'处理订单号为'.$orderNumber.'的充值订单',1);

		return 1;
	}
}