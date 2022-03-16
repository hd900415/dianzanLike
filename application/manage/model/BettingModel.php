<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 作用：投注记录数据表相关操作
 */

namespace app\manage\model;

use think\Model;

class BettingModel extends Model{
	//表名
	protected $table = 'ly_betting';

	/**
	 * 投注列表
	 */
	public function betList(){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();

		//用户名
		if(isset($param['username']) && $param['username']){
			$where[] = array('ly_betting.username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		//彩种
		$lotteryPlay = array();
		if(isset($param['lottery_type']) && $param['lottery_type']){
			$where[] = array('lottery_type','=',$param['lottery_type']);
			$pageParam['lottery_type'] = $param['lottery_type'];
			//玩法处理
			$thirdPlay = model('PlayGame')->field('pid,game_name,name')->where(array(array('class','=',$param['lottery_type']),array('isopen','=',1)))->select();
			foreach ($thirdPlay as $playKey => $playValue) {
				$secondPlay = model('PlayGame')->field('pid,game_name')->where(array(['class','=',$param['lottery_type']],['basics_id','=',$playValue['pid']]))->find();
				if($secondPlay['pid']){
						$lotteryPlay[$playValue['name']] = model('PlayGame')->where(array(['class','=',$param['lottery_type']],['basics_id','=',$secondPlay['pid']]))->value('game_name').'-'.$secondPlay['game_name'].'-'.$playValue['game_name'];

				}elseif($secondPlay['game_name']){
					$lotteryPlay[$playValue['name']] = $secondPlay['game_name'].'-'.$playValue['game_name'];
				}else{
					$lotteryPlay[$playValue['name']] = $playValue['game_name'];
				}
			}
		}
		//玩法
		if(isset($param['play']) && $param['play']){
			$where[] = array('play','=',$param['play']);
			$pageParam['play'] = $param['play'];
		}
		//状态
		if(isset($param['state']) && $param['state']){
			$where[] = array('ly_betting.state','=',$param['state']);
			$pageParam['state'] = $param['state'];

			if($param['state']==3){
				if(isset($param['price1']) && $param['price1']){
					$where[] = array('bonus','>=',trim($param['price1']));
					$pageParam['price1'] = $param['price1'];
				}
				if(isset($param['price2']) && $param['price2']){
					$where[] = array('bonus','>=',trim($param['price2']));
					$pageParam['price2'] = $param['price2'];
				}
			}
		}
		//危险账户
		if(isset($param['danger']) && $param['danger']){
			$where[] = array('danger','=',$param['danger']);
			$pageParam['danger'] = $param['danger'];
		}
		//IP
		if(isset($param['ip']) && $param['ip']){
			$where[] = array('ip','=',trim($param['ip']));
			$pageParam['ip'] = $param['ip'];
		}
		//订单号
		if(isset($param['order_number']) && $param['order_number']){
			$where[] = array('order_number','=',trim($param['order_number']));
			$pageParam['order_number'] = $param['order_number'];
		}
		//期号
		if(isset($param['no']) && $param['no']){
			$where[] = array('no','=',trim($param['no']));
			$pageParam['no'] = $param['no'];
		}
		//同金额
		if(isset($param['price']) && $param['price']){
			$where[] = array('price','=',$param['price']);
			$pageParam['price'] = $param['price'];
		}else{
			if(isset($param['price1']) && $param['price1']){
				$where[] = array('bonus','>=',trim($param['price1']));
				$pageParam['price1'] = $param['price1'];
			}
			if(isset($param['price2']) && $param['price2']){
				$where[] = array('bonus','>=',trim($param['price2']));
				$pageParam['price2'] = $param['price2'];
			}
		}
		// 投注金额
		if(isset($param['betprice1']) && $param['betprice1']){
			$where[] = array('price','>=',trim($param['betprice1']));
			$pageParam['betprice1'] = $param['betprice1'];
		}
		if(isset($param['betprice2']) && $param['betprice2']){
			$where[] = array('price','>=',trim($param['betprice2']));
			$pageParam['betprice2'] = $param['betprice2'];
		}
		//投注时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('buy_time','>=',strtotime($dateTime[0]));
			$where[] = array('buy_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('buy_time','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('buy_time','<=',$todayEnd);
		}
		//是否显示测试账号数据
		if (isset($param['displayTest']) && $param['displayTest']) {
			if ($param['displayTest'] != 1) {
				$where[] = array('ly_betting.user_type','neq',3);
			} else {
				$where[] = array('ly_betting.user_type','=',3);
			}
			$pageParam['displayTest'] = $param['displayTest'];
		}

		//查询符合条件的数据
		$resultData = $this->field('ly_betting.*,users.danger')->join('users','ly_betting.uid = users.id','left')->where($where)->order(['buy_time'=>'desc','id'=>'desc'])->paginate(14,false,['query'=>$pageParam]);
		//数据集转数组
		$betList = $resultData->toArray()['data'];
		//部分元素重新赋值
		$orderState = config('custom.bettingState');//中奖状态
		$orderColor = config('manage.adminColor');
		//分页统计
		$pageTotal['bettingZhushu'] = 0;
		$pageTotal['betting'] = 0;
		$pageTotal['rebate'] = 0;
		$pageTotal['winningZhushu'] = 0;
		$pageTotal['winning'] = 0;
		foreach ($betList as $key => &$value) {
			$value['order_state'] 	= $orderState[$value['state']];
			$value['stateColor'] 	= $orderColor[$value['state']];
			$value['lottery_name'] 	= model('PlayClass')->getLotteryFullName(array('class'=>$value['lottery_type']));
			//分页统计
			$pageTotal['bettingZhushu'] += $value['zhushu'];
			$pageTotal['betting'] += $value['price'];
			$pageTotal['rebate'] += $value['rebate_bili'];
			$pageTotal['winningZhushu'] += $value['winning_zhushu'];
			$pageTotal['winning'] += $value['bonus'];
		}
		//彩种
		$lotteryList = model('PlayClass')->where('name','<>','')->field('state,name,class,class_name')->order('name','ASC')->select();

		foreach ($lotteryList as $listKey => &$listValue) {
			$listValue['className'] =  model('PlayClass')->where(array('class'=>$listValue['name']))->value('class_name').' - '.$listValue['class_name'];
		}
		//权限
		$power = model('ManageUserRole')->getUserPower(array(['uid','=',session('manage_userid')],['cid','=',4]));

		return array(
			'betList'		=>	$betList,
			'pageTotal'		=>	$pageTotal,
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$pageParam,
			'orderState'	=>	$orderState,
			'lotteryList'	=>	$lotteryList,
			'lotteryPlay'	=>	$lotteryPlay,//彩种玩法
			'power'			=>	$power,
		);
	}
	/**
	 * 订单详情
	 */
	public function orderDetails(){
		//获取参数
		$param = input('get.');
		//获取订单数据
		$orderInfo = $this->where('id',$param['id'])->find();
		//获取彩种开奖数据
		$lotteryTable = ucfirst($param['lottery']);
		$opencode = model($lotteryTable)->field('opencode')->where('no',$orderInfo['no'])->find();
		$orderInfo['opencode'] = $opencode['opencode'];
		//获取用户信息
		$userInfo = model('Users')->field('username')->where('id',$orderInfo['uid'])->find();
		$orderInfo['username'] = $userInfo['username'];
		//部分数据重新赋值
		$orderInfo['state'] = config('custom.bettingState')[$orderInfo['state']];
		$orderInfo['buy_time'] = date('Y-m-d H:i:s',$orderInfo['buy_time']);
		$orderInfo['pattern'] = config('custom.pattern')[$orderInfo['pattern']];
		//获取任选位
		$orderInfo['rxw'] = $this->rxwz($orderInfo['rxw'],$orderInfo['play']);

		return $orderInfo;
	}

	/**
	 * 订单删除
	 */
	public function orderDel(){
		//获取参数
		$param = input('post.');
		//获取彩种存放的表
		$table = model('PlayClass')->getOne([['class','=',$param['lottery_type']]],'bet_table');
		//修改订单状态
		$editRes = model($table)->where([['order_number','=',$param['order_number']],['state','=',1]])->setField('state',4);
		if(!$editRes) return '操作失败。ERR:1';
		$editRes2 = $this->where([['order_number','=',$param['order_number']],['state','=',1]])->setField('state',4);
		if(!$editRes2) return '操作失败。ERR:2';

		//获取订单信息
		$betInfo = model($table)->field('no,lottery_type,play,price,uid')->where([['order_number','=',$param['order_number']],['state','=',4]])->find();
		//获取用余额
		$userBalance = model('UserTotal')->field('balance')->where('uid',$betInfo['uid'])->find();
		//更新用户余额和撤单金额
		$updateRes = model('UserTotal')->where('uid',$betInfo['uid'])->inc('balance',$betInfo['price'])->inc('total_cancel',$betInfo['price'])->update();
		if(!$updateRes) return '操作失败。ERR:3';

		$tradeDetails = array(
			'uid'					=>	$betInfo['uid'],
			'order_number'			=>	$param['order_number'],
			'trade_type'			=>	4,
			'trade_before_balance'	=>	$userBalance['balance'],
			'trade_amount'			=>	$betInfo['price'],
			'account_balance'		=>	$userBalance['balance'] + $betInfo['price'],
			'remarks'				=>	'管理员删除订单',
			'isadmin'				=>	1,
		);
		model('TradeDetails')->tradeDetails($tradeDetails);
		model('Actionlog')->actionLog(session('manage_username'),'删除订单号为：'.$param['order_number'].'的投注订单',1);

		return 1;
	}


	/**
	 * 改单
	 */
	public function changeOrder(){
		$param = input('post.');
		if(!$param) return false;

		//获取彩种存放的表
		$table = model('PlayClass')->getOne([['class','=',$param['lottery_type']]],'bet_table');
		//获取订单信息
		$betInfo = model($table)->where([['order_number','=',$param['order_number']],['state','=',1]])->find()->toArray();
		//将修改该订单的管理员ID和订单信息入库
		unset($betInfo['id']);
		$betInfo['eid'] = session('manage_userid');
		$betInfo['state'] = 8;

		$result = model('BettingOld')->insertGetId($betInfo);
		if(!$result) return '操作失败';

		model('Betting')->where('order_number',$param['order_number'])->update(['state'=>8]);
		model($table)->where('order_number',$param['order_number'])->update(['state'=>8]);

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'将投注订单'.$param['order_number'].'放入改单列表',1);

		return 1;
	}

	/**
	 * 任选位
	 */
	public function rxwz($rxw,$play){
		$rxwz = '';
		$rx_array = array('r2fs','r2ds','r2zxhz','r2zux','r2zuxhz','r2zx','r2zxds','r2zxfs','r3fs','r3ds','r3zux','r3zuxhz','r3zx','r3zx3','r3zx6','r3zxhx','r3zxhz','r4fs','r4ds','r4zux','r4zx','r4zx24','r4zx12','r4zx6','r4zx4','rx','rxds','rxfs');
		if(in_array($play, $rx_array)){
			$rxwz .= $rxw[0] == '1' ? '万,' : '';
			$rxwz .= $rxw[1] == '1' ? '千,' : '';
			$rxwz .= $rxw[2] == '1' ? '百,' : '';
			$rxwz .= $rxw[3] == '1' ? '十,' : '';
			$rxwz .= $rxw[4] == '1' ? '个,' : '';
			rtrim($rxwz,',');
		}
		return $rxwz;
	}

	/**
	 * 投注分表
	 */
	public function branchTable(){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();

		$orderState = config('custom.bettingState');//中奖状态

		//彩种
		$lotteryList = model('PlayClass')->where('name','<>','')->field('state,name,class,class_name')->order('name','ASC')->select();

		foreach ($lotteryList as $listKey => &$listValue) {
			$listValue['className'] =  model('PlayClass')->where(array('class'=>$listValue['name']))->value('class_name').' - '.$listValue['class_name'];
		}

		//权限
		$power = model('ManageUserRole')->getUserPower(array(['uid','=',session('manage_userid')],['cid','=',4]));

		//彩种
		$lotteryPlay = array();
		if(isset($param['lottery_type']) && $param['lottery_type']){
			$where[] = array('lottery_type','=',$param['lottery_type']);
			$pageParam['lottery_type'] = $param['lottery_type'];

			//获取存放表
			$betTable = model('PlayClass')->field('bet_table')->where('class',$param['lottery_type'])->find();
			//玩法处理
			$thirdPlay = model('PlayGame')->field('pid,game_name,name')->where(array(array('class','=',$param['lottery_type']),array('isopen','=',1)))->select();
			foreach ($thirdPlay as $key => $value) {
				$secondPlay = model('PlayGame')->field('pid,game_name')->where(array(array('class','=',$param['lottery_type']),array('basics_id','=',$value['pid'])))->find();
				
				if($secondPlay['pid']){
					
					$lotteryPlay[$value['name']] = model('PlayGame')->where(array(array('class','=',$param['lottery_type']),array('basics_id','=',$secondPlay['pid'])))->value('game_name').'-'.$secondPlay['game_name'].'-'.$value['game_name'];
				
				}elseif($secondPlay['game_name']){
					
					$lotteryPlay[$value['name']] = $secondPlay['game_name'].'-'.$value['game_name'];
				}else{
					
					$lotteryPlay[$value['name']] = $value['game_name'];
				}
			}
		}else{
			return array(
				'betList'		=>	'',
				'pageTotal'		=>	'',
				'page'			=>	'',
				'where'			=>	'',
				'orderState'	=>	$orderState,
				'lotteryList'	=>	$lotteryList,
				'lotteryPlay'	=>	'',//彩种玩法
				'power'			=>	$power,
			);
		}
		//用户名
		if(isset($param['username']) && $param['username']){
			$where[] = array('users.username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		//玩法
		if(isset($param['play']) && $param['play']){
			$where[] = array('play','=',$param['play']);
			$pageParam['play'] = $param['play'];
		}
		//状态
		if(isset($param['state']) && $param['state']){
			$where[] = array('state','=',$param['state']);
			$pageParam['state'] = $param['state'];

			if($param['state']==3){
				if(isset($param['price1']) && $param['price1']){
					$where[] = array('bonus','>=',trim($param['price1']));
					$pageParam['price1'] = $param['price1'];
				}
				if(isset($param['price2']) && $param['price2']){
					$where[] = array('bonus','>=',trim($param['price2']));
					$pageParam['price2'] = $param['price2'];
				}
			}
		}
		//危险账户
		if(isset($param['danger']) && $param['danger']){
			$where[] = array('danger','=',$param['danger']);
			$pageParam['danger'] = $param['danger'];
		}
		//IP
		if(isset($param['ip']) && $param['ip']){
			$where[] = array('ip','=',trim($param['ip']));
			$pageParam['ip'] = $param['ip'];
		}
		//订单号
		if(isset($param['order_number']) && $param['order_number']){
			$where[] = array('order_number','=',trim($param['order_number']));
			$pageParam['order_number'] = $param['order_number'];
		}
		//期号
		if(isset($param['no']) && $param['no']){
			$where[] = array('no','=',trim($param['no']));
			$pageParam['no'] = $param['no'];
		}
		//同金额
		if(isset($param['price']) && $param['price']){
			$where[] = array('price','=',$param['price']);
			$pageParam['price'] = $param['price'];
		}else{
			if(isset($param['price1']) && $param['price1']){
				$where[] = array('bonus','>=',trim($param['price1']));
				$pageParam['price1'] = $param['price1'];
			}
			if(isset($param['price2']) && $param['price2']){
				$where[] = array('bonus','>=',trim($param['price2']));
				$pageParam['price2'] = $param['price2'];
			}
		}
		// 投注金额
		if(isset($param['betprice1']) && $param['betprice1']){
			$where[] = array('price','>=',trim($param['betprice1']));
			$pageParam['betprice1'] = $param['betprice1'];
		}
		if(isset($param['betprice2']) && $param['betprice2']){
			$where[] = array('price','>=',trim($param['betprice2']));
			$pageParam['betprice2'] = $param['betprice2'];
		}
		//投注时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('buy_time','>=',strtotime($dateTime[0]));
			$where[] = array('buy_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('buy_time','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('buy_time','<=',$todayEnd);
			$param['datetime'] = date('Y-m-d H:i:s',$todayStart).' - '.date('Y-m-d H:i:s',$todayEnd);
		}

		//查询符合条件的数据
		$resultData = $this->field('ly_betting.*,users.username,danger')->join('users','ly_betting.uid = users.id','left')->where($where)->order(['buy_time'=>'desc','id'=>'desc'])->paginate(16,false,['query'=>$pageParam]);
		//数据集转数组
		$betList = $resultData->toArray()['data'];
		//部分元素重新赋值
		$orderColor = config('custom.color');
		//分页统计
		$pageTotal['bettingZhushu'] = 0;
		$pageTotal['betting'] = 0;
		$pageTotal['rebate'] = 0;
		$pageTotal['winningZhushu'] = 0;
		$pageTotal['winning'] = 0;
		foreach ($betList as $key => &$value) {
			$value['order_state'] = $orderState[$value['state']];
			$value['stateColor'] = $orderColor[$value['state']];
			//分页统计
			$pageTotal['bettingZhushu'] += $value['zhushu'];
			$pageTotal['betting'] += $value['price'];
			$pageTotal['rebate'] += $value['rebate_bili'];
			$pageTotal['winningZhushu'] += $value['winning_zhushu'];
			$pageTotal['winning'] += $value['bonus'];
		}

		return array(
			'betList'		=>	$betList,
			'pageTotal'		=>	$pageTotal,
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$param,
			'orderState'	=>	$orderState,
			'lotteryList'	=>	$lotteryList,
			'lotteryPlay'	=>	$lotteryPlay,//彩种玩法
			'power'			=>	$power,
		);
	}

	/**
	 * 撤单
	 */
	public function cancelOrder($param=array()){

		//获取彩种存放的表
		$table = model('PlayClass')->getOne([['class','=',$param['lottery_type']]],'bet_table');
		//更新betting表
		$result1 = $this->where([['order_number','=',$param['order_number']],['state','=',1]])->update(array('state'=>5));
		//更新其他表
		$result2 = model($table)->where([['order_number','=',$param['order_number']],['state','=',1]])->update(array('state'=>5));

		if(!$result1 || !$result2) return false;

		//获取订单信息
		$betInfo = model($table)->field('uid,play,no,lottery_type,price')->where([['order_number','=',$param['order_number']],['state','=',5]])->find();

		//获取用户余额
		$balance = model('UserTotal')->field('balance')->where('uid',$betInfo['uid'])->find();
		//更新用户余额
		$result3 = model('UserTotal')->where('uid',$betInfo['uid'])->inc('total_cancel',$betInfo['price'])->inc('balance',$betInfo['price'])->update();
		if(!$result3) return false;

		//生成流水数据
		$detailsArray = array(
			'uid' 					=>	$betInfo['uid'],
			'order_number' 			=>	$param['order_number'],
			'trade_type' 			=>	5,
			'trade_before_balance'	=>	$balance['balance'],
			'trade_amount' 			=>	$betInfo['price'],
			'account_balance' 		=>	$balance['balance']+$betInfo['price'],
			'lottery_no'			=>	$betInfo['no'],
			'lottery_type'			=>	$betInfo['lottery_type'],
			'lottery_play'			=>	$betInfo['play']
		);
		if(isset($param['sid'])) $detailsArray['sid'] = $param['sid'];
		if(isset($param['remarks'])) $detailsArray['remarks'] = $param['remarks'];
		$result4 = model('TradeDetails')->tradeDetails($detailsArray);

		if(!$result4) return false;

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'将投注订单'.$param['order_number'].'撤单',1);

		return 5;
	}

	public function cancelMany(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';

		//数据验证
		$validate = validate('app\manage\validate\Bet');
		if(!$validate->scene('cancelMany')->check($param)){
			return $validate->getError();
		}

		$openCode = model(ucfirst($param['lottery']))->where('no', $param['no'])->value('opencode');

		$res = $this->cancelOrderAll($param['lottery'], array('lottery_type'=>$param['lottery'],'no'=>$param['no'],'state'=>1), $openCode);

		if (!$res) return '操作失败';

		return 1;
	}

	/**
	 * 批量撤单
	 */
	public function cancelOrderAll($lotteryType,$where=array(),$kjcode=''){
		if(!$lotteryType || !$where) return false;
		//获取彩种存放的表
		$table = model('PlayClass')->getOne([['class','=',$lotteryType]],'bet_table');

		//获取所有订单
		$betInfo = model($table)->field('uid,order_number,lottery_type,play,no,price')->where($where)->select();
		if(!$betInfo) return false;
		//数据集转数组
		$betInfo = $betInfo->toArray();

		$res = 0;
		foreach ($betInfo as $key => $value) {
			//更新betting表
			$result1 = $this->where([['order_number','=',$value['order_number']],['state','=',1]])->update(array('state' =>5,'kjcode'=>$kjcode));
			//更新其他表
			$result2 = model($table)->where([['order_number','=',$value['order_number']],['state','=',1]])->update(array('state' =>5,'kjcode'=>$kjcode));

			if(!$result1 || !$result2) return false;

			//获取用户余额
			$balance = model('UserTotal')->where('uid',$value['uid'])->value('balance');
			//更新用户余额
			$result3 = model('UserTotal')->where('uid',$value['uid'])->inc('total_cancel',$value['price'])->inc('balance',$value['price'])->update();
			if(!$result3) return false;

			//生成流水数据
			$detailsArray = array(
				'uid' 					=>	$value['uid'],
				'order_number' 			=>	$value['order_number'],
				'trade_type' 			=>	5,
				'trade_before_balance'	=>	$balance,
				'trade_amount' 			=>	$value['price'],
				'account_balance' 		=>	$balance+$value['price'],
				'lottery_no'			=>	$value['no'],
				'lottery_type'			=>	$value['lottery_type'],
				'lottery_play'			=>	$value['play'],
				'remarks'				=>	'系统撤单'
			);
			$result4 = model('TradeDetails')->tradeDetails($detailsArray);

			if(!$result4) return false;

			$res++;
		}

		if(count($betInfo) != $res) return false;

		return true;
	}



	/**
	 * 同IP投注统计
	 */
	public function betIpIntersect(){
		// 获取参数
		$param = input('get.');
		// 查询条件组装
		$where = array();
		// 分页参数组装
		$pageParam = array();

		// 时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('buy_time','>=',strtotime($dateTime[0]));
			$where[] = array('buy_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('buy_time','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('buy_time','<=',$todayEnd);
		}
		// 获取多账号投注IP
		$notIntersect = $this->where($where)->group('ip,username')->column('ip');
		$Intersect = array_unique(array_diff_assoc($notIntersect, array_unique($notIntersect)));
		// 查询符合条件的数据
		$resultData = $this->field('username,ip')->where($where)->whereIn('ip',$Intersect)->group('username,ip')->order('ip', 'desc')->paginate(15,false,['query'=>$pageParam]);
		// 数据集转数组
		$betIpIntersect = $resultData->toArray()['data'];

		return array(
			'betIpIntersect' =>	$betIpIntersect,
			'page'           =>	$resultData->render(),//分页
			'where'          =>	$pageParam,
		);
	}
}