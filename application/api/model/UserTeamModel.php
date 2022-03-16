<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Db;
// use think\Model;
use app\common\model\UserTeamModel as UT;

class UserTeamModel extends UT{
	//表名
	protected $table = 'ly_user_team';

	//添加至团队表
	public function addUserTeam($id){
		$array = model('Users')->userSid($id);

		$insertArray = array();
		foreach ($array as $key => $value) {
			$insertArray[] = ['uid'=>$value, 'team'=>$id];
		}

		$res = $this->insertAll($insertArray);
		if (!$res) return false;

		return true;
	}

	/**
	 * 团队总览
	 */
	public function teamReport(){
		//获取参数
		$token 		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];//uid
		$username 	= $userArr[1];//username
        $lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		$param 		= input('post.');

		$sid = $uid;
		//查看下级
		if (isset($param['pve_id']) && $param['pve_id']) $sid = $param['pve_id'];
		//团队关系判断
		$isInTeam = model('UserTeam')->where(['uid'=>$uid,'team'=>$sid])->count();
		if (!$isInTeam)
			        if($lang=='cn'){
		                return ['code'=>0, 'code_dec'=>'团队中搜索不到该用户'];
		            }elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'The user was not found in the team'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'Pengguna tidak ditemukan dalam tim'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '團隊中蒐索不到該用戶'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'उपयोक्ता टीम में नहीं मिला'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'Người dùng không tìm thấy trong đội'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'No se puede encontrar en el equipo'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => 'チームではこのユーザを検索できません。'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'ไม่สามารถค้นหาผู้ใช้นี้ในทีมงาน'];
					}		
		

		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));

		//开始时间
		$startDate = (isset($param['startdate']) && $param['startdate']) ? strtotime($param['startdate']) : $today - 86400 * 7;
		//结束时间
		$endDate = (isset($param['enddate']) && $param['enddate']) ? strtotime($param['enddate'].' 23:59:59') : $today + 86400;

		/**
		 * 团队报表
		 */
		// 团队余额
		$data['teamBalance']        = round($this->alias('ut')->join('user_total','ut.team=user_total.uid')->where('ut.uid','=',$uid)->sum('balance'),2);
		$param['trade_number']								   = 'L'.trading_number();
		// 团队收益
		$teamProfit                 = $this->alias('ut')->field(['SUM(`commission`)'=>'commission','SUM(`rebate`)'=>'rebate'])->join('user_daily','ut.team=user_daily.uid')->where('ut.uid','=',$uid)->whereTime('date', 'between', [$startDate, $endDate])->find();
		$data['teamProfit']         = round($teamProfit['commission'] + $teamProfit['rebate'],3);
		// 团队总充值
		$data['teamRecharge']       = round($this->alias('ut')->join('user_recharge','ut.team=user_recharge.uid')->where('ut.uid','=',$uid)->where('user_recharge.state','=',1)->sum('money'),2);
		// 团队总提现
		$data['teamWithdrawal']     = round($this->alias('ut')->join('user_withdrawals','ut.team=user_withdrawals.uid')->where('ut.uid','=',$uid)->where('user_withdrawals.state','=',1)->sum('price'),2);
		$param['trade_number']								   = 'L'.trading_number();
		// 直推人数
		$data['directlyUnder']      = model('Users')->where('sid',$uid)->count();
		// 今日首冲
		$data['firstRechargeToday'] = $this->alias('ut')->field('user_recharge.uid')->join('user_recharge','ut.team=user_recharge.uid')->where([['ut.uid','=',$uid],['state','=',1]])->whereTime('add_time', 'between', [$startDate, $endDate])->group('user_recharge.uid')->count();
		//团队总人数
		$data['teamNumber']         = $this->where('uid',$uid)->count();
		// 新增人数
		$data['newReg']             = $this->alias('ut')->join('users','ut.team=users.id')->where('ut.uid','=',$uid)->whereTime('reg_time', 'between', [$startDate, $endDate])->count();
		// 计算日期间隔
		$dateSpace = ($endDate - $startDate) / 86400;
		if($dateSpace > 31) $dateSpace = 31;
		if($dateSpace < 1) $dateSpace = 1;

		//查询符合条件的数据
		$userList = model('Users')->field('id,sid,username,state')->where('sid', $sid)->select()->toArray();
		//用户团队数据计算
		$data['teamData'] = model('manage/UserDaily')->teamStatistic($userList,$startDate,$endDate,$sid);
		unset($data['teamData']['totalAll']);

		// // 当天开始时间
		// $startTime = $endDate - ($endDate - strtotime(date('Y-m-d',$endDate)));

		// // 循环得出每日数据
		// for ($i=0; $i < $dateSpace; $i++) {
		// 	// 团队数据
		// 	$data['teamData'][$i] = $this->alias('ut')->field([
		// 		'SUM(`recharge`)'   => 'recharge', // 充值
		// 		'SUM(`withdrawal`)' => 'withdrawal', // 提现
		// 		'SUM(`task`)'       => 'task', // 发布任务
		// 		'SUM(`rebate`)'     => 'rebate', // 返点
		// 		'SUM(`regment`)'    => 'regment', // 活动
		// 		'SUM(`buymembers`)' => 'buymembers', // 购买会员
		// 		'SUM(`commission`)' => 'commission', // 任务提成
		// 	])->join('user_daily','ut.team=user_daily.uid')
		// 	->where('ut.uid','=',$uid)
		// 	->whereTime('date', 'between', [$startDate, $endDate])->find()->toArray();
		// 	// 发布任务数量
		// 	$data['teamData'][$i]['relTaskNumber'] = $this->alias('ut')->join('task','ut.team=task.uid')->where('ut.uid','=',$uid)->whereIn('status', [3,4])->whereTime('add_time', 'between', [$startTime, $startTime+86399])->count();
		// 	// 接手任务数量
		// 	$data['teamData'][$i]['takeTaskNumber'] = $this->alias('ut')->join('user_task','ut.team=user_task.uid')->where('ut.uid','=',$uid)->whereTime('add_time', 'between', [$startTime, $startTime+86399])->count();

		// 	// 日期
		// 	$data['teamData'][$i]['date'] = date('m-d', $endDate);

		// 	$startTime -= 86400;
		// 	$endDate -= 86400;
		// }
		$data['code'] = 1;

		return $data;
	}
}
