<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\model;

use think\Model;

class TaskModel extends Model{
	//表名
	protected $table = 'ly_task';

	/**
	 * 添加任务
	 */
	public function add(){
		if (!request()->isAjax()) return '提交失败';
		$param = input('param.');
		//数据验证
		$validate = validate('app\manage\validate\Task');
		if (!$validate->scene('add')->check($param)) return $validate->getError();



		if (isset($param['finish_condition']) and $param['finish_condition']) $param['finish_condition'] 	= json_encode(array_keys($param['finish_condition']));

		if (isset($param['task_step']) and $param['task_step']) $param['task_step']               			= json_encode(array_merge($param['task_step']),true);
		if (isset($param['examine_demo']) and $param['examine_demo']) $param['examine_demo']         		= json_encode($param['examine_demo'],true);

		$param['end_time']                                         = strtotime($param['end_time']);
		$param['add_time']                                         = time();
		$param['surplus_number']								   = $param['total_number'];

		// 流水 任务金额

		$param['order_number']								 = 'B'.trading_number();
		$param['trade_number']								 = 'L'.trading_number();
		$param['username']									 = '1'.mt_rand(50,99).'3745'.mt_rand(1483,9789);

        $repeat_num = $param['repeat_num'];
        unset($param['repeat_num']);
        if ($repeat_num) {
            $temp = [];
            
            for($i=1; $i<= $repeat_num; $i++) {
                $temp[] = $param;
            }
            
            $res = $this->allowField(true)->saveAll($temp);
        } else {
            $res = $this->allowField(true)->save($param);
        }
	
		if (!$res) return '添加失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'添加任务：标题为'.$param['title'],1);

		return 1;
	}

	/**
	 * 编辑任务
	 */
	public function edit(){

		if (!request()->isAjax()) return '提交失败';

		$param = input('param.');
		//数据验证
		$validate = validate('app\manage\validate\Task');
		if (!$validate->scene('add')->check($param)) return $validate->getError();

		$id = $param['id'];
		unset($param['id']);
		if (isset($param['finish_condition']) && $param['finish_condition']) $param['finish_condition'] = json_encode(array_keys($param['finish_condition']));
		if (isset($param['examine_demo']) && $param['examine_demo']) $param['examine_demo']         	= json_encode($param['examine_demo'],true);
		if (isset($param['task_step']) && $param['task_step']) $param['task_step']               		= json_encode(array_merge($param['task_step']),true);
		$param['end_time']                                         = strtotime($param['end_time']);
		
		$taskInfo	= $this->where('id', $id)->find();
		if(!$taskInfo){
			if($param['lang']=='cn') return ['code'=>0, 'code_dec'=>'任务不存在'];
			else return ['code'=>0, 'code_dec'=>'Task does not exist!'];
		}
		
		// 如果是修改任务的领取数量，则必须修改剩余数量——————————————————————————————
		if($param['total_number'] && $param['total_number'] < $taskInfo['total_number']){	// 判断新数量必须大于原数量
			if($param['lang']=='cn') return '新的领取数量应大于原来的领取数量';
			else return 'The new collection quantity should be greater than the original collection quantity!';
		}
		
		if($param['total_number'] && $param['total_number'] > $taskInfo['total_number']){
			$param['surplus_number']	= $param['total_number'] - $taskInfo['receive_number'];
		}		
		
		$res = $this->allowField(true)->save($param, ['id'=>$id]);
		if (!$res) return '修改失败';
		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'修改任务：标题为'.$param['title'],1);

		return 1;
	}


	/**
	 * 编辑任务
	 */
	public function del(){
		if (!request()->isAjax()) return '提交失败';
		$param = input('param.');
		if (!$param) return '提交失败';

		if (isset($param['data']) && $param['data']) { // 批量删除
			foreach ($param['data'] as $key => $value) {
				// 提取信息
				$taskInfo = $this->where('id', $value['id'])->find();
				if ($taskInfo && is_object($taskInfo)) $taskInfo = $taskInfo->toArray();
				// 删除图片
		//		if ($taskInfo['examine_demo']) {
		//			$taskInfo['examine_demo'] = json_decode($taskInfo['examine_demo'], true);
		//			foreach ($taskInfo['examine_demo'] as $key => $value) {
		//				unlink('.'.$value);
		//			}
		//		}
				// 删除图片
		//		if ($taskInfo['task_step']) {
		//			$taskInfo['task_step'] = json_decode($taskInfo['task_step'], true);
		//			foreach ($taskInfo['task_step'] as $key => $value) {
		//				unlink('.'.$value['img']);
		//			}
		//		}

				$res[] = $this->where('id', $value['id'])->delete();
			}
		} elseif (isset($param['id']) && $param['id']) { // 删除单个
			// 提取信息
			$taskInfo = $this->where('id', $param['id'])->find();
			if ($taskInfo && is_object($taskInfo)) $taskInfo = $taskInfo->toArray();

			// 删除图片
		//	if ($taskInfo['examine_demo']) {
		//		$taskInfo['examine_demo'] = json_decode($taskInfo['examine_demo'], true);
		//		foreach ($taskInfo['examine_demo'] as $key => $value) {
		//			unlink('.'.$value);
		//		}
		//	}
			// 删除图片
		//	if ($taskInfo['task_step']) {
		//		$taskInfo['task_step'] = json_decode($taskInfo['task_step'], true);
		//		foreach ($taskInfo['task_step'] as $key => $value) {
		//			unlink('.'.$value['img']);
		//		}
		//	}

			$res = $this->where('id', $param['id'])->delete();
			if (!$res) return '删除失败';
		} else {
			return '提交失败';
		}

		return 1;
	}

	/**
	 * 审核
	 * @return [type] [description]
	 */
	public function audit(){
		if (!request()->isAjax()) return '提交失败';

		$param = input('param.');
		if (!$param || !isset($param['id']) || !$param['id']) return '提交失败';

		$updateArray = [];

		if (isset($param['status']) && $param['status']) $updateArray['status']  = $param['status'];

		if (isset($param['remarks']) && $param['remarks']) $updateArray['remarks'] = $param['remarks'];

		$res = $this->where('id', $param['id'])->update($updateArray);

		model('Actionlog')->actionLog(session('manage_username'),'审核任务：'.$param['id'],1);

		if (!$res) return '提交失败';

		if (isset($param['status']) && $param['status']){
			//审核未通过
			switch($param['status']){
				case 5://撤销
					$info 		= $this->where(array(['id','=',$param['id']]))->find();
					//会员发布的
					if($info['uid']){
						//任务完成了几次
						$count = model('UserTask')->where(array(['task_id','=',$param['id']],['status','=',3]))->count();

						$r_number = $info['total_number']	-	$count;

						if($r_number > 0){
							$userinfo	= model('Users')->field('ly_users.id,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.id', $info['uid'])->find();
							if($userinfo){

								$total_price		=	$r_number*$info['reward_price']	+ $r_number*$info['reward_price']*($info['pump']/100);

								if($total_price>0){

									$is_up_to = model('UserTotal')->where('uid', $userinfo['id'])->Inc('balance', $total_price);

									if(!$is_up_to){
										$this->where(array(['id','=',$param['id']],['status','=',2]))->update(array('status'=>1));//审核中
										return '提交失败';
									}

									// 流水
									$financial_data_p['uid'] 					= $userinfo['id'];
									$financial_data_p['username'] 				= $userinfo['username'];
									$financial_data_p['order_number'] 			= $info['order_number'];
									$financial_data_p['trade_number'] 			= 'L'.trading_number();;
									$financial_data_p['trade_type'] 			= 10;
									$financial_data_p['trade_before_balance']	= $userinfo['balance'];
									$financial_data_p['trade_amount'] 			= $total_price;
									$financial_data_p['account_balance'] 		= $userinfo['balance'] + $total_price;
									$financial_data_p['remarks'] 				= '撤销任务';
									$financial_data_p['types'] 					= 1;	// 用户1，商户2
									model('common/TradeDetails')->tradeDetails($financial_data_p);
								}
							}
						}
					}

				break;
				case 2:
					$info 		= $this->where(array(['id','=',$param['id']],['status','=',2]))->find();
					if(!$info){
						$this->where(array(['id','=',$param['id']],['status','=',2]))->update(array('status'=>1));//审核中
						return '提交失败';
					}

					$userinfo	= model('Users')->field('ly_users.id,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.id', $info['uid'])->find();
					if($userinfo){

						$total_price		=	$info['total_price']	+	$info['task_pump'];

						if($total_price>0){

							$is_up_to = model('UserTotal')->where('uid', $userinfo['id'])->Inc('balance', $total_price);

							if(!$is_up_to){
								$this->where(array(['id','=',$param['id']],['status','=',2]))->update(array('status'=>1));//审核中
								return '提交失败';
							}

							// 流水
							$financial_data_p['uid'] 					= $userinfo['id'];
							$financial_data_p['username'] 				= $userinfo['username'];
							$financial_data_p['order_number'] 			= $info['order_number'];
							$financial_data_p['trade_number'] 			= 'L'.trading_number();;
							$financial_data_p['trade_type'] 			= 10;
							$financial_data_p['trade_before_balance']	= $userinfo['balance'];
							$financial_data_p['trade_amount'] 			= $total_price;
							$financial_data_p['account_balance'] 		= $userinfo['balance'] + $total_price;
							$financial_data_p['remarks'] 				= '撤销任务';
							$financial_data_p['types'] 					= 1;	// 用户1，商户2
							model('common/TradeDetails')->tradeDetails($financial_data_p);
						}
					}

				break;
			}
		}



		return 1;
	}
	/**
		任务订单审核
	**/

	public function userTaskAudit(){
		if (!request()->isAjax()) return '提交失败1';
		$param = input('param.');
		if (!$param) return '提交失败2';
		/**
		 * 批量审核
		 */
		if (isset($param['data']) && is_array($param['data'])) return $this->userTaskBatchAudit($param);

		if (!$param || !isset($param['id']) || !$param['id']) return '提交失败2';

		$updateArray = [];

		if (isset($param['status']) && $param['status']) $updateArray['status']  = $param['status'];

		if (isset($param['handle_remarks']) && $param['handle_remarks']) $updateArray['handle_remarks'] = $param['handle_remarks'];//说明

		$nowTime	= time();

		$updateArray['handle_time']		= $nowTime;

		$updateArray['complete_time']	= $nowTime;
		
		$task_info = model('UserTask')->field('ly_task.order_number,ly_task.reward_price,ly_task.total_number,ly_user_task.status,ly_user_task.uid,ly_user_task.task_id')->join('ly_task','ly_task.id=ly_user_task.task_id')->where('ly_user_task.id',$param['id'])->find();//完成
		
		if (!$task_info) return '提交失败3';
		
		$userinfo		= model('Users')->field('ly_users.id,ly_users.vip_level,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.id', $task_info['uid'])->find();
		
		if (!$userinfo) return '提交失败3';
		
		if($param['status']==2){
			$res = model('UserTask')->where(array(['id','=',$param['id']],['status','=',4]))->update($updateArray);//状态2 审核中的订单才能审核
		}else{
			$res = model('UserTask')->where(array(['id','=',$param['id']],['status','=',2]))->update($updateArray);//状态2 审核中的订单才能审核
		}
		
		if (!$res) return '提交失败3';

		if (isset($param['status']) && $param['status']){//审核

			$UserDailydata	=	array();
			switch($updateArray['status']){
				
				case 3://完成

					//任务提成
					$commission		=	$task_info['reward_price'];//任务单价

					if($commission > 0){

						$userinfo		= model('Users')->field('ly_users.id,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.id', $task_info['uid'])->find();

						if(!$userinfo){
							$up_trial_data_r	=	array(
								'status'			=>	2,//审核
								'handle_time'		=>	time(),
							);
							model('UserTask')->where('id', $param['id'])->update(array('status'=>2));//变审核
							return '提交失败7';
						}
						//加余额钱
						$is_up_to = model('UserTotal')->where('uid', $userinfo['id'])->setInc('balance', $commission);

						if(!$is_up_to){
							$up_trial_data_r	=	array(
								'status'			=>	2,//审核
								'handle_time'		=>	time(),
							);
							model('UserTask')->where('id', $param['id'])->update(array('status'=>2));//变审核
							return '提交失败8';
						}
						//加总金额
						model('UserTotal')->where('uid', $userinfo['id'])->setInc('total_balance', $commission);
						// 流水
						$financial_data_p['uid'] 					= $userinfo['id'];
						$financial_data_p['sid'] 					= $userinfo['sid'];
						$financial_data_p['username'] 				= $userinfo['username'];
						$financial_data_p['order_number'] 			= $task_info['order_number'];
						$financial_data_p['trade_number'] 			= 'L'.trading_number();
						$financial_data_p['trade_type'] 			= 6;
						$financial_data_p['trade_before_balance']	= $userinfo['balance'];
						$financial_data_p['trade_amount'] 			= $commission;
						$financial_data_p['account_balance'] 		= $userinfo['balance'] + $commission;
						$financial_data_p['remarks'] 				= '完成任务';
						$financial_data_p['types'] 					= 1;	// 用户1，商户2

						model('common/TradeDetails')->tradeDetails($financial_data_p);

						//已经完成的 和 总的任务数 一样 更新任务 完成

						$y_surplus_number	=	model('UserTask')->where(array(['task_id','=',$task_info['task_id']],['status','=',3]))->count();

						if($y_surplus_number==$task_info['total_number']){
							$arr = array(
								'status'			=>4,//完成
								'complete_time'		=>	time(),//完成时间
							);
							$this->where(array(['id','=',$task_info['task_id']],['status','=',3]))->update($arr);
						}

						//上级返点
						if($userinfo['sid']){
							$rebatearr = array(
								'num'			=>	1,
								'uid'			=>	$userinfo['id'],
								'sid'			=>	$userinfo['sid'],
								'order_number'	=>	$task_info['order_number'],
								'commission'	=>	$commission,
							);

							$this->setrebate($rebatearr);
						}
					}
					//更新每日完成任务次数
					$UserDailydata = array(
						'uid'				=>	$userinfo['id'],
						'username'			=>	$userinfo['username'],
						'field'				=>	'w_t_o_n',//完成
						'value' 			=>	1,
					);

				break;
				case 4://失败

					//退回任务次数
					$this->where('id', $task_info['task_id'])->dec('surplus_number')->inc('receive_number')->update();

					//更新每日失败任务次数
					$UserDailydata = array(
						'uid'				=>	$userinfo['id'],
						'username'			=>	$userinfo['username'],
						'field'				=>	's_t_o_n',//失败
						'value' 			=>	1,
					);
				break;
				case 5://恶意
					
					//退回任务次数
					$this->where('id', $task_info['task_id'])->dec('surplus_number')->inc('receive_number')->update();
					//更新每日恶意任务次数
					$UserDailydata = array(
						'uid'				=>	$userinfo['id'],
						'username'			=>	$userinfo['username'],
						'field'				=>	'e_t_o_n',//恶意
						'value' 			=>	1,
					);
				break;
			}

			if($UserDailydata){
				model('UserDaily')->updateReportfield($UserDailydata);
			}
		}

		model('Actionlog')->actionLog(session('manage_username'),'审核订单：'.$param['id'],1);

		return 1;
	}

	/**
	 * 批量审核
	 */
	public function userTaskBatchAudit($param=[]){
		if (!$param) return '提交失败';

		foreach ($param['data'] as $key => $value) {
			$updateArray 	= [];
			$UserDailydata	= array();
			if (isset($param['status']) && $param['status']) $updateArray['status']  = $param['status'];
			$updateArray['handle_time']		= time();
			$updateArray['complete_time']	= time();
			
			$task_info = model('UserTask')->field('task.order_number,task.reward_price,task.total_number,ly_user_task.status,ly_user_task.uid,ly_user_task.task_id')->join('task','task.id=ly_user_task.task_id')->where('ly_user_task.id',$value['id'])->find();//完成
			if (!$task_info) continue;
			
			$userinfo		= model('Users')->field('ly_users.id,ly_users.vip_level,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.id', $task_info['uid'])->find();
			
			if (!$userinfo) continue;

			if($param['status']==2){
				$res = model('UserTask')->where(array(['id','=',$value['id']],['status','=',4]))->update($updateArray);//状态4 失败才能重审
			}else{
				$res = model('UserTask')->where(array(['id','=',$value['id']],['status','=',2]))->update($updateArray);//状态2 审核中的订单才能审核
			}
			if (!$res) continue;

			if (isset($param['status']) && $param['status']){//审核

				switch($updateArray['status']){

					case 3: // 完成
						
						//任务提成
						$commission = $task_info['reward_price'];//任务单价

						if($commission > 0){
							
							//加余额钱
							$is_up_to = model('UserTotal')->where('uid', $userinfo['id'])->setInc('balance', $commission);

							if(!$is_up_to){
								model('UserTask')->where('id', $value['id'])->update(['status'=>2]);//变审核
								continue;
							}
							//加总金额
							
							// 流水
							$financial_data_p['uid']                  = $userinfo['id'];
							$financial_data_p['sid']                  = $userinfo['sid'];
							$financial_data_p['username']             = $userinfo['username'];
							$financial_data_p['order_number']         = $task_info['order_number'];
							$financial_data_p['trade_number']         = 'L'.trading_number();
							$financial_data_p['trade_type']           = 6;
							$financial_data_p['trade_before_balance'] = $userinfo['balance'];
							$financial_data_p['trade_amount']         = $commission;
							$financial_data_p['account_balance']      = $userinfo['balance'] + $commission;
							$financial_data_p['remarks']              = '完成任务';
							$financial_data_p['types']                = 1;	// 用户1，商户2

							model('common/TradeDetails')->tradeDetails($financial_data_p);

							//已经完成的 和 总的任务数 一样 更新任务 完成

							$y_surplus_number =	model('UserTask')->where(array(['task_id','=',$task_info['task_id']],['status','=',3]))->count();

							if($y_surplus_number == $task_info['total_number']){
								$this->where(array(['id','=',$task_info['task_id']],['status','=',3]))->update(['status'=>4, 'complete_time'=>time()]);
							}

							//上级返点
							if($userinfo['sid']){
								$rebatearr = array(
									'num'			=>	1,
									'uid'			=>	$userinfo['id'],
									'sid'			=>	$userinfo['sid'],
									'order_number'	=>	$task_info['order_number'],
									'commission'	=>	$commission,
								);

								$this->setrebate($rebatearr);
							}
						}
						//更新每日恶意任务次数
						$UserDailydata = array(
							'uid'				=>	$userinfo['id'],
							'username'			=>	$userinfo['username'],
							'field'				=>	'w_t_o_n',//完成
							'value' 			=>	1,
						);

					break;
					case 4:
						$this->where('id', $task_info['task_id'])->dec('surplus_number')->inc('receive_number')->update();
						//更新每日恶意任务次数
						$UserDailydata = array(
							'uid'				=>	$userinfo['id'],
							'username'			=>	$userinfo['username'],
							'field'				=>	's_t_o_n',//失败
							'value' 			=>	1,
						);
					break;
					case 5:
						//更新每日恶意任务次数
						$UserDailydata = array(
							'uid'				=>	$userinfo['id'],
							'username'			=>	$userinfo['username'],
							'field'				=>	'e_t_o_n',//恶意
							'value' 			=>	1,
						);
						$this->where('id', $task_info['task_id'])->dec('surplus_number')->inc('receive_number')->update();
						
					break;
				}
				if($UserDailydata){
					model('UserDaily')->updateReportfield($UserDailydata);
				}
			}
			model('Actionlog')->actionLog(session('manage_username'),'审核订单：'.$value['id'],1);
		}

		return 1;
	}

	//返点
	public function setrebate($param){
		if($param['num']<4){//上三级

			$rebate			=	model('Setting')->where('id',1)->value('rebate'.$param['num']);//返点值
			if($rebate){

				$rebate_amount	=	round($param['commission']*($rebate/100),2);

				if($rebate_amount>0){
					$userinfo = model('Users')->field('ly_users.id,ly_users.vip_level,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.id', $param['sid'])->find();

					if($userinfo){

						$is_up_to = model('UserTotal')->where('uid', $userinfo['id'])->setInc('balance', $rebate_amount);

						if($is_up_to){
							model('UserTotal')->where('uid', $userinfo['id'])->setInc('total_balance', $rebate_amount);
							// 流水
							$financial_data_p['uid'] 					= $userinfo['id'];
							$financial_data_p['sid']					= $param['uid'];
							$financial_data_p['username'] 				= $userinfo['username'];
							$financial_data_p['order_number'] 			= $param['order_number'];
							$financial_data_p['trade_number'] 			= 'L'.trading_number();
							$financial_data_p['trade_type'] 			= 5;
							$financial_data_p['trade_before_balance']	= $userinfo['balance'];
							$financial_data_p['trade_amount'] 			= $rebate_amount;
							$financial_data_p['account_balance'] 		= $userinfo['balance'] + $rebate_amount;
							$financial_data_p['remarks'] 				= '下级返点';
							$financial_data_p['types'] 					= 1;	// 用户1，商户2

							model('common/TradeDetails')->tradeDetails($financial_data_p);
						}
					}
					if($userinfo['sid']){
						$rebatearr = array(
							'num'			=>	$param['num']+1,
							'uid'			=>	$userinfo['id'],
							'sid'			=>	$userinfo['sid'],
							'order_number'	=>	$param['order_number'],
							'commission'	=>	$param['commission'],
						);
						$this->setrebate($rebatearr);
					}
				}
			}
		}
	}

}
