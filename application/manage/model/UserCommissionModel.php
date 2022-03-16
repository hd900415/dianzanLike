<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class UserCommissionModel extends Model{
	//表名
	protected $table = 'ly_user_commission';

	public function userFeeList(){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();

		//用户名
		if(isset($param['username']) && $param['username']){
			$where[] = array('u.username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		// 时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('date','>=',strtotime($dateTime[0]));
			$where[] = array('date','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('date','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('date','<=',$todayEnd);
		}

		//查询符合条件的数据
		$resultData = $this->alias('uc')->field('uc.*, u.username')->join('users u', 'uc.uid = u.id', 'left')->where($where)->order(['issue_time'=>'desc','id'=>'desc'])->paginate(15,false,['query'=>$pageParam]);
	

		
	
		
// 		foreach ($resultData as $key => $value) {
// 		    if ($value['date']) {
// 		        $value['date'] = date('Y-m-d H:i:s', time());
// 		    }
		    
// 		    $resultData[$key] = $value;
// 		}

		return array(
			'data'			=>	$resultData->toArray()['data'],
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$pageParam
		);
	}
	
	
		public function userFeeListnew(){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();

		//用户名
		if(isset($param['username']) && $param['username']){
			$where[] = array('u.username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		// 时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('date','>=',strtotime($dateTime[0]));
			$where[] = array('date','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}

		//查询符合条件的数据
		$resultData = $this->alias('uc')->field('uc.*, u.username')->join('users u', 'uc.uid = u.id', 'left')->where($where)->order(['issue_time'=>'desc','id'=>'desc'])->paginate(15,false,['query'=>$pageParam]);
		
		$count = $this->alias('uc')->field('uc.*, u.username')->join('users u', 'uc.uid = u.id', 'left')->where($where)->count();
	

		
	
		
// 		foreach ($resultData as $key => $value) {
// 		    if ($value['date']) {
// 		        $value['date'] = date('Y-m-d H:i:s', time());
// 		    }
		    
// 		    $resultData[$key] = $value;
// 		}

		return array(
			'data'			=>	$resultData->toArray()['data'],
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$pageParam,
			'count'         => $count
		);
	}


	public $commissionConfig = array(
		['teamOrder' => 2, 'ratio' => 0.0005],
		['teamOrder' => 5, 'ratio' => 0.001],
		['teamOrder' => 10, 'ratio' => 0.0015],
		['teamOrder' => 20, 'ratio' => 0.002],
		['teamOrder' => 40, 'ratio' => 0.0025],
		['teamOrder' => 80, 'ratio' => 0.003],
		['teamOrder' => 140, 'ratio' => 0.004],
		['teamOrder' => 230, 'ratio' => 0.005],
		['teamOrder' => 350, 'ratio' => 0.006],
		['teamOrder' => 550, 'ratio' => 0.007],
		['teamOrder' => 800, 'ratio' => 0.008],
	);
	/**
	 * 发放每日佣金
	 * @return [type] [description]
	 */
	public function commissionGrant(){
		$param = input('post.');
		if(!$param) return '提交失败';

		//数据验证
		$validate = validate('app\manage\validate\Activity');
		if(!$validate->scene('everyday')->check($param)){
			//抛出异常
			return $validate->getError();
		}

		$date = strtotime($param['date']);

		//时间检测
		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));
		if($date >= $today) return '不能发放'.$param['date'].'的佣金';
		// 获取待发放列表
		$userDailyLis = model('UserDaily')->field('ly_user_daily.uid,ly_user_daily.order,users.sid')->join('users','ly_user_daily.uid = users.id')->where('ly_user_daily.date',$date)->order('users.sid','asc')->select()->toArray();
		if(!$userDailyLis) return '没有待发放的用户';

		foreach ($userDailyLis as $key => $value) {
			$this->calculate([
				'sid'   => $value['sid'],
				'uid'   => $value['uid'],
				'order' => $value['order'],
				'date'  => $date
			]);
		}

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'派发每日佣金',1);

		return 1;
	}

	/**
	 * 计算
	 */
	public function calculate($array=array()){
		if(!$array) return false;

		//获取当天团队数据
		$teamData = model('UserDaily')->join('user_team', 'ly_user_daily.uid = user_team.team')->where(array(['user_team.uid','=',$array['uid']],['user_team.team','<>',$array['uid']],['date','=',$array['date']],['is_commission','<>',1]))->sum('order');
		if($teamData < 20000) return false;

		//计算金额和百分比
		$price = 0;
		$ratio = 0;
		$bonusConfigArray = array_reverse($this->commissionConfig);		
		foreach ($bonusConfigArray as $key => $value) {
			if($teamData >= $value['teamOrder']*10000){
				$price = $teamData * $value['ratio'];
				$ratio = $value['ratio'];
				break;
			}
		}

		if(!$price) return false;

		$state = 1;//订单状态默认1（已发放）
		//上级金额不足时订单状态为2
		$sUserBalance = model('UserTotal')->where('uid',$array['sid'])->value('balance');
		if($sUserBalance - $price < 0){
			model('Users')->where('id',$array['sid'])->update(['withdrawals_state'=>2]);
			$state = 2;
		}
		// 发放人
		$sid = ($array['sid'] && $price) ? $array['sid'] : 0;
		
		$orderNumber = 'D'.trading_number();		
		$insertArray = array(
			'uid'          => $array['uid'],
			'gid'          => $sid,
			'order_number' => $orderNumber,
			'team_order'   => $teamData,
			'price'        => $price,
			'ratio'        => $ratio,
			'status'       => 1,
			'issue_time'   => time(),
			'date'         => strtotime($array['date'])
		);
		$this->insertGetId($insertArray);
		
		// 修改时间段分红发放情况
		model('UserDaily')->where([['uid','=',$array['uid']],['date','=',$array['date']]])->setField('is_commission', 1);
		// 上级余额不足时执行到此结束
		if($state == 2 || !$price) return false;
		//获取用户余额
		$balance = model('UserTotal')->field('balance,frozen_balance')->where('uid',$array['uid'])->findOrEmpty();
		// 扣除上级金额
		model('UserTotal')->where('uid',$array['sid'])->dec('balance',$price)->dec('total_commission',$price)->update();		
		//更新用户余额
		model('UserTotal')->where('uid',$array['uid'])->inc('balance',$price)->inc('total_commission',$price)->update();

		if(!$sid){
			$remarks = '系统发放';
		}else{
			$getUsername = model('Users')->where('id',$array['sid'])->value('username');
			$username = model('Users')->where('id',$array['uid'])->value('username');
			$remarks = $getUsername.'发放给'.$username.'的佣金';
		}
		// 生成流水
		$details = array(
			'uid'                    =>	$array['uid'],
			'order_number'           =>	$orderNumber,
			'trade_type'             =>	9,
			'trade_before_balance'   =>	$balance['balance'],
			'trade_amount'           =>	$price,
			'account_balance'        =>	$balance['balance'] + $price,
			'account_frozen_balance' => $balance['frozen_balance'],
			'remarks'                =>	$remarks,
			'types'                  =>	1,
			'isadmin'                =>	1,
			'isdaily'                => 1
		);
		$detailsInsertId = model('common/TradeDetails')->tradeDetails($details);

		// 生成上级流水
		$details = array(
			'uid'                    =>	$array['sid'],
			'order_number'           =>	$orderNumber,
			'trade_type'             =>	9,
			'trade_before_balance'   =>	$balance['balance'],
			'trade_amount'           =>	$price,
			'account_balance'        =>	$balance['balance'] - $price,
			'account_frozen_balance' => $balance['frozen_balance'],
			'remarks'                =>	$remarks,
			'types'                  =>	1,
			'isadmin'                =>	1,
			'isdaily'                => 1
		);
		$detailsInsertId = model('common/TradeDetails')->tradeDetails($details);

		return true;
	}
}