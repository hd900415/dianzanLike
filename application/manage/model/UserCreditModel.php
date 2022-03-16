<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class UserCreditModel extends Model{
	//表名
	protected $table = 'ly_user_credit';

	/**
	 * 信用评估
	 * @return [type] [description]
	 */
	public function assess(){
		if (!request()->isAjax()) return '提交失败';
		$beginLastweek = strtotime("last week Monday",time()); // 上周一
		$endLastweek   = strtotime("last week Sunday",time())+86399; // 上周日
		$thisWeekStart = $beginLastweek + 86400 * 7;
		$thisWeekEnd   = $endLastweek + 86400 * 7;

		$lastWeekIsAssess = $this->whereTime('time', 'between', [$thisWeekStart, $thisWeekEnd])->count();
		if ($lastWeekIsAssess) return '上周信用已评估完成，请勿重复操作';
		// 用户组
		$userGroup = model('UserTask')->field('uid')->whereTime('handle_time', 'between', [$beginLastweek, $endLastweek])->group('uid')->select()->toArray();
		if (!$userGroup) return '上周无可评估的信用记录';
		// 初始化更新信用
		foreach ($userGroup as $key => $value) {
			$userCredit[$value['uid']] = 0;
		}
		// 计算信用
		$startTime = $beginLastweek;
		$endTime   = $startTime + 86399;
		for ($i=0; $i < 7; $i++) {
			// 获取一天的任务单
			$taskList = model('UserTask')->field('uid,status')->whereTime('handle_time', 'between', [$startTime, $endTime])->select()->toArray();
			foreach ($taskList as $key => $value) {
				if (in_array($value['status'], [5])) $userCredit[$value['uid']]--;
			}

			$startTime += 86400;
			$endTime   += 86400;
		}
		// 重新判断信用
		foreach ($userCredit as $key => &$value) {
			if ($value == 0) $value = 1;
			if ($value < -7) $value = -7;
			// 更新用户信用
			$userInfo = model('Users')->field('credit,username')->where('id', $key)->find();
			$userCreditVal = ($userInfo['credit'] + $value > 100) ? 100 : $userInfo['credit'] + $value;
			$res = model('Users')->where('id', $key)->setField('credit', $userCreditVal);
			// 添加信用记录
			$res2 = $this->insert([
				'username' => $userInfo['username'],
				'uid'      => $key,
				'credit'   => $value,
				'time'     => time(),
				'remarks'  => '系统信用评估'
			]);
		}

		return 1;
	}
}