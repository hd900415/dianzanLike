<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\create\controller;

use think\Controller;

class IndexController extends Controller{

	public function initialize(){
    	header('Access-Control-Allow-Origin:*');
	}

	/**
	 * 自动审核
	 * @return [type] [description]
	 */
	public function autoAudit(){
		$isAutoAudit = model('Setting')->where('id','>',0)->value('auto_audit');
		if ($isAutoAudit == 2 || !$isAutoAudit || is_null($isAutoAudit)) return 0;

		$today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$yesterday = $today - 86400;

		//$taskList = model('UserTask')->field('ly_user_task.id,ly_user_task.examine_demo,task_id')->join('task','ly_user_task.task_id=task.id')->where([['ly_user_task.status','=',2],['task.uid','=',0]])->whereTime('trial_time', '>=', $yesterday)->select()->toArray();
		$taskList = model('UserTask')->field('ly_user_task.id,ly_user_task.examine_demo,task_id')->join('task','ly_user_task.task_id=task.id')->where([['ly_user_task.status','=',2]])->whereTime('trial_time', '>=', $yesterday)->limit(600)->select()->toArray();
		if (!$taskList) return 0;

		foreach ($taskList as $key => $value) {
			$status = ($value['examine_demo']) ? 3 : 4;
			// 修改任务订单状态
			$res = model('UserTask')->where('id',$value['id'])->update(['status'=>$status,'handle_time'=>time(),'complete_time'=>time()]);

			if ($status == 4) {
				model('Task')->where('id', $value['task_id'])->dec('receive_number')->inc('surplus_number')->update();
				continue;
			}
			// 订单信息
			$taskInfo = model('UserTask')
						->field([
							'ly_user_task.status',
							'ly_user_task.uid',
							'ly_user_task.task_id',
							'task.order_number',
							'task.reward_price',
							'task.total_number',
						])
						->join('task','task.id=ly_user_task.task_id')
						->where([
							['ly_user_task.id','=',$value['id']]
						])
						->find();
			// 单价为零则跳出本次循环
			if ($taskInfo['reward_price'] <= 0) continue;
			// 获取用户信息
			$userInfo = model('Users')->field('ly_users.id,ly_users.vip_level,username,sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.id', $taskInfo['uid'])->find();
			if (!$userInfo) {
				model('UserTask')->where('id', $value['id'])->update(['status'=>2,'complete_time'=>0]);
				continue;
			}
			// 加余额
			$incUserBalance = model('UserTotal')->where('uid', $userInfo['id'])->inc('balance', $taskInfo['reward_price'])->inc('total_balance', $taskInfo['reward_price'])->update();
			if (!$incUserBalance) {
				model('UserTask')->where('id', $value['id'])->update(['status'=>2,'complete_time'=>0]);
				continue;
			}
			// 流水
			$financialArray['uid']                  = $userInfo['id'];
			$financialArray['sid']                  = $userInfo['sid'];
			$financialArray['username']             = $userInfo['username'];
			$financialArray['order_number']         = $taskInfo['order_number'];
			$financialArray['trade_number']         = 'L'.trading_number();
			$financialArray['trade_type']           = 6;
			$financialArray['trade_before_balance'] = $userInfo['balance'];
			$financialArray['trade_amount']         = $taskInfo['reward_price'];
			$financialArray['account_balance']      = $userInfo['balance'] + $taskInfo['reward_price'];
			$financialArray['remarks']              = '完成任务';
			$financialArray['types']                = 1;	// 用户1，商户2

			model('common/TradeDetails')->tradeDetails($financialArray);

			//已经完成的 和 总的任务数 一样 更新任务 完成

			$finishNumber =	model('UserTask')->where(array(['task_id','=',$taskInfo['task_id']],['status','=',3]))->count();
			if ($finishNumber == $taskInfo['total_number']) {
				model('Task')->where(array(['id','=',$taskInfo['task_id']],['status','=',3]))->update(['status'=>4,'complete_time'=>time()]);
			}

			//上级返点
			if ($userInfo['sid']) {
				$rebatearr = array(
					'num'			=>	1,
					'uid'			=>	$userInfo['id'],
					'sid'			=>	$userInfo['sid'],
					'order_number'	=>	$taskInfo['order_number'],
					'commission'	=>	$taskInfo['reward_price'],
				);

				model('manage/Task')->setrebate($rebatearr);
			}
			
			//更新每日完成任务次数
			$UserDailydata = array(
				'uid'				=>	$userInfo['id'],
				'username'			=>	$userInfo['username'],
				'field'				=>	'w_t_o_n',//完成
				'value' 			=>	1,
			);
			
			model('common/UserDaily')->updateReportfield($UserDailydata);

		}

		return 1;
	}

}
