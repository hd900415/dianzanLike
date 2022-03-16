<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 用户列表的相关操作
 */

namespace app\manage\model;

use think\Model;
use app\api\model\ApiModel;

class UsersModel extends Model{
	//表名
	protected $table = 'ly_users';
	/**
	 * 用户列表
	 */
	public function userList(){
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		// $pageParam = array();
		$pageUrl = "";
		//用户名搜索
		if(isset($param['username']) && $param['username']){
			//$where[] = array('username','like','%'.trim($param['username']).'%');
			$where[] = array('username','=',$param['username']);
			// $pageParam['username'] = $param['username'];
			$pageUrl .= '&username='.$param['username'];
		}
		//用户名搜索
		if(isset($param['uid']) && $param['uid']){
			//$where[] = array('uid','like','%'.trim($param['uid']).'%');
			$where[] = array('ly_users.uid','=',trim($param['uid']));
			// $pageParam['uid'] = $param['uid'];
			$pageUrl .= '&uid='.$param['uid'];
		}
		//用户名搜索
		if(isset($param['balance1']) && $param['balance1']){
			$where[] = array('user_total.balance','>=',trim($param['balance1']));
			// $pageParam['balance1'] = $param['balance1'];
			$pageUrl .= '&balance1='.$param['balance1'];
		}
		//用户名搜索
		if(isset($param['balance2']) && $param['balance2']){
			$where[] = array('user_total.balance','<=',trim($param['balance2']));
			// $pageParam['balance2'] = $param['balance2'];
			$pageUrl .= '&balance2='.$param['balance2'];
		}
		//用户状态搜索
		if(isset($param['state']) && $param['state']){
			$where[] = array('state','=',$param['state']);
			// $pageParam['state'] = $param['state'];
			$pageUrl .= '&state='.$param['state'];
		}
		//用户接单状态搜索
		if(isset($param['is_automatic']) && $param['is_automatic']){
			$where[] = array('is_automatic','=',$param['is_automatic']);
			// $pageParam['state'] = $param['state'];
			$pageUrl .= '&is_automatic='.$param['is_automatic'];
		}

		//用户注册时间搜索
		if(isset($param['datetime_range']) && $param['datetime_range']){
			$dateTime = explode(' - ', $param['datetime_range']);
			$where[] = array('reg_time','>=',strtotime($dateTime[0]));
			$where[] = array('reg_time','<=',strtotime($dateTime[1]));
			// $pageParam['datetime_range'] = $param['datetime_range'];
			$pageUrl .= '&datetime_range='.$param['datetime_range'];
		}
		//查询符合条件的数据
		$userList = $this->field('ly_users.*,user_total.balance,user_total.total_balance')->join('user_total','ly_users.id = user_total.uid','left')->where($where)->order('ly_users.id','desc')->select()->toArray();
		//部分元素重新赋值
		$userType = config('custom.userType');//账号类型
		$userState = config('custom.userState');//账号状态
		foreach ($userList as $key => &$value) {
			$value['state'] = $userState[$value['state']];
			$value['isOnline'] = (cache('C_token_'.$value['id'])) ? '在线' : '离线';
		}
		// 在离状态
		if(isset($param['isonline']) && $param['isonline']){
			foreach ($userList as $key => &$value) {
				if ($param['isonline'] == 1 && $value['isOnline'] != '在线') unset($userList[$key]);
				if ($param['isonline'] == 2 && $value['isOnline'] != '离线') unset($userList[$key]);
			}
			$pageParam['isonline'] = $param['isonline'];
		}

		//分页
		$pageNum  = isset($param['page']) && $param['page'] ? $param['page'] : 1 ;
		$pageInfo = model('ArrPage')->page($userList, 15, $pageNum, $pageUrl);
		$page     = $pageInfo['links'];
		$source   = $pageInfo['source'];

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',2],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'where'     =>	$param,
			'userList'  =>	$source,//数据
			'count'     => count($userList),
			'page'      =>	$page,//分页
			'userState' =>	$userState,//账号状态
			'power'     =>	$power,//权限
		);
	}
	/**
	 * 用户添加
	 */
	public function add(){
		$param = input('post.');

		//数据验证
		$validate = validate('app\manage\validate\Users');
		if(!$validate->scene('add')->check($param)){
			return $validate->getError();
		}
		// 获取所有用户idocde
		$idCode      = $this->column('idcode');
		$checkIdcode = false;
		do {
			// 生成idcode
			list($msec, $sec) = explode(' ', microtime());
			$msectime         = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
			$newIdcode        = substr($msectime,-7);// 邀请码
			if (!in_array($newIdcode, $idCode)) $checkIdcode = true;
		} while (!$checkIdcode);

		$param['uid']      		= $newIdcode;
		$param['idcode']   		= $newIdcode;
		$param['phone']    		= $param['username'];
		$param['password'] 		= auth_code($param['password'],'ENCODE');
		$param['header'] 		= 'head_1.png';
		$param['vip_level'] 	= 1;
		$param['credit'] 		= 60;
		$param['reg_time'] 		= time();
		//添加用户数据
		$insertUsers = $this->allowField(true)->save($param);
		if(!$insertUsers) return '添加失败';
		$insertId = $this->id;

		//将该账户添加至user_total表
		$userTotal = model('UserTotal');
		$insertTotalId = $userTotal->insertGetId(array('uid'=>$insertId));
		if(!$insertTotalId){
			$this->destroy($this->id);
			return '添加失败';
		}
		//添加至user_team表
		$insertTeamId = model('UserTeam')->insertGetId(array('uid'=>$insertId,'team'=>$insertId));
		if(!$insertTeamId){
			$this->destroy($insertId);
			$userTotal->where('id',$insertTotalId)->delete();
			return '添加失败';
		}
		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'添加用户名为'.$param['username'].'的用户',1);

		return 1;
	}

	/**
	 * 用户编辑提交
	 */
	public function edit(){
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		//参数过滤
		// $array = $param;
		$array = array_filter($param);
		//提取用户ID
		$uid = $array['id'];
		unset($array['id']);
		//获取用户信息（用于操作日志）
		$userInfo = $this->where('id','=',$uid)->find();
		// 用户状态
		if (isset($array['state']) && $array['state']) {
			switch ($array['state']) {
				// 冻结
				case 3:
					if (cache('C_token_'.$uid)) cache('C_token_'.$uid, NULL);
					break;
				// 踢下线
				case 4:
					if (cache('C_token_'.$uid)) cache('C_token_'.$uid, NULL);
					unset($array['state']);
					break;
			}
		}
		//修改vip等级
		
		
		if($userInfo['vip_level'] != $array['vip_level']){

			if($array['vip_level']>1){
				$start_time = strtotime(date("Y-m-d",time()));//当天的时间错
				$UserVip		=	model('UserVip')->where(array(['uid','=',$userInfo['id']],['state','=',1],['etime','>=',$start_time]))->find();
				$GradeInfo			=	model('UserGrade')->where('grade', $array['vip_level'])->find();
				if($UserVip){
					$vipdate = array(
						'en_name'	=>	$GradeInfo['en_name'],
						'name'		=>	$GradeInfo['name'],
						'grade'		=>	$array['vip_level'],
						'stime'		=>	$start_time,
						'etime'		=>	$start_time	+	356 * 24 * 3600,
					);
					model('UserVip')->where(array(['id','=',$UserVip['id']]))->update($vipdate);
				}else{
					$newData	= [
						'username'	=> $userInfo['username'],
						'uid'		=> $userInfo['id'],
						'state'		=> 1,
						'name'		=> $GradeInfo['name'],
						'en_name'	=> $GradeInfo['en_name'],
						'grade'		=> $array['vip_level'],
						'stime'		=> $start_time,
						'etime'		=> $start_time + 365 * 24 * 3600,
					];
					model('UserVip')->insertGetId($newData);
				}
			}
		}

		// 账户密码加密
		if (isset($array['password']) && $array['password']) $array['password'] = auth_code($array['password'],'ENCODE');
		// 安全密码加密
		if (isset($array['fund_password']) && $array['fund_password']) $array['fund_password'] = auth_code($array['fund_password'],'ENCODE');
		// 信用分
		//$credit = 0;
		//if (isset($array['credit']) && $array['credit']) {
			//$credit = $array['credit'];
			//unset($array['credit']);
		//}
		// 数据更新
		$res2 = $this->where('id', $uid)->update($array);
		
		//解绑支付宝
		if($param['alipay']==''){
			$alipayarray	=	array(
				'alipay'		=>	'',
				'alipay_name'	=>	'',
			);
			$this->where('id', $uid)->update($alipayarray);
		}
		//解除实名认证
		if($param['realname']==''){
			$alipayarray	=	array(
				'realname'		=>	'',
			);
			$this->where('id', $uid)->update($alipayarray);
		}
		//日志内容
		$logContent = '编辑用户名为'.$userInfo['username'].'的用户，';
		foreach ($array as $key => $value) {
			if (!isset($userInfo[$key]) || $userInfo[$key] == $value) continue;
			switch ($key) {
				case 'vip_level':
					$grade1 = model('UserGrade')->field('grade,name')->where('id','>',0)->select()->toArray();
					foreach ($grade1 as $key => $value) {
						$grade2[$value['grade']] = $value['name'];
					}
					$logContent .= '用户VIP等级由'.$grade2[$userInfo['vip_level']].'调整为'.$grade2[$array['vip_level']].'，';
					break;
				case 'state':
					$logContent .= '用户状态由'.config('custom.userState')[$userInfo['state']].'调整为'.config('custom.userState')[$array['state']].'，';
					break;
				case 'grade':
					$logContent .= '会员积分等级由'.$userInfo['grade'].'修改为'.$array['grade'].'，';
					// $logContent .= '会员积分由'.$userInfo['experience'].'修改为'.$array['experience'].'，';
					break;
				case 'password':
					$logContent .= '登录密码由'.auth_code($userInfo['password'],'DECODE').'修改为'.auth_code($array['password'],'DECODE').'，';
					break;
				case 'fund_password':
					$logContent .= '取款密码由'.auth_code($userInfo['fund_password'],'DECODE').'修改为'.auth_code($array['fund_password'],'DECODE').'，';
					break;
				case 'rebate':
					$logContent .= '返点由'.$userInfo['rebate'].'修改为'.$array['rebate'].'，';
					break;
				case 'credit':
					$logContent .= '信用分由'.$userInfo['credit'].'调整为'.$array['credit'].'，';
					break;
				case 'alipay':
					$logContent .= '支付宝账号由'.$userInfo['alipay'].'调整为'.$array['alipay'].'，';
					break;
				case 'alipay_name':
					$logContent .= '支付宝名称由'.$userInfo['alipay_name'].'调整为'.$array['alipay_name'].'，';
					break;
				case 'realname':
					$logContent .= '实名认证由'.$userInfo['realname'].'调整为'.$array['realname'].'，';
					break;
				case 's_hb':
					$logContent .= '私发状态由'.($userInfo['s_hb'] == 1 ? '允许' : '不允许').'调整为'.($array['s_hb'] == 1 ? '允许' : '不允许').'，';
					break;
				case 'is_auto_f':
					$logContent .= '自动添加好友由'.($userInfo['is_auto_f'] == 1 ? '允许' : '不允许').'调整为'.($array['is_auto_f'] == 1 ? '允许' : '不允许').'，';
					break;
				case 'withdrawals_state':
					$logContent .= '提现状态由'.($userInfo['withdrawals_state'] == 1 ? '开启' : '关闭').'调整为'.($array['withdrawals_state'] == 1 ? '开启' : '关闭').'，';
					break;
			}
		}
		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),$logContent,1);

		return 1;
	}
	/**
	 * 用户编辑视图
	 */
	public function editView(){
		$uid = input('get.id');//获取参数
		$data = $this->where('id','=',$uid)->find();//获取用户信息
		//获取该用户所有上级
		$getUserSupArray = $this->getUserUp($data['sid'],'id,sid,username');
		$userSup = '';
		foreach ($getUserSupArray as $key => $value) {
			$userSup .= $value['username'].' > ';
		}
		$data['userSup'] = rtrim($userSup,' > ');
		//用户总金额信息
		$data['userTotal'] = model('UserTotal')->field('id',true)->where('uid','=',$uid)->find();
		//最近一次充值
		$lastRecharge = model('UserRecharge')->field('add_time')->where('uid','=',$uid)->order('add_time','desc')->limit(1)->find();
		$data['LastRecharge'] = $lastRecharge['add_time'] ? date('Y-m-d H:i:s',$lastRecharge['add_time']) : '';
		//用户银行
		$data['bankInfo'] = model('UserBank')->where('uid',$uid)->order('id','desc')->limit(1)->find();
		//用户银行
		$data['userVip'] = model('UserGrade')->select()->toArray();

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',2],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);
		return array(
			'userInfo'			=>	$data,
			'power'				=>	$power,
			'userState'			=>	config('custom.userState'),//账号状态
		);
	}
	/**
	 * 后台码商
	 */
	public function isAdmin(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		$userInfo = $this->field('username')->where('id',$param['uid'])->find();
		//更新
		$updateRes = $this->where('id',$param['uid'])->setField('is_admin',$param['value']);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '非' : '';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$userInfo['username'].'设为'.$actionStr.'后台码商',1);

		return 1;
	}
	/**
	 * 风险账号
	 */
	public function risk(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		$userInfo = $this->field('username')->where('id',$param['uid'])->find();
		//更新
		$updateRes = $this->where('id',$param['uid'])->setField('danger',$param['value']);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '非' : '';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$userInfo['username'].'设为'.$actionStr.'风险账号',1);

		return 1;
	}
	/**
	 * 工资
	 */
	public function wages(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		$userInfo = $this->field('username')->where('id',$param['uid'])->find();
		//更新
		$updateRes = $this->where('id',$param['uid'])->setField('iswage',$param['value']);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '关闭' : '开启';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$userInfo['username'].'的工资设为'.$actionStr,1);

		return 1;
	}
	/**
	 * 亏损工资
	 */
	public function loss(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		$userInfo = $this->field('username')->where('id',$param['uid'])->find();
		//更新
		$updateRes = $this->where('id',$param['uid'])->setField('losswage',$param['value']);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '关闭' : '开启';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$userInfo['username'].'的亏损工资设为'.$actionStr,1);

		return 1;
	}
	/**
	 * 分红
	 */
	public function bonus(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		$userInfo = $this->field('username')->where('id',$param['uid'])->find();
		//更新
		$updateRes = $this->where('id',$param['uid'])->setField('isbonus',$param['value']);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '关闭' : '开启';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$userInfo['username'].'的分红设为'.$actionStr,1);

		return 1;
	}
	/**
	 * 锁定
	 */
	public function locking(){
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		$userInfo = $this->field('username')->where('id',$param['uid'])->find();
		//更新
		$updateRes = $this->where('id',$param['uid'])->setField('islock',$param['value']);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '未锁' : '锁定';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$userInfo['username'].'的锁定状态设为'.$actionStr,1);

		return 1;
	}
	/**
	 * 账号锁定
	 */
	public function lockAccount(){
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		$userInfo = $this->field('username')->where('id',$param['uid'])->find();
		//更新
		$val = ($param['value'] == 1) ? 2 : 1;
		$updateRes = $this->where('id',$param['uid'])->setField('state', $val);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '锁定' : '解锁';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$userInfo['username'].$actionStr,1);

		return 1;
	}

	/**
	 * 修改单个字段
	 */
	public function setFieldValue(){
		$param = input('post.');//获取参数
		if (!$param || !isset($param['id']) || !isset($param['field']) || !isset($param['value'])) return '提交失败';

		//提取信息
		$userName = $this->where('id', '=', $param['uid'])->value('username');

		//更新
		$res = $this->where('id', '=', $param['uid'])->setField($param['field'], $param['value']);
		if (!$res) return '操作失败';


		switch ($param['field']) {
			case 'state':
				$logStr = $param['value']==2 ? '非' : '';
				$logContent = $userName.'设为'.$logStr.'锁定';
				break;
			case 'danger':
				$logStr = $param['value']==2 ? '非' : '';
				$logContent = $userName.'设为'.$logStr.'风险账号';
				break;
			case 'iswage':
				$logStr = ($param['value'] == 1) ? '开启' : '关闭' ;
				$logContent = $userName.'的工资设为'.$logStr;
				break;
			case 'losswage':
				$logStr = ($param['value'] == 1) ? '开启' : '关闭' ;
				$logContent = $userName.'的亏损工资设为'.$logStr;
				break;
			case 'isbonus':
				$logStr = ($param['value'] == 1) ? '开启' : '关闭' ;
				$logContent = $userName.'的分红设为'.$logStr;
				break;
			case 'islock':
				$logStr = ($param['value'] == 1) ? '锁定' : '未锁' ;
				$logContent = $userName.'的锁定状态设为'.$logStr;
				break;
		}

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'), '将账号'.$logContent, 1);

		return 1;
	}


	/**
	 * 解锁视图
	 */
	public function lockView(){
		$param = input('get.');//获取参数
		//获取用户安全问题
		$questionAndaAnswer = $this->field('question,answer')->where('id','=',$param['uid'])->find();

		return array(
			'param'	=>	$param,
			'questionAndaAnswer'	=>	$questionAndaAnswer,
		);
	}
	/**
	 * 删除
	 */
	public function del(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';

		$delRes = model($param['table'])->where('id','=',$param['id'])->delete();
		if(!$delRes) return '删除失败';

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'删除'.$param['name'],1);

		return 1;
	}

	/**
	 * 代理迁移
	 */
	public function teamMove(){
		if(!request()->isAjax()) return '非法提交';

		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		//参数过滤
		$array = array_filter($param);
		//获取管理员ID
		$aid = session('manage_userid');

		//数据验证
		// $validate = validate('app\manage\validate\Users');
		// if(!$validate->scene('teamMove')->check([
		// 	'bUsername'				=>	(isset($array['bqusername'])) ? $array['bqusername'] : '',
		// 	'artificialUsername'	=>	(isset($array['qusername'])) ? $array['qusername'] : '',
		// 	'artificialSafeCode'	=>	(isset($array['safe_code'])) ? $array['safe_code'] : '',
		// ])){
		// 	return $validate->getError();
		// }
		//获取被转移用户信息
		$name1 = $this->where('username',$array['bqusername'])->field('id,sid,vip_level,rebate')->find();
		//获取即将转移到的用户信息
		$name2 = $this->where('username',$array['qusername'])->field('id,sid,vip_level,rebate')->find();
		if(!$name1 || !$name2) return '用户不存在，请核对后再操作';
		//获取即将被迁移的团队
		$QteamTemp = model('UserTeam')->where('uid',$name1['id'])->field('team')->select();
		$Qteam = array();
		foreach ($QteamTemp as $key => $value) {
			$Qteam[] = $value['team'];
		}
		//判断是否是团队内的上级迁移成下级
		if(in_array($name2['id'],$Qteam)) return '非法操作';

		//获取即将迁移到的团队
		/*
		$ZteamTemp = model('UserTeam')->where('uid',$name2['id'])->field('team')->select();
		$Zteam = array();
		foreach ($ZteamTemp as $key => $value) {
			$Zteam[] = $value['team'];
		}
		*/
		if($name1['sid']){
			//从上级中删除该团队
			$getUserSupName1 = $this->getUserUp($name1['sid'],'id,sid',true);
			foreach ($getUserSupName1 as $key => $value) {
				foreach ($Qteam as $Qkey => $Qvalue) {
					model('UserTeam')->where(array(array('uid','=',$value),array('team','=',$Qvalue)))->delete();
				}
			}
		}
		if($name2['sid']){
			// 新上级团队中添加该团队
			$getUserSupName2 = $this->getUserUp($name2['id'],'id,sid',true);
			foreach ($getUserSupName2 as $key => $value) {
				foreach ($Qteam as $Qkey => $Qvalue) {
					model('UserTeam')->insertGetId(array('uid'=>$value,'team'=>$Qvalue));
				}
			}
		}else{
			foreach ($Qteam as $Qkey => $Qvalue) {
				model('UserTeam')->insertGetId(array('uid'=>$name2['id'],'team'=>$Qvalue));
			}
		}
		//修改上级ID
		$this->where('id',$name1['id'])->update(array('sid'=>$name2['id']));

		//计算并修改会员等级
		// $vipDiff = $name1['vip_level'] - $name2['vip_level'];
		// if($vipDiff > 1){
		// 	foreach ($Qteam as $key => $value) {
		// 		$updateVip[] = $this->where('id',$value)->setDec('vip_level',$vipDiff-1);
		// 	}
		// }elseif($vipDiff <= 0){
		// 	$diffDown = $name2['vip_level'] - $name1['vip_level'] + 1;
		// 	foreach ($Qteam as $key => $value) {
		// 		$updateVip[] = $updateVip[] = $this->where('id',$value)->setInc('vip_level',$diffDown);
		// 	}
		// }
		//返点计算
		// if($name1['rebate']>$name2['rebate']){
		// 	$rebateDiff = $name1['rebate'] - $name2['rebate'];
		// 	foreach ($Qteam as $key => $value) {
		// 		$userRebate = $this->where('id',$value)->field('rebate')->find();
		// 		if($userRebate['rebate'] - $rebateDiff > 0){
		// 			$updateRebate = $userRebate['rebate'] - $rebateDiff;
		// 		}else{
		// 			$updateRebate = 0;
		// 		}
		// 		$this->where('id',$value)->update(array('rebate'=>$updateRebate));
		// 	}
		// }
		//上庄返点计算
		// if($name1['banker_rebate']>$name2['banker_rebate']){
		// 	$rebateDiff = $name1['banker_rebate'] - $name2['banker_rebate'];
		// 	foreach ($Qteam as $key => $value) {
		// 		$userRebate = $this->where('id',$value)->field('banker_rebate')->find();
		// 		if($userRebate['banker_rebate'] - $rebateDiff > 0){
		// 			$updateRebate = $userRebate['banker_rebate'] - $rebateDiff;
		// 		}else{
		// 			$updateRebate = 0;
		// 		}
		// 		$this->where('id',$value)->update(array('banker_rebate'=>$updateRebate));
		// 	}
		// }
		//添加迁移日志
		model('TeammoveLog')->insertGetId(array('aid'=>session('manage_userid'),'addtime'=>time(),'log'=>'迁移'.$param['bqusername'].'至'.$param['qusername']));

		return 1;
	}

	/**
	 * 团队报表
	 */
	public function teamStatistic(){
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageUrl = '';
		$param['isUser'] = (isset($param['isUser'])) ? $param['isUser'] : 1;
		//用户名搜索
		$sid = 0;
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','=',trim($param['username']));
			$pageUrl .= '&username='.$param['username'];
		}else{
			//查看下级
			if(isset($param['sid']) && $param['sid']){
				$idOne = $this->where('id',$param['sid'])->value('sid');
				$idtwo = $this->where('id',$idOne)->value('id');
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
		// 过滤
		$filter = (isset($param['filter']) && $param['filter'] == 'on') ? true : false;
		//查询符合条件的数据
		$userList = $this->field('id,sid,username,state')->where($where)->select()->toArray();
		// print_r($userList);die;
		//用户团队数据计算
		$data = model('UserDaily')->teamStatistic($userList,$startDate,$endDate,$sid,$filter);
		$total['totalAll'] = $data['totalAll'];
		// var_dump($data);die;
		unset($data['totalAll']);
		//var_dump($data);
		//分页
		$pageNum = isset($param['page']) && $param['page'] ? $param['page'] : 1 ;
		$pageInfo = model('ArrPage')->page($data, 10, $pageNum, $pageUrl);
		//var_dump($pageInfo);
		$page = $pageInfo['links'];
		$source = $pageInfo['source'];

		// 分页小计
		$sumField = ['recharge','withdrawal','task','rebate','regment','other','buymembers','spread','pump','revoke','commission'];
		foreach ($sumField as $key => &$value) {
			$total['totalPage'][$value] = 0;
			foreach ($source as $k => $v) {
				$total['totalPage'][$value] += $v[$value];
			}
		}
		//var_dump($source);
		return array(
			'data'  =>	$source,
			'total' =>	$total,
			'page'  =>	$page,//分页
			'where' =>	$param,
		);
	}


	/**
	 * 上庄团队报表
	 */
	public function RoomteamStatistic(){
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		//用户名搜索
		$sid = 0;
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}else{
			//查看下级
			if(isset($param['sid']) && $param['sid']){
				$idOne = $this->field('sid')->where('id',$param['sid'])->find();
				$idtwo = $this->field('id')->where('id',$idOne['sid'])->find();
				$param['id'] = $idtwo['id'];
				$pageParam['sid'] = $param['sid'];
			}
			//查看上级
			if(isset($param['id']) && $param['id']){
				$where[] = array('sid','=',$param['id']);
				$sid = $param['id'];
				$pageParam['id'] = $param['id'];
			}else{
				$where[] = array('sid','=',0);
			}
		}
		//开始时间
		if(isset($param['startdate']) && $param['startdate']){
			$startDate = strtotime($param['startdate']);
			$pageParam['startdate'] = $param['startdate'];
		}else{
			$startDate = mktime(0,0,0,date('m'),date('d'),date('Y')) - 86400;
			$param['startdate'] = date('Y-m-d',$startDate);
		}
		//结束时间
		if(isset($param['enddate']) && $param['enddate']){
			$endDate = strtotime($param['enddate']);
			$pageParam['enddate'] = $param['enddate'];
		}else{
			$endDate = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$param['enddate'] = date('Y-m-d',$endDate);
		}
		//查询符合条件的数据
		$resultData = $this->field('id,sid,username')->where($where)->paginate(16,false,['query'=>$pageParam]);
		//数据集转数组
		$userList = $resultData->toArray()['data'];
		//用户团队数据计算
		$data = model('RoomUserDaily')->teamStatistic($userList,$startDate,$endDate,$sid);

		return array(
			'data'			=>	$data,
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$param,
		);
	}

	/**
	 * 第三方团队
	 */
	public function thirdTeam($type){
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		//用户名搜索
		$sid = 0;
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}else{
			//查看下级
			if(isset($param['sid']) && $param['sid']){
				$idOne = $this->field('sid')->where('id',$param['sid'])->find();
				$idtwo = $this->field('id')->where('id',$idOne['sid'])->find();
				$param['id'] = $idtwo['id'];
				$pageParam['sid'] = $param['sid'];
			}
			//查看上级
			if(isset($param['id']) && $param['id']){
				$where[] = array('sid','=',$param['id']);
				$sid = $param['id'];
				$pageParam['id'] = $param['id'];
			}else{
				$where[] = array('sid','=',0);
			}
		}
		//开始时间
		if(isset($param['startdate']) && $param['startdate']){
			$startDate = strtotime($param['startdate']);
			$pageParam['startdate'] = $param['startdate'];
		}else{
			$startDate = mktime(0,0,0,date('m'),date('d'),date('Y')) - 86400;
			$param['startdate'] = date('Y-m-d',$startDate);
		}
		//结束时间
		if(isset($param['enddate']) && $param['enddate']){
			$endDate = strtotime($param['enddate']);
			$pageParam['enddate'] = $param['enddate'];
		}else{
			$endDate = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$param['enddate'] = date('Y-m-d',$endDate);
		}
		//查询符合条件的数据
		$resultData = $this->field('id,sid,username')->where($where)->paginate(16,false,['query'=>$pageParam]);
		//数据集转数组
		$userList = $resultData->toArray()['data'];
		//用户团队数据计算
		$data = model('UserDailyThird')->teamStatistic($userList,$startDate,$endDate,$sid,$type);

		return array(
			'data'			=>	$data,
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$param,
		);
	}

	/**
	 * 获取用户所有的上级信息
	 * @param  integer $id   要获取的直属上级的用户ID
	 * @param  string $field 需要获取的字段
	 * @param  bool   $getid 是否只返回含有用户ID的一维数组
	 * @param  array  $array
	 * @return array         包含所有上级用户的二维数组
	 */
	public function getUserUp($id,$field='*',$getid=false,$array=array()){
		$userInfo = $this->field($field)->where('id','=',$id)->find();
		if($getid){
			$array[] = $userInfo['id'];
		}else{
			$array[] = $userInfo;
		}
		if(isset($userInfo['sid']) && $userInfo['sid']){
			$array = $this->getUserUp($userInfo['sid'],$field,$getid,$array);
		}
		return $array;
	}

	/**
	 * 人工存提单人处理
	 * @return [type] [description]
	 */
	public function artificialAction(){
		if (!request()->isAjax()) return '非法提交';

		$param = input('post.');
		if (!$param) return'提交失败';

		//数据验证
		$validate = validate('app\manage\validate\Users');
		if(!$validate->scene('artificial')->check([
			'artificialUsername'	=>	(isset($param['username'])) ? $param['username'] : '',
			'artificialPrice'		=>	(isset($param['price'])) ? $param['price'] : '',
			'artificialType'		=>	(isset($param['type'])) ? $param['type'] : '',
			'artificialSafeCode'	=>	(isset($param['safe_code'])) ? $param['safe_code'] : '',
		])){
			return $validate->getError();
		}

		//用户ID
		$userInfo = $this->field('id,user_type')->where('username','=',$param['username'])->find();
		if (!$userInfo) return '用户不存在，请核对后再操作';
		// 提交时间限制
		$artificialTime = cache('CA_artificialTime'.session('manage_userid')) ? cache('CA_artificialTime'.session('manage_userid')) : time()-2;
		if (time() - $artificialTime < 2) return ' 2 秒内不能重复提交';
		cache('CA_artificialTime'.session('manage_userid'), time(), 10);
		//获取用户余额
		$userBalance = model('UserTotal')->where('uid', $userInfo['id'])->value('balance');
		// 金额判断
		if ($param['type'] == 2) {
			if ($param['price'] > 0) $param['price'] = '-'.$param['price'];
			if ($userBalance - abs($param['price']) < 0) return '余额不足';
		}

		//统计类型
		if (isset(config('custom.userTotal')[$param['type']])) $userTotalType = config('custom.userTotal')[$param['type']];
		//更新用户余额、统计金额
		$newUserTotal = model('UserTotal')->where('uid', $userInfo['id'])->inc('balance', $param['price']);
		// $newUserTotal->where('uid', $userInfo['id']);
		// $newUserTotal->inc('balance', $param['price']);
		if (isset($userTotalType) && $userTotalType) $newUserTotal->inc($userTotalType, $param['price']);
		$res = $newUserTotal->update();
		if (!$res) return '操作失败';

		$remarks = (isset($param['remarks'])) ? $param['remarks'] : '';
		//单号生成
		$orderNumber = 'C'.trading_number();
		$tradeNumber = 'L'.trading_number();
		if ($userInfo['user_type'] != 3) {
			switch ($param['type']) {
				case 1:
					//添加充值记录
					$rechargeArray = [
						'uid'			=>	$userInfo['id'],
						'order_number'	=>	$orderNumber,
						'money'			=>	$param['price'],
						'state'			=>	1,
						'add_time'		=>	time(),
						'aid'			=>	session('manage_userid'),
						'dispose_time'	=>	time(),
						'remarks'		=>	$remarks,
					];
					model('UserRecharge')->insertGetId($rechargeArray);
					break;
				case 2:
					//添加提现记录
					$withdrawalsModelArray = [
						'uid'			=>	$userInfo['id'],
						'order_number'	=>	$orderNumber,
						'price'			=>	abs($param['price']),
						'time'			=>	time(),
						'trade_number'	=>	$tradeNumber,
						'examine'		=>	1,
						'state'			=>	1,
						'aid'			=>	session('manage_userid'),
						'remarks'		=>	$remarks,
						'set_time'		=>	time(),
					];
					model('UserWithdrawals')->insertGetId($withdrawalsModelArray);
					break;
			}
		}

		//添加流水
		$tradeDetailsArray = array(
			'uid'					=>	$userInfo['id'],
			'order_number'			=>	$orderNumber,
			'trade_number'			=>	$tradeNumber,
			'trade_type'			=>	$param['type'],
			'trade_amount'			=>	$param['price'],
			'trade_before_balance'	=>	$userBalance,
			'account_balance'		=>	$userBalance + $param['price'],
			'remarks'				=>	$remarks,
			'isadmin'				=>	1,
		);
		model('TradeDetails')->tradeDetails($tradeDetailsArray);

		//添加操作日志
		$userTransactionType = config('custom.transactionType')[$param['type']];//操作类型
		model('Actionlog')->actionLog(session('manage_username'),'通过人工存提为'.$param['username'].$userTransactionType.$param['price'].'元');

		return 1;
	}

	/**
	 * 人工存提批量处理
	 * @return [type] [description]
	 */
	public function artificialBatch(){
		if (!request()->isAjax()) return '非法提交';

		$param = input('post.');
		if (!$param) return'提交失败';

		//数据验证
		$validate = validate('app\manage\validate\Users');
		if(!$validate->scene('artificialBatch')->check([
			'artificialType'		=>	(isset($param['type'])) ? $param['type'] : '',
			'artificialSafeCode'	=>	(isset($param['safe_code'])) ? $param['safe_code'] : '',
		])){
			return $validate->getError();
		}

		//统计类型
		$userTotalType = config('custom.userTotal')[$param['type']];
		if (!$userTotalType) return '请选择操作类型';
		// 用户同级字段
		if (isset(config('custom.userTotal')[$param['type']])) $userTotalType = config('custom.userTotal')[$param['type']];

		$data = session('artificialBatchData');
		// session('artificialBatchData', null);

		$keyArray = $data[1];
		unset($data[1]);

		foreach ($keyArray as $key => $value) {
			switch ($value) {
				case '用户名':
				case '用户':
				case '账户':
				case '账号':
					$usernameKey = $key;
					break;
				case '金额':
					$priceKey = $key;
					break;
				case '说明':
				case '备注':
					$remarksKey = $key;
					break;
			}
		}

		foreach ($data as $key => $value) {
			//用户ID
			$userId = $this->where('username', $value[$usernameKey])->value('id');
			if (!$userId) {
				$error1[] = $key;
				continue;
			}

			//获取用户余额
			$userBalance = model('UserTotal')->where('uid', $userId)->value('balance');

			if ($param['type'] == 2 && $value[$priceKey] > 0) $value[$priceKey] = '-'.$value[$priceKey];
			//更新用户余额、统计金额
			$newUserTotal = model('UserTotal')->where('uid', $userInfo['id'])->inc('balance', $value[$priceKey]);
			if (isset($userTotalType) && $userTotalType) $newUserTotal->inc($userTotalType, $value[$priceKey]);
			$res = $newUserTotal->update();
			if (!$res) {
				$error2[] = $key;
				continue;
			}

			//单号生成
			$orderNumber = 'C'.trading_number();
			$tradeNumber = 'L'.trading_number();

			switch ($param['type']) {
				case 1:
					//添加充值记录
					$rechargeArray = [
						'uid'			=>	$userId,
						'order_number'	=>	$orderNumber,
						'money'			=>	$value[$priceKey],
						'state'			=>	1,
						'add_time'		=>	time(),
						'aid'			=>	session('manage_userid'),
						'dispose_time'	=>	time(),
						'remarks'		=>	(isset($value[$remarksKey])) ? $value[$remarksKey] : '管理员后台操作',
					];
					model('UserRecharge')->insertGetId($rechargeArray);
					break;
				case 2:
					//添加提现记录
					$withdrawalsModelArray = [
						'uid'			=>	$userId,
						'order_number'	=>	$orderNumber,
						'price'			=>	abs($value[$priceKey]),
						'time'			=>	time(),
						'trade_number'	=>	$tradeNumber,
						'examine'		=>	1,
						'state'			=>	1,
						'aid'			=>	session('manage_userid'),
						'remarks'		=>	(isset($value[$remarksKey])) ? $value[$remarksKey] : '管理员后台操作',
						'set_time'		=>	time(),
					];
					model('UserWithdrawals')->insertGetId($withdrawalsModelArray);
					break;
			}

			//添加流水
			$tradeDetailsArray = array(
				'uid'					=>	$userId,
				'trade_type'			=>	$param['type'],
				'trade_amount'			=>	$value[$priceKey],
				'trade_before_balance'	=>	$userBalance,
				'account_balance'		=>	$userBalance + $value[$priceKey],
				'remarks'				=>	(isset($value[$remarksKey]) && $value[$remarksKey]) ? $value[$remarksKey] : '管理员操作' ,
				'isadmin'				=>	1,
			);
			model('TradeDetails')->tradeDetails($tradeDetailsArray);

			//添加操作日志
			model('Actionlog')->actionLog(session('manage_username'),'通过批量人工存提为'.$value[$usernameKey].$userTransactionType.$value[$priceKey].'元');
		}

		$errorStr = '';
		if (isset($error1) && $error1) {
			$error1Row = rtrim(implode('、', $error1), '、');
			$errorStr .= '第'.$error1Row.'行的用户不存在';
		}
		if (isset($error2) && $error2) {
			$comma = ($errorStr) ? '，' : '' ;
			$error2Row = rtrim(implode('、', $error2), '、');
			$errorStr .= $comma.'第'.$error2Row.'行金额更新失败';
		}

		if ($errorStr) return $errorStr;

		return 1;
	}
}
