<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 用户列表的相关操作
 */

namespace app\manage\model;

use think\Model;
use app\api\model\ApiModel;

class MerchantModel extends Model{
	//表名
	protected $table = 'ly_merchant';
	/**
	 * 用户列表
	 */
	public function userList(){
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		// 用户名搜索
		if(isset($param['username']) && $param['username']){
			//$where[] = array('username','like','%'.trim($param['username']).'%');
			$where[] = array('username','=',$param['username']);
			$pageParam['username'] = $param['username'];
		}
		// 商户号搜索
		if(isset($param['merchantid']) && $param['merchantid']){
			//$where[] = array('merchantid','like','%'.trim($param['merchantid']).'%');
			$where[] = array('merchantid','=',$param['merchantid']);
			$pageParam['merchantid'] = $param['merchantid'];
		}
		//用户状态搜索
		if(isset($param['status']) && $param['status']){
			$where[] = array('status','=',$param['status']);
			$pageParam['status'] = $param['status'];
		}
		//用户注册时间搜索
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('reg_time','>=',strtotime($dateTime[0]));
			$where[] = array('reg_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}
		//查询符合条件的数据
		$resultData = $this->field('ly_merchant.*,merchant_total.balance,frozen_balance')->join('merchant_total','ly_merchant.id = merchant_total.uid','left')->where($where)->order(['ly_merchant.status'=>'desc','ly_merchant.id'=>'desc'])->paginate(15,false,['query'=>$pageParam]);
		//数据集转数组
		$userlist = $resultData->toArray()['data'];
		//部分元素重新赋值
		$userState = config('custom.merchantVerify');//账号状态
		foreach ($userlist as $key => &$value) {
			$value['statusStr'] = $userState[$value['status']];
		}
		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',255],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'where'		=>	$pageParam,
			'userList'	=>	$userlist,//数据
			'page'		=>	$resultData->render(),//分页
			'userState'	=>	$userState,//账号状态
			'power'		=>	$power,//权限
		);
	}
	/**
	 * 用户添加
	 */
	public function add(){
		$param = input('post.');

		//数据验证
		$validate = validate('app\manage\validate\Merchant');
		if(!$validate->scene('add')->check($param)){
			return $validate->getError();
		}

		//密码加密并添加注册时间
		$param['password']   = auth_code($param['password'],'ENCODE');
		$param['merchantid'] = md5(trading_number());
		$param['reg_time']   = time();
		//添加用户数据
		$insertUsers = $this->allowField(true)->save($param);
		if(!$insertUsers) return '添加失败';
		$insertId = $this->id;
		
		//将该账户添加至user_total表
		$insertTotal = model('MerchantTotal');
		$insertTotalId = $insertTotal->insertGetId(array('uid'=>$insertId));
		if(!$insertTotalId){
			$this->destroy($this->id);
			return '添加失败';
		}

		// 注册至团队表
		$insertTeam = model('MerchantTeam')->reg($insertId);
		if (!$insertTeam) {
			$this->destroy($this->id);
			$insertTotal->where('uid', $insertId)->delete();
			return '添加失败';
		}

		//添加操作日志
		model('Actionlog')->actionLog(session('manage_username'),'添加用户名为'.$param['username'].'的商户',1);

		return 1;
	}

	/**
	 * 用户编辑提交
	 */
	public function edit(){
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		//参数过滤
		$array = $param;
		//提取用户ID
		$uid = $array['id'];
		unset($array['id']);
		//获取用户信息（用于操作日志）
		$userInfo = $this->field('username,password,status')->where('id','=',$uid)->find();

		//获取系统最高、最低返点、上庄返点
		$setting = model('Setting')->getFieldsById('m_alipay_fee_min,m_alipay_fee_max,m_wechat_fee_min,m_wechat_fee_max,m_bank_fee_min,m_bank_fee_max');
		if(isset($array['alipay_fee']) && $array['alipay_fee']){
			if($array['alipay_fee']<$setting['m_alipay_fee_min'] || $array['alipay_fee']>$setting['m_alipay_fee_max']){
				return '请输入合理的支付宝费率（'.$setting['m_alipay_fee_min'].' - '.$setting['m_alipay_fee_max'].'）';
			}
		}
		if(isset($array['wechat_fee']) && $array['wechat_fee']){
			if($array['wechat_fee']<$setting['m_wechat_fee_min'] || $array['wechat_fee']>$setting['m_wechat_fee_max']){
				return '请输入合理的微信费率（'.$setting['m_wechat_fee_min'].' - '.$setting['m_wechat_fee_max'].'）';
			}
		}
		if(isset($array['bank_fee']) && $array['bank_fee']){
			if($array['bank_fee']<$setting['m_bank_fee_min'] || $array['bank_fee']>$setting['m_bank_fee_max']){
				return '请输入合理的银行费率（'.$setting['m_bank_fee_min'].' - '.$setting['m_bank_fee_max'].'）';
			}
		}
		// 账户密码加密
		if(isset($array['password']) && $array['password']) $array['password'] = auth_code($array['password'],'ENCODE');
		// 交易密码交易
		if(isset($array['pay_pwd']) && $array['pay_pwd']) $array['pay_pwd'] = auth_code($array['pay_pwd'],'ENCODE');
		//数据更新
		$res2 = $this->where('id', $uid)->update($array);
		if (!$res2) return '无须修改';
		
		//日志内容
		$logContent = '编辑用户名为'.$userInfo['username'].'的用户，';
		foreach ($array as $key => $value) {
			if (!isset($userInfo[$key]) || $userInfo[$key] == $value) continue;
			switch ($key) {
				case 'status':
					$logContent .= '用户状态由'.config('custom.merchantVerify')[$userInfo['status']].'调整为'.config('custom.merchantVerify')[$array['status']].'，';
					break;
				case 'password':
					$logContent .= '登录密码由'.auth_code($userInfo['password'],'DECODE').'修改为'.auth_code($array['password'],'DECODE').'，';
					break;
				case 'alipay_fee':
					$logContent .= '会员支付宝费率由'.$userInfo['alipay_fee'].'修改为'.$array['alipay_fee'].'，';
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
		$data['userTotal'] = model('MerchantTotal')->field('id',true)->where('uid','=',$uid)->find();
		//用户银行
		$data['bankInfo'] = model('MerchantBank')->where('mid',$uid)->order('id','desc')->limit(1)->find();

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',255],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);
		return array(
			'userInfo'			=>	$data,
			'power'				=>	$power,
			'userState'			=>	config('custom.userState'),//账号状态
		);
	}
	/**
	 * 风险账号
	 */
	public function risk(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		//更新
		$updateRes = $this->where('id',$param['uid'])->setField('danger',$param['value']);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '非' : '';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$param['username'].'设为'.$actionStr.'风险账号',1);

		return 1;
	}
	/**
	 * 工资
	 */
	public function wages(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		//更新
		$updateRes = $this->where('id',$param['uid'])->setField('iswage',$param['value']);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '关闭' : '开启';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$param['username'].'的工资设为'.$actionStr,1);

		return 1;
	}
	/**
	 * 亏损工资
	 */
	public function loss(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		//更新
		$updateRes = $this->where('id',$param['uid'])->setField('losswage',$param['value']);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '关闭' : '开启';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$param['username'].'的亏损工资设为'.$actionStr,1);

		return 1;
	}
	/**
	 * 分红
	 */
	public function bonus(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		//更新
		$updateRes = $this->where('id',$param['uid'])->setField('isbonus',$param['value']);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '关闭' : '开启';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$param['username'].'的分红设为'.$actionStr,1);

		return 1;
	}
	/**
	 * 锁定
	 */
	public function locking(){
		$param = input('post.');//获取参数
		if(!$param) return '提交失败';
		//更新
		$updateRes = $this->where('id',$param['uid'])->setField('islock',$param['value']);
		if(!$updateRes) return '修改失败';
		//添加操作日志
		$actionStr = $param['value']==2 ? '未锁' : '锁定';
		model('Actionlog')->actionLog(session('manage_username'),'将账号'.$param['username'].'的锁定状态设为'.$actionStr,1);

		return 1;
	}

	/**
	 * 修改单个字段
	 */
	public function setFieldValue(){
		$param = input('post.');//获取参数
		if (!$param || !isset($param['uid']) || !isset($param['field']) || !isset($param['value'])) return '提交失败';

		//更新
		$res = $this->where('id', '=', $param['uid'])->setField($param['field'], $param['value']);
		if (!$res) return '操作失败';
		
		switch ($param['field']) {
			case 'is_lock':
				$logStr = ($param['value'] == 1) ? '正常' : '关闭' ;
				$userName = (isset($param['username']) && $param['username']) ? $param['username'] : $this->where('id', '=', $param['uid'])->value('username');
				$logContent = $userName.'的状态设为'.$logStr;
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

		if (strtolower($param['table']) == 'merchant') {
			model('MerchantTeam')->where('team', $param['id'])->delete();
			model('MerchantTotal')->where('uid', $param['id'])->delete();
		}		

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
		$validate = validate('app\manage\validate\Users');
		if(!$validate->scene('teamMove')->check([
			'bUsername'				=>	(isset($array['bqusername'])) ? $array['bqusername'] : '',
			'artificialUsername'	=>	(isset($array['qusername'])) ? $array['qusername'] : '',
			'artificialSafeCode'	=>	(isset($array['safe_code'])) ? $array['safe_code'] : '',
		])){
			return $validate->getError();
		}
		//获取被转移用户信息
		$name1 = $this->where('username',$array['bqusername'])->field('id,sid,vip_level,rebate,banker_rebate')->find();
		//获取即将转移到的用户信息
		$name2 = $this->where('username',$array['qusername'])->field('id,sid,vip_level,rebate,banker_rebate')->find();
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
			//新上级团队中添加该团队
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
		$vipDiff = $name1['vip_level'] - $name2['vip_level'];
		if($vipDiff > 1){
			foreach ($Qteam as $key => $value) {
				$updateVip[] = $this->where('id',$value)->setDec('vip_level',$vipDiff-1);
			}
		}elseif($vipDiff <= 0){
			$diffDown = $name2['vip_level'] - $name1['vip_level'] + 1;
			foreach ($Qteam as $key => $value) {
				$updateVip[] = $updateVip[] = $this->where('id',$value)->setInc('vip_level',$diffDown);
			}
		}
		//返点计算
		if($name1['rebate']>$name2['rebate']){
			$rebateDiff = $name1['rebate'] - $name2['rebate'];
			foreach ($Qteam as $key => $value) {
				$userRebate = $this->where('id',$value)->field('rebate')->find();
				if($userRebate['rebate'] - $rebateDiff > 0){
					$updateRebate = $userRebate['rebate'] - $rebateDiff;
				}else{
					$updateRebate = 0;
				}
				$this->where('id',$value)->update(array('rebate'=>$updateRebate));
			}
		}
		//上庄返点计算
		if($name1['banker_rebate']>$name2['banker_rebate']){
			$rebateDiff = $name1['banker_rebate'] - $name2['banker_rebate'];
			foreach ($Qteam as $key => $value) {
				$userRebate = $this->where('id',$value)->field('banker_rebate')->find();
				if($userRebate['banker_rebate'] - $rebateDiff > 0){
					$updateRebate = $userRebate['banker_rebate'] - $rebateDiff;
				}else{
					$updateRebate = 0;
				}
				$this->where('id',$value)->update(array('banker_rebate'=>$updateRebate));
			}
		}
		//添加迁移日志
		model('TeammoveLog')->insertGetId(array('aid'=>session('manage_userid'),'addtime'=>time(),'log'=>'迁移'.$param['bqusername'].'至'.$param['qusername']));

		return 1;
	}

	/**
	 * 会员关系树视图
	 */
	public function relationView(){
		$param = input('get.');//获取参数
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		//用户名搜索
		if(isset($param['username']) && $param['username']){
			$userInfo = $this->field('id')->where('username',trim($param['username']))->find();
			$where[] = array('id','=',$userInfo['id']);
			$pageParam['username'] = $param['username'];
			if(!$userInfo){
				return array(
					'where'			=>	$pageParam,
					'userRelation'	=>	'',//数据
					'page'			=>	'',//分页
				);
			}
		}else{
			$where[] = array('sid','=',0);
		}
		//查询符合条件的数据
		$resultData = $this->field('id,username,sid')->where($where)->order('id','asc')->paginate(16,false,['query'=>$pageParam]);
		//数据集转数组
		$userRelation = $resultData->toArray()['data'];
		//部分元素重新赋值
		foreach ($userRelation as $key => &$value) {
			$value['isDown'] = $this->where('sid',$value['id'])->count();
		}

		return array(
			'where'			=>	$pageParam,
			'userRelation'	=>	$userRelation,//数据
			'page'			=>	$resultData->render(),//分页
		);
	}
	/**
	 * 会员关系树
	 */
	public function relation(){
		$param = input('post.');//获取参数
		if(!$param) return '非法提交';
		//参数过滤
		$array = array_filter($param);
		if(!$array) return '请确认各项填写无误后再提交';
		//获取数据集
		$resultData = $this->where('sid',$array['uid'])->field('id,username,sid')->select();
		if(!$resultData) return '该用户暂无下级';

		foreach ($resultData as $key => &$value) {
			$value['isDown'] = $this->where('sid',$value['id'])->count();
		}
		return $resultData;
	}

	/**
	 * 团队报表
	 */
	public function teamStatistic(){
		$param = input('get.');
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageUrl = '?page={page}';
		$param['isUser'] = (isset($param['isUser'])) ? $param['isUser'] : 2;
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
		//查询符合条件的数据
		$userList = $this->field('id,sid,username')->where($where)->select()->toArray();
		//print_r($userList);
		//用户团队数据计算
		$data = model('MerchantDaily')->teamStatistic($userList,$startDate,$endDate,$sid);
		$total['totalAll'] = $data['totalAll'];
		unset($data['totalAll']);
		//var_dump($data);
		//分页
		$pageNum  = isset($param['page']) && $param['page'] ? $param['page'] : 1 ;
		$pageInfo = model('ArrPage')->page($data, 15, $pageNum, $pageUrl);
		//var_dump($pageInfo);
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
		//$userId = $this->where('username', $param['username'])->value('id');
		$userInfo = $this->field('id,user_type')->where('username','=',$param['username'])->find();
		if (!$userInfo) return '用户不存在，请核对后再操作';
		$artificialTime = cache('CA_artificialTime'.session('manage_userid')) ? cache('CA_artificialTime'.session('manage_userid')) : time()-2;
		if(time() - $artificialTime < 2){
			return ' 2 秒内不能重复提交';
		}
		cache('CA_artificialTime'.session('manage_userid'), time(), 10);
		//统计类型
		$userTotalType = config('custom.userTotal')[$param['type']];		
		if ($param['type'] == 2 && $param['price'] > 0) $param['price'] = '-'.$param['price'];

		//获取用户余额
		$userBalance = model('MerchantTotal')->where('uid', $userInfo['id'])->value('balance');

		//更新用户余额、统计金额
		$res = model('MerchantTotal')->where('uid', $userInfo['id'])->inc('balance', $param['price'])->inc($userTotalType, $param['price'])->update();
		if (!$res) return '操作失败';
		if(isset($param['remarks'])){
			$remarks = $param['remarks'];
		} else{
			$remarks = '';
		}
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
					model('MerchantRecharge')->insertGetId($rechargeArray);
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

		$userTransactionType = config('custom.transactionType')[$param['type']];//操作类型	

		$data = session('artificialBatchData');
		// session('artificialBatchData', null);
		
		$keyArray = $data[1];
		unset($data[1]);		

		foreach ($keyArray as $key => $value) {
			switch ($value) {
				case '用户名':
				case '用户':
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
			$userBalance = model('MerchantTotal')->where('uid', $userId)->value('balance');

			if ($param['type'] == 2 && $value[$priceKey] > 0) $value[$priceKey] = '-'.$value[$priceKey];
			//更新用户余额、统计金额
			$res = model('MerchantTotal')->where('uid', $userId)->inc('balance', $value[$priceKey])->inc($userTotalType, $value[$priceKey])->update();
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
					model('MerchantRecharge')->insertGetId($rechargeArray);
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

	/**
	 * 资质认证
	 */
	public function verifyMer(){
		if (!request()->isAjax()) return '非法提交';
		$param = input('post.');
		if (!$param) return '提交失败';

		// 验证安全码
		$safeCode = model('Manage')->where('id', session('manage_userid'))->value('safe_code');
		if (auth_code($safeCode, 'DECODE') != $param['safe_code']) return '安全验证未通过';
		if (isset($param['verify']) && strlen($param['verify']) > 255) return '说明过长';

		$update['status'] = $param['status'];
		if (isset($param['verify']) && $param['verify']) $update['verify'] = $param['verify'];
		
		$res = $this->where('id', $param['id'])->update($update);
		if (!$res) return '操作失败';

		return 1;
	}
}