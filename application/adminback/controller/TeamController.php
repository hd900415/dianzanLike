<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\controller;

use app\admin\controller\Common;

class TeamController extends CommonController{
	/**
	 * 空操作处理
	 */
	public function _empty(){
		return $this->index();
	}
	/**
	 * 团队成员
	 * @return [type] [description]
	 */
	public function teamInfo(){
		// 获取参数
		$param = input('get.');
		// 分页参数组装
		$pageParam = array();
		// 查询条件
		$where[] = ['user_team.uid','=',session('admin_userid')];
		//用户名搜索
		if(isset($param['username']) && $param['username']){
			$where[] = array('ly_users.username','like','%'.trim($param['username']).'%');
			// $where[] = array('username','=',$param['username']);
			$pageParam['username'] = $param['username'];
		}
		// 用户状态
		if(isset($param['state']) && $param['state']){
			$where[] = array('ly_users.state','=',$param['state']);
			$pageParam['state'] = $param['state'];
		}
		//用户注册时间搜索
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('ly_users.reg_time','>=',strtotime($dateTime[0]));
			$where[] = array('ly_users.reg_time','<=',strtotime($dateTime[1]));
			// $pageParam['datetime'] = $param['datetime'];
			$pageUrl .= '&datetime='.$param['datetime'];
		}
		// 查询数据
		$resultData = model('Users')->field('ly_users.id,ly_users.username,ly_users.state,user_total.balance,user_total.frozen_balance')->join('user_team','ly_users.id=user_team.team')->join('user_total','ly_users.id=user_total.uid')->where($where)->order('ly_users.id','asc')->paginate(15,false,['query'=>$pageParam]);

		return view('', [
			'where' => $param,
			'data'  => $resultData->toArray()['data'], // 数据
			'page'  => $resultData->render(), // 分页
		]);
	}

	/**
	 * 团队成员二维码列表
	 * @return [type] [description]
	 */
	public function userQrcode(){
		// 获取参数
		$id = input('get.id/d');
		if (!$id) return view('', ['data'=>[]]);

		$data = model('Qrcode')->where('uid', $id)->select()->toArray();
		foreach ($data as $key => &$value) {
			$value['qrcodeurl'] = ltrim($value['qrcodeurl'], '.');
		}

		return view('', [
			'data' => $data
		]);
	}

	/**
	 * 团队流水
	 * @return [type] [description]
	 */
	public function teamTrade(){
		$param = input('get.');

		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		// 查询条件
		$where[] = array('user_team.uid','=',session('admin_userid'));
		$where[] = array('ly_trade_details.types','=',1);

		//用户名搜索
		if(isset($param['username']) && $param['username']){
			$where[] = array('ly_trade_details.username','like','%'.trim($param['username']).'%');
			// $where[] = array('username','=',$param['username']);
			$pageParam['username'] = $param['username'];
		}
		//交易类型
		if(isset($param['trade_type']) && $param['trade_type']){
			$where[] = array('ly_trade_details.trade_type','=',$param['trade_type']);
			$pageParam['trade_type'] = $param['trade_type'];
		}
		//交易金额
		if(isset($param['price1']) && $param['price1']){
			$where[] = array('ly_trade_details.trade_amount','>=',$param['price1']);
			$pageParam['price1'] = $param['price1'];
		}
		//交易金额
		if(isset($param['price2']) && $param['price2']){
			$where[] = array('ly_trade_details.trade_amount','<=',$param['price2']);
			$pageParam['price2'] = $param['price2'];
		}
		//时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('ly_trade_details.trade_time','>=',strtotime($dateTime[0]));
			$where[] = array('ly_trade_details.trade_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('ly_trade_details.trade_time','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('ly_trade_details.trade_time','<=',$todayEnd);
		}

		//查询符合条件的数据
		$resultData = model('TradeDetails')->join('user_team','ly_trade_details.uid=user_team.team')->where($where)->order(['ly_trade_details.trade_time'=>'desc','ly_trade_details.id'=>'desc'])->paginate(15,false,['query'=>$pageParam]);
		//数据集转数组
		$tradeList = $resultData->toArray()['data'];
		//部分元素重新赋值
		$tradeType   = config('custom.transactionType');//交易类型
		$orderStates = config('custom.orderStates');
		$orderColor  = config('manage.color');
		$adminColor  = config('manage.adminColor');
		foreach ($tradeList as $key => &$value) {
			$value['tradeType']      = $tradeType[$value['trade_type']];
			$value['tradeTypeColor'] = $adminColor[$value['trade_type']];
			$value['statusStr']      = config('custom.tradedetailsStatus')[$value['state']];
			$value['statusColor']    = $orderColor[$value['state']];
			$value['front_type_str'] = config('custom.front_type')[$value['front_type']];
			$value['payway_str']     = config('custom.payway')[$value['payway']];
		}

		return view('', [
			'data'      =>	$tradeList,
			'page'      =>	$resultData->render(),//分页
			'where'     =>	$param,
			'tradeType' =>	$tradeType,
		]);
	}

	/**
	 * 团队订单
	 * @return [type] [description]
	 */
	public function order(){
		// 获取参数
		$param = input('get.');
		// 查询条件组装
		$where = array();
		// 分页参数组装
		$pageParam = array();
		// 查询条件
		$where[]  = array('user_team.uid','=',session('admin_userid'));
		
		// 商户
		if(isset($param['merchant']) && $param['merchant']){
			$merchantId = model('Merchant')->where('username', 'like', '%'.trim($param['merchant']).'%')->value('id');
			$where[]    = array('ly_order.mid','=',$merchantId);
			$pageParam['merchant'] = $param['merchant'];
		}
		// 订单号
		if(isset($param['order1']) && $param['order1']){
			$where[] = array('ly_order.orderid','=',$param['order1']);
			$pageParam['order1'] = $param['order1'];
		}
		// 商户订单号
		if(isset($param['order2']) && $param['order2']){
			$where[] = array('ly_order.jorderid','=',$param['order2']);
			$pageParam['order2'] = $param['order2'];
		}
		// 订单状态
		if(isset($param['state']) && $param['state']){
			$where[] = array('ly_order.status','=',$param['state']);
			$pageParam['state'] = $param['state'];
		}
		// 支付方式
		if(isset($param['payType']) && $param['payType']){
			$where[] = array('ly_order.payway','=',$param['payType']);
			$pageParam['payType'] = $param['payType'];
		}
		// 时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('ly_order.ordertimes','>=',strtotime($dateTime[0]));
			$where[] = array('ly_order.ordertimes','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('ly_order.ordertimes','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('ly_order.ordertimes','<=',$todayEnd);
		}

		$where2 = $where;
		// 用户名
		if(isset($param['username']) && $param['username']){
			$userId   = model('Users')->where('username', 'like', '%'.trim($param['username']).'%')->value('id');
			$where[]  = array('ly_order.uid','=',$userId);
			$where2[] = array('ly_order.juid','=',$userId);
			$pageParam['username'] = $param['username'];
		}

		//查询符合条件的数据
		$resultData = model('Order')->field('ly_order.*')
					->join('user_team','ly_order.uid=user_team.team')
					->whereOr([$where, $where2])
					->order(['ly_order.ordertimes'=>'desc','ly_order.id'=>'desc'])
					->paginate(15,false,['query'=>$pageParam]);
		// 数据集转数组
		$data = $resultData->toArray()['data'];
		// 部分元素重新赋值
		$transactionType = config('custom.transactionType');	//交易类型
		$orderStates     = config('custom.orderStates');	// 订单状态
		$payway          = config('custom.payway');	// 支付方式
		$orderColor      = config('manage.color');
		foreach ($data as $key => &$value) {
			$value['ordertypeStr'] = $transactionType[$value['ordertype']];
			$value['statusStr']    = $orderStates[$value['status']];
			$value['statusColor']  = $orderColor[$value['status']];
			$value['mName']        = ($value['bitype'] == 2) ? model('Merchant')->where('id', $value['mid'])->value('username') : model('Users')->where('id', $value['juid'])->value('username');
			$value['uName']        = model('Users')->where('id', $value['uid'])->value('username');
		}

		return view('', [
			'data'        => $data,
			'page'        => $resultData->render(),//分页
			'where'       => $param,
			'orderStates' => $orderStates,
			'payway'      => $payway
		]);
	}

	/**
	 * 补单
	 * @return [type] [description]
	 */
	public function repairorder(){
		if (request()->isAjax()) {
			// 添加操作日志
			model('Actionlog')->actionLog(session('admin_username'), '补单：'.input('post.orderid/s'), 3);

			return model('manage/Order')->repairorder();
		}

		//获取参数
		$param     = input('get.');
		//获取订单数据
		$orderInfo = model('Order')->where('id',$param['id'])->find();
		// if (is_object($orderInfo)) $orderInfo = $orderInfo->toArray();
		//部分数据重新赋值
		$orderInfo['statusStr']    = config('custom.orderStates')[$orderInfo['status']];
		$orderInfo['ordertypeStr'] = config('custom.transactionType')[$orderInfo['ordertype']];
		//获取用户信息
		if ($orderInfo['uid']) $orderInfo['uName'] = model('Users')->where('id', $orderInfo['uid'])->value('username');
		if ($orderInfo['mid']) $orderInfo['mName'] = model('Merchant')->where('id', $orderInfo['mid'])->value('username');

		return view('', [
			'data' => $orderInfo
		]);
	}

	/**
	 * 回调
	 */
	public function callBack(){
		$id = input('post.id/d');
		$order = model('Order')->where('id', $id)->findOrEmpty();
		if (!$order) return '订单不存在';

		$callBackData = array(
			'uid'              => $order['uid'],
			'merchantId'       => model('Merchant')->where('id', $order['mid'])->value('merchantid'),
			'timestamp'        => $order['timestamp'],
			'signatureMethod'  => 'HmacSHA256',
			'signatureVersion' => 1,
			'orderId'          => $order['orderid'],
			'status'           => 3,
			'jOrderId'         => $order['jorderid'],
			'notifyUrl'        => base64_decode($order['notifyurl']),
			'orderType'        => $order['ordertype'],
			'amount'           => $order['oamount'],
			'currency'         => $order['currency'],
			'actualAmount'     => $order['oactualamount'],
			'fee'              => $order['feeamount'],
			'payWay'           => $order['payway'],
			'payTime'          => $order['paytimes'],
			'jExtra'           => base64_decode($order['jextra']),
			'mkey'             => $order['mkey'],
		);
		model('api/Order')->Callback($callBackData);

		// 添加操作日志
		model('Actionlog')->actionLog(session('admin_username'), '回调订单：'.$order['orderid'], 3);

		return 1;
	}

	/**
	 * 二维码锁定/解锁
	 * @return [type] [description]
	 */
	public function qrcodeStatus(){
		if (!request()->isAjax()) return '非法提交';
		$param = input('post.');
		if (!$param) return '提交失败';
		if (!isset($param['id']) || !$param['id']) return '提交失败';
		if (!isset($param['value']) || !$param['value']) return '提交失败';

		$res = model('Qrcode')->where('id', $param['id'])->setField('status', $param['value']);
		if (!$res) return '更新失败';

		return 1;
	}

	/**
	 * 二维码启用/禁用
	 * @return [type] [description]
	 */
	public function qrcodeDisabled(){
		if (!request()->isAjax()) return '非法提交';
		$param = input('post.');
		if (!$param) return '提交失败';
		if (!isset($param['id']) || !$param['id']) return '提交失败';
		if (!isset($param['value']) || !$param['value']) return '提交失败';

		$res = model('Qrcode')->where('id', $param['id'])->setField('enable', $param['value']);
		if (!$res) return '更新失败';

		return 1;
	}

	/**
	 * 团队报表
	 * @return [type] [description]
	 */
	public function teamReport(){
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageUrl = '';
		//用户名搜索
		$sid = session('admin_userid');
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','like','%'.trim($param['username']).'%');
			$pageUrl .= '&username='.$param['username'];
		}else{
			//查看下级
			if(isset($param['sid']) && $param['sid']){
				$idOne = model('Users')->where('id',$param['sid'])->value('sid');
				$idtwo = model('Users')->where('id',$idOne)->value('id');
				$param['id'] = $idtwo;
				$pageUrl .= '&sid='.$param['sid'];
			}
			//查看上级
			if(isset($param['id']) && $param['id']){
				$where[] = array('sid','=',$param['id']);
				$sid = $param['id'];
				$pageUrl .= '&id='.$param['id'];
			}else{
				$where[] = array('sid','=',$sid);
			}
		}
		// 时间
		if(isset($param['date_range']) && $param['date_range']){
			$dateTime  = explode(' - ', $param['date_range']);
			$startDate = strtotime($dateTime[0]);
			$endDate   = strtotime($dateTime[1]);
			$pageUrl   .= '&date_range='.$param['date_range'];
		}else{
			$startDate           = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$endDate             = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$param['date_range'] = date('Y-m-d', $startDate).' - '.date('Y-m-d', $endDate);
		}
		//查询符合条件的数据
		$userObj = model('Users')->field('id,sid,username');
		($sid == session('admin_userid')) ? $userObj->whereOr(array(['sid','=',$sid],['id','=',$sid])) : $userObj->where($where);
		$userList = $userObj->select()->toArray();
		//用户团队数据计算
		$data              = model('manage/UserDaily')->teamStatistic($userList,$startDate,$endDate,$sid);
		$total['totalAll'] = $data['totalAll'];
		unset($data['totalAll']);
		//分页
		$pageNum  = isset($param['page']) && $param['page'] ? $param['page'] : 1 ;
		$pageInfo = model('ArrPage')->page($data, 15, $pageNum, $pageUrl);
		$page     = $pageInfo['links'];
		$source   = $pageInfo['source'];

		// 分页小计
		$sumField = array('recharge','withdrawal','order','giveback','fee','commission','activity','recovery','rob','buy','sell');
		foreach ($sumField as $key => &$value) {
			$total['totalPage'][$value] = 0;
			foreach ($source as $k => $v) {
				$total['totalPage'][$value] += $v[$value];
			}
		}
		
		return view('', [
			'data'   =>	$source,
			'total'  =>	$total,
			'page'   =>	$page,//分页
			'where'  =>	$param,
			'userId' => session('admin_userid')
		]);
	}

	/**
	 * 团队每日
	 * @return [type] [description]
	 */
	public function teamDaily(){
		// 获取参数
		$param = input('get.');
		// 分页参数组装
		$pageUrl = "";
		// 查询条件定义
		$where = array();
		// 用户名
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','=','%'.trim($param['username']).'%');
			$pageUrl .= '&username='.$param['username'];
		}
		// 时间搜索
		if(isset($param['date_range']) && $param['date_range']){
			$dateTime  = explode(' - ', $param['date_range']);
			$startDate = strtotime($dateTime[0]);
			$endDate   = strtotime($dateTime[1]);
			$where[]   = array('date', '>=', $startDate);
			$where[]   = array('date', '<=', $endDate);
			$pageUrl   .= '&date_range='.$param['date_range'];
		} else {
			$startDate = mktime(0,0,0,date('m'),date('d'),date('Y')) - 86400 * 7;
			$endDate   = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[]   = array('date', '>=', $startDate);
			$where[]   = array('date', '<=', $endDate);
			$param['date_range'] = date('Y-m-d',$startDate).' - '.date('Y-m-d',$endDate);
		}

		if (isset($param['username']) && $param['username']) {
			$data = model('UserDaily')->where($where)->order('date','desc')->select()->toArray();
		} else {
			$data = array();
			$day = ($endDate - $startDate)/86400;
			for ($i=0; $i <= $day; $i++) { 
				$data[$i] = model('UserDaily')->field([
					'SUM(`recharge`)'   => 'recharge',
					'SUM(`withdrawal`)' => 'withdrawal',
					'SUM(`order`)'      => 'order',
					'SUM(`rebate`)'     => 'rebate',
					'SUM(`fee`)'        => 'fee',
					'SUM(`commission`)' => 'commission',
					'SUM(`activity`)'   => 'activity',
					'SUM(`recovery`)'   => 'recovery',
					'SUM(`rob`)'        => 'rob',
					'SUM(`buy`)'        => 'buy',
					'SUM(`sell`)'       => 'sell',
					'SUM(`giveback`)'   => 'giveback',
				])->where('date', $endDate)->find()->toArray();
				
				$data[$i]['date'] = $endDate;
				$endDate -= 86400;
			}
		}
		
		$decimalPlace = config('manage.decimalPlace');	// 获取小数保留位数
		foreach ($data as $key => &$value) {
			// 小数位数
			foreach ($value as $k => $v) {
				if ($k != 'date') $data[$key][$k] = round($v, $decimalPlace);
			}
		}

		//全部合计
		$sumField = array('recharge','withdrawal','order','rebate','fee','commission','activity','recovery','rob','buy','sell','giveback');
		foreach ($sumField as $key => &$value) {
			$totalAll[$value] = 0;
			foreach ($data as $k => $v) {
				$totalAll[$value] += $v[$value];
			}
		}
		
		//分页
		$pageNum  = isset($param['page']) && $param['page'] ? $param['page'] : 1 ;
		$pageInfo = model('ArrPage')->page($data, 15, $pageNum, $pageUrl);
		$page     = $pageInfo['links'];
		$source   = $pageInfo['source'];
		
		//本页总计
		foreach ($sumField as $key => &$value) {
			$totalPage[$value] = 0;
			foreach ($source as $k => $v) {
				$totalPage[$value] += $v[$value];
			}
		}

		return view('', [
			'data'		=>	$source,
			'page'		=>	$page,
			'where'		=>	$param,
			'totalAll'	=>	$totalAll,
			'totalPage'	=>	$totalPage,
		]);
	}
}