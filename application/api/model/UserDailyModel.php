<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

// use think\model;
use app\common\model\UserDailyModel as UD;

class UserDailyModel extends UD{

	protected $table = 'ly_user_daily';

	/**
	 * 计算团队数据
	 * @param  array  $userInfo [description]
	 * @return [type]           [description]
	 */
	public function teamData($userInfo=array()){
		if (!$userInfo || !is_array($userInfo)) return array();

		$todayStart = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$todayEnd   = mktime(23, 59, 59, date('m'), date('d'), date('Y'));

		$array = array();
		foreach ($userInfo as $key => $value) {
			$array[$key]['id']       = $value['id'];
			$array[$key]['uid']      = $value['uid'];
			$array[$key]['alipay_fee']      = $value['alipay_fee'];
			$array[$key]['wechat_fee']      = $value['wechat_fee'];
			$array[$key]['bank_fee']      	= $value['bank_fee'];
			$array[$key]['state']      		= $value['state'];
			$array[$key]['at_time']  = ($value['at_time']) ? date('Y-m-d', $value['at_time']) : 0;
			if ($value['realname']) {
				$array[$key]['realname'] = (preg_match("/[\x7f-\xff]/", $value['realname'])) ? '**'.mb_substr($value['realname'], -1, 1) : '**'.substr($value['realname'], -3, 3);
			} else {
				$array[$key]['realname'] = '-';
			}
			// 今日个人流水
			$array[$key]['myOrder'] = $this->where(array(['uid','=',$value['id']],['date','>=',$todayStart],['date','<=',$todayEnd]))->sum('order');
			// 今日团队业绩
			$array[$key]['teamOrder'] = $this->join('user_team', 'ly_user_daily.uid = user_team.team')->where(array(['user_team.uid','=',$value['id']],['user_team.team','<>',$value['id']],['date','>=',$todayStart],['date','<=',$todayEnd]))->sum('order');
			// 直属信息
			$zhishu = model('Users')->where('sid', $value['id'])->column('reg_time');
			// 直属今日注册
			$array[$key]['todayReg'] = 0;
			foreach ($zhishu as $key2 => $value2) {
				if ($value2 >= $todayStart && $value2 <= $todayEnd) $array[$key]['todayReg']++;
			}
			// 直属累积
			$array[$key]['zhishuSum'] = count($zhishu);
			// 团队信息
			$team = model('Users')->join('user_team', 'ly_users.id = user_team.team')->where(array(['user_team.uid','=',$value['id']],['user_team.team','<>',$value['id']]))->column('ly_users.reg_time');
			// 团队今日注册
			$array[$key]['todayTeamReg'] = 0;
			foreach ($team as $key3 => $value3) {
				if ($value3 >= $todayStart && $value3 <= $todayEnd) $array[$key]['todayTeamReg']++;
			}
			// 团队累积
			$array[$key]['teamSum'] = count($team);
		}

		return multi_array_sort($array, 'myOrder');
	}

	/**
	 * 团队业绩报表
	 * @return [type] [description]
	 */
	public function teamReport($date, $userInfo=array()){
		if (!$userInfo || !is_array($userInfo)) return array();

		$array = array();
		foreach ($userInfo as $key => $value) {
			$array[$key]['id']       = $value['id'];
			$array[$key]['uid']      = $value['uid'];
			if ($value['realname']) {
				$array[$key]['realname'] = (preg_match("/[\x7f-\xff]/", $value['realname'])) ? '**'.mb_substr($value['realname'], -1, 1) : '**'.substr($value['realname'], -3, 3);
			} else {
				$array[$key]['realname'] = '-';
			}
			// 团队数据
			$teamData = $this->field(['SUM(`order`)'=>'order','SUM(`commission`)'=>'commission','SUM(`giveback`)'=>'giveback'])->join('user_team', 'ly_user_daily.uid = user_team.team')->where(array(['user_team.uid', '=', $value['id']],['user_team.team', '<>', $value['id']],['ly_user_daily.date', '=',$date]))->findOrEmpty();
			// 团队业绩
			$array[$key]['teamOrder']  = (isset($teamData['order'])) ? $teamData['order'] : 0;
			// 团队佣金
			$array[$key]['teamFee']    = (isset($teamData['commission'])) ? $teamData['commission'] : 0;
			// 团队收益
			$array[$key]['teamProfit'] = (isset($teamData['giveback'])) ? $teamData['giveback'] : 0;
			// 个人数据
			$aloneData = $this->field('order,commission,giveback')->where(array(['uid', '=', $value['id']],['date', '=', $date]))->findOrEmpty();
			// 个人佣金
			$array[$key]['aloneFee']    = (isset($aloneData['commission'])) ? $aloneData['commission'] : 0;
			// 个人流水
			$array[$key]['aloneDetail'] = (isset($aloneData['order'])) ? $aloneData['order'] : 0;
			// 个人收益
			$array[$key]['aloneProfit'] = (isset($aloneData['giveback'])) ? $aloneData['giveback'] : 0;
		}

		return multi_array_sort($array, 'teamOrder');
	}

	/**
	 * 获取账号每日盈利
	 * @return [type] [description]
	 */
	public function getUserDailyProfit(){
		$param    = input('param.');
		$userArr  = explode(',', auth_code($param['token'], 'DECODE'));	// uid,username
		$uid      = $userArr[0];	// uid
		$username = $userArr[1];	// username

		$todayStart = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

		$userDaily = $this->field('date,rebate,commission,giveback')->where(array(['uid','=',$uid], ['date','<=',$todayStart]))->order('date', 'desc')->limit(30)->select()->toArray();
		for ($i=0; $i < 30; $i++) { 
			// 默认数据
			$data[$i] = ['date'=>date('Y-m-d',$todayStart), 'rebate'=>0, 'commission'=>0, 'giveback'=>0, 'sumToday'=>0];
			
			foreach ($userDaily as $key => &$value) {
				if ($todayStart == $value['date']) {
					// 格式挂时间戳
					$value['date'] = date('Y-m-d', $value['date']);
					// 今日总和
					$value['sumToday'] = $value['rebate'] + $value['commission'] + $value['giveback'];
					
					$data[$i] = $value;

					break;
				}
			}
			$todayStart -= 86400;
		}

		return $data;
	}
}
