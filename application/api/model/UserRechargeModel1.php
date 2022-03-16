<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;
use app\api\validate\UserTotal as UserTotalValidate;

class UserRechargeModel extends Model
{
	protected $table = 'ly_user_recharge';

	/**
	 * [inpourpay 用户使用该接口提交充值记录订单]
	 * @return [type] [description]
	 */
	public function newRechargeOrder(){
		//获取参数
		$token 		= input('post.token/s');

		$userArr	= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid		= $userArr[0];//uid
		$username 	= $userArr[1];//username

		if (cache('submitRechargeTime'.$uid) && time() - cache('submitRechargeTime'.$uid) < 5) return ['code'=>0,'code_dec'=>'提交过于频繁'];
		cache('submitRechargeTime'.$uid, time());

		$param = input('param.');

		//数据验证
		$validate = validate('app\api\validate\Recharge');
		if (!$validate->scene('userRechargeSub')->check($param)) return ['code'=>0,'code_dec'=>$validate->getError()];
		// 获取渠道信息
		$rechargeTypeInfo = model('RechangeType')->where('id', $param['recharge_id'])->find();
		if ($param['money'] < $rechargeTypeInfo['minPrice'] || $param['money'] > $rechargeTypeInfo['maxPrice']) return ['code'=>0,'code_dec'=>'金额过高或过低'];

		$orderNumber = trading_number();		
		$insertArray = [
			'uid'          => $uid,
			'order_number' => $orderNumber,
			'type'         => $param['recharge_id'],
			'money'        => $param['money'],
			'add_time'     => time()
		];

		$res = $this->allowField(true)->save($insertArray);
		if (!$res) return ['code'=>0,'code_dec'=>'提交失败'];
		// 获取收款账号信息
		$recaivablesInfo = model('Recaivables')->field('id,account,name,qrcode,bank')->where('type', $param['recharge_id'])->select()->toArray();
		if (!$recaivablesInfo) return ['code'=>0,'code_dec'=>'暂无收款账户'];

		foreach ($recaivablesInfo as $key => &$value) {
			$value['typeName'] = ($value['qrcode']) ? $rechargeTypeInfo['name'] : $value['bank'];
		}

		$data['code']        = 1;
		$data['code_dec']    = '充值申请提交成功';
		$data['orderNumber'] = $orderNumber;
		$data['money']       = $param['money'];
		$data['date']        = date('Y-m-d');
		$data['receive']     = $recaivablesInfo;
		return $data;
	}

	public function getRechargeInfo(){
		//获取参数
		$param 		= input('param.');

		$userArr	= explode(',',auth_code($param['token'],'DECODE'));//uid,username
		$uid		= $userArr[0];//uid
		$username 	= $userArr[1];//username

		if (!isset($param['orderNumber']) || !$param['orderNumber']) return ['code'=>0,'code_dec'=>'暂无数据'];

		$orderInfo = $this->where([['uid','=',$uid],['order_number','=',$param['orderNumber']]])->find();
		if (!$orderInfo->toArray()) return ['code'=>0,'code_dec'=>'暂无数据'];
		// 充值渠道名称
		$typeInfo = model('RechangeType')->field('name')->where('id', $orderInfo['type'])->find();
		// 获取收款账号信息
		$recaivablesInfo = model('Recaivables')->field('ly_recaivables.id,ly_recaivables.account,ly_recaivables.name,ly_recaivables.qrcode,bank.bank_name')
							->join('bank','ly_recaivables.bid=bank.id','left')
							->where('ly_recaivables.type', $orderInfo['type'])->select()->toArray();
		if (!$recaivablesInfo) return ['code'=>0,'code_dec'=>'暂无收款账户'];

		foreach ($recaivablesInfo as $key => &$value) {
			$value['typeName'] = ($value['qrcode']) ? $typeInfo['name'] : $value['bank_name'];
			unset($value['bank_name']);
		}

		$data['code']        = 1;
		$data['code_dec']    = '充值申请提交成功';
		$data['orderNumber'] = $orderInfo['order_number'];
		$data['money']       = $orderInfo['money'];
		$data['date']        = date('Y-m-d');
		$data['receive']     = $recaivablesInfo;
		return $data;
	}

	/**
	 * 充值记录
	 */
	public function getUserRechargeList(){

		//获取参数
		$token 		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid		= $userArr[0];//uid
		$username 	= $userArr[1];//username

		$param 		= input('post.');

		if(!$uid){
			$data['code'] = 0;
			return $data;
		}

		//用户名
		if (isset($param['username']) and $param['username']) {
			$userId = model('Users')->where('username',$param['username'])->value('id');
			$where[] = array('uid','=',$userId);
		} else {
			$where[] = array('uid','=',$uid);
		}

		//状态
		if (isset($param['state']) and $param['state']) {
			$where[] = array('state','=',$param['state']);
		}
		/*//开始时间
		if (isset($param['search_time_s']) and $param['search_time_s']) {
			$where[] = array('ly_user_recharge.add_time','>=',strtotime($param['search_time_s']));
		} else {
			$where[] = array('ly_user_recharge.add_time','>=',mktime(0,0,0,date('m'),date('d'),date('Y')));
		}
		//结束时间
		if (isset($param['search_time_e']) and $param['search_time_e']) {
			$where[] = array('ly_user_recharge.add_time','<=',strtotime($param['search_time_e']));
		} else {
			$where[] = array('ly_user_recharge.add_time','<=',mktime(23,59,59,date('m'),date('d'),date('Y')));
		}*/

		//分页
		//总记录数
		$count = $this->where($where)->count();
		if(!$count){
			$data['code'] = 0;
			$data['code_dec']	= '暂无记录';
			return $data;
		}

		//每页记录数
		$pageSize = (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		//当前页
		$pageNo = (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		//总页数
		$pageTotal = ceil($count / $pageSize); //当前页数大于最后页数，取最后
		//偏移量
		$limitOffset = ($pageNo - 1) * $pageSize;

/*		$dataAll = $this->field('ly_user_recharge.*,rechange_type.name,bank.bank_name')
						->join('rechange_type','ly_user_recharge.type = rechange_type.id')
						->join('bank','ly_user_recharge.bid = bank.id')
						->where($where)
						->order(['ly_user_recharge.add_time'=>'desc','id'=>'desc'])
						->limit($limitOffset, $pageSize)
						->select();*/
		$dataAll = $this->field('*')->where($where)->order(['add_time'=>'desc','id'=>'desc'])->limit($limitOffset, $pageSize)->select();

		if (!$dataAll) {
			$data['code'] = 0;
			$data['code_dec']	= '暂无记录';
			return $data;
		}

		//获取成功
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;

		//数组重组赋值
		foreach ($dataAll as $key => $value) {
			$bank_name = array();
			$pay_type = array();
			$data['info'][$key]['dan'] 			= $value['order_number'];
			$data['info'][$key]['adddate'] 		= date('Y-m-d H:i:s',$value['add_time']);

            $bank_name  = model('Bank')->field('bank_name')->where('id', $value['bid'])->find();
			$data['info'][$key]['bank_name'] 	= isset($bank_name['bank_name']) ? $bank_name['bank_name'] : '';
			         //$value['bank_name'];
			$pay_type = model('RechangeType')->field('name')->where('id', $value['type'])->find();
			$data['info'][$key]['pay_type'] 	= isset($pay_type['name']) ? $pay_type['name'] : '';
			   //$value['name'];
			$data['info'][$key]['money'] 		= $value['money'];
			$data['info'][$key]['status'] 		= $value['state'];
			$data['info'][$key]['status_desc'] 	= config('custom.rechargeStatus')[$value['state']];
			$data['info'][$key]['typedes'] 		= '充值';
		}

		return $data;
	}

	/**
	 * 团队充值
	 */
	public function TeamRecharge(){
		//获取参数并过滤
		$param 				= input('param.');
		$param['user_id'] 	= input('param.user_id/d');

		if(!$param['user_id']){
			$data['code'] = 0;
			return $data;
		}

		$where[] = array('user_team.uid','=',$param['user_id']);
		$where[] = array('user_team.team','neq',$param['user_id']);

		//用户名搜索
		if (isset($param['username']) and $param['username']) {
			$where[] = array('username','=',$param['username']);
		}
		//状态
		if (isset($param['state']) and $param['state']) {
			$where[] = array('ly_user_recharge.state','=',$param['state']);
		}
		//开始时间
		if (isset($param['search_time_s']) and $param['search_time_s']) {
			$where[] = array('add_time','>=',strtotime($param['search_time_s']));
		} else {
			$where[] = array('add_time','>=',mktime(0,0,0,date('m'),date('d'),date('Y')));
		}
		//结束时间
		if (isset($param['search_time_e']) and $param['search_time_e']) {
			$where[] = array('add_time','<=',strtotime($param['search_time_e']));
		} else {
			$where[] = array('add_time','<=',mktime(23,59,59,date('m'),date('d'),date('Y')));
		}

		//分页
		//总记录数
		$count = $this->join('users','ly_user_recharge.uid = users.id')->join('user_team','ly_user_recharge.uid = user_team.team')->where($where)->count();
		//每页记录数
		$pageSize = (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		//当前页
		$pageNo = (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		//总页数
		$pageTotal = ceil($count / $pageSize); //当前页数大于最后页数，取最后
		//偏移量
		$limitOffset = ($pageNo - 1) * $pageSize;

		//数据
		$dataArray = $this->field('ly_user_recharge.*,users.username')
							->join('users','ly_user_recharge.uid = users.id')
							->join('user_team','ly_user_recharge.uid = user_team.team')
							->where($where)
							->order('add_time','desc')
							->limit($limitOffset, $pageSize)
							->select();

		if (!$dataArray) {
			$data['code'] = 0;
			return $data;
		}

		//获取成功
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;

		$decimalPlace = config('api.decimalPlace');
		foreach($dataArray as $key => $value){
			$value['fee'] = round($value['fee'],$decimalPlace);
			$value['money'] = round($value['money'],$decimalPlace);

			$bankName = model('Bank')->field('bank_name')->where('id',$value['bid'])->find();
			$payType = model('RechangeType')->field('name')->where('id',$value['type'])->find();
			$data['info'][$key]['dan']                   =   $value['order_number'];
			$data['info'][$key]['adddate']               =   date('Y-m-d H:i:s',$value['add_time']);
			$data['info'][$key]['bank_name']             =   $bankName ? $bankName['bank_name'] : '';
			$data['info'][$key]['pay_type']              =   $payType['name'];
			$data['info'][$key]['username']              =   $value['username'];
			$data['info'][$key]['fee']              	 =   $value['fee'];
			$data['info'][$key]['money']                 =   $value['money'];
			$data['info'][$key]['status']                =   config('custom.rechargeStatus')[$value['state']];
		}

		return $data;
	}

	/**
	 * 团队账号首冲人数
	 */
	public function rechargeFirst($userId, $startData, $endDate){
		$data = $this->field('ly_user_recharge.uid,ly_user_recharge.add_time')->join('user_team','ly_user_recharge.uid = user_team.team','inner')->where(['user_team.uid'=>$userId,'ly_user_recharge.state'=>1])->order('add_time','asc')->group('ly_user_recharge.uid')->select();
		if (!$data) return 0;

		foreach ($data as $key => $value) {
			if ($value['add_time'] <= $startData || $value['add_time'] >= $endDate) {
				unset($data[$key]);
			}
		}

		return count($data);
	}

	//获取用户首冲信息
	public function getfirstRecharge(){

		//获取参数
		$token 		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];//uid
		$username 	= $userArr[1];//username

		$count = $this->where(array('uid'=>$uid,'first_recharge'=>2))->count();
		if($count){
			$data['code'] 					= 1;
			$data['info']['state']			=	0;
			return $data;
		}

		$data_arr = $this->field('money,first_recharge,add_time')->where(array('uid'=>$uid,'state'=>1))->find();

		if(!$data_arr){
			$data['code'] 					= 1;
			$data['info']['state']			=	0;
			return $data;
		}

		if ($data_arr['first_recharge'] == 2) {
			$data['code'] 					= 1;
			$data['info']['state']			=	0;
			return $data;
		}

		//判断首冲是否过了24小时
		if(time()-$data_arr['add_time']>24*60*60){
			$data['code'] 					= 1;
			$data['info']['state']			=	0;
			return $data;
		}

		$date = mktime(0,0,0,date('m'),date('d'),date('Y'));

		$where[] = array('uid','=',$uid);
		$where[] = array('date','=',$date);
		$where[] = array('betting','=',0);

		$bettingcount = Model('UserDaily')->wehre($where)->count();

		if($bettingcount){
			$data['code'] 					= 1;
			$data['info']['state']			=	0;
			return $data;
		}

		$state = 1;

		if (!$data_arr['money']) {
			$state = 0;
		}

		if ($data_arr['money'] >= 100 && $data_arr['money'] < 500) {
			$back = 18.88;
		}elseif ($data_arr['money'] >= 500 && $data_arr['money'] < 1000) {
			$back = 38.88;
		}elseif ($data_arr['money'] >= 1000 && $data_arr['money'] < 5000) {
			$back = 58.88;
		}elseif ($data_arr['money'] >= 5000 && $data_arr['money'] < 10000) {
			$back = 88.88;
		}elseif($data_arr['money'] >= 10000){
			$back = 188.88;
		}

		if (!$back) {
			$data['code'] = 0;
			return $data;
		}

		if($state){
			$data['code'] = 1;
			$data['info']['firstRecharge']	=	$data_arr['money'];
			$data['info']['money']			=	$back;
			$data['info']['state']			=	1;
		}else{
			$data['code'] = 1;
			$data['info']['state']			=	0;
		}
		return $data;
	}

	//首冲返现
	public function firstRecharge(){
				//获取参数
		$token 		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];//uid
		$username 	= $userArr[1];//username

		$count = $this->where(array('uid'=>$uid,'first_recharge'=>2))->count();
		if($count){
			$data['code'] 		= 2;
			$data['code_dec'] 	= '已领取奖励';
			return $data;
		}

		$data_arr = $this->field('money,first_recharge,add_time')->where(array('uid'=>$uid,'state'=>1))->find();

		if(!$data_arr){
			$data['code'] 		= 0;
			$data['code_dec'] 	= '领取失败';
			return $data;
		}

		if ($data_arr['first_recharge'] == 2) {
			$data['code'] 		= 2;
			$data['code_dec'] 	= '已领取奖励';
			return $data;
		}

		//判断首冲是否过了24小时
		if(time()-$data_arr['add_time']>24*60*60){
			$data['code'] 		= 0;
			$data['code_dec'] 	= '领取失败';
			return $data;
		}

		$date = mktime(0,0,0,date('m'),date('d'),date('Y'));

		$where[] = array('uid','=',$uid);
		$where[] = array('date','=',$date);
		$where[] = array('betting','=',0);

		$bettingcount = Model('UserDaily')->wehre($where)->count();

		if($bettingcount){
			$data['code'] 		= 0;
			$data['code_dec'] 	= '领取失败';
			return $data;
		}


		if (!$data_arr['money']) {
			$data['code'] 		= 0;
			$data['code_dec'] 	= '领取失败';
		}

		if ($data_arr['money'] >= 100 && $data_arr['money'] < 500) {
			$back = 18.88;
		}elseif ($data_arr['money'] >= 500 && $data_arr['money'] < 1000) {
			$back = 38.88;
		}elseif ($data_arr['money'] >= 1000 && $data_arr['money'] < 5000) {
			$back = 58.88;
		}elseif ($data_arr['money'] >= 5000 && $data_arr['money'] < 10000) {
			$back = 88.88;
		}elseif($data_arr['money'] >= 10000){
			$back = 188.88;
		}

		if (!$back) {
			$data['code'] 		= 0;
			$data['code_dec'] 	= '领取失败';
			return $data;
		}
		$price = sprintf("%.3f",$back);

		//获取余额
		$balance = model('UserTotal')->where('uid' , $uid)->value('balance');

		$is_updata_user = model('UserTotal')->where('uid' , $uid)->setDec('balance', $price);

		if (!$is_updata_user) {
			$data['code'] 		= 0;
			$data['code_dec'] 	= '领取失败';
			return $data;
		}

		$financial_data['uid'] 						= $uid;
		$financial_data['username'] 				= $username;
		$financial_data['order_number'] 			= 'A'.trading_number();
		$financial_data['trade_type'] 				= 9;
		$financial_data['trade_before_balance']		= $balance;
		$financial_data['trade_amount'] 			= $price;
		$financial_data['account_balance'] 			= $balance + $price;
		$financial_data['remarks'] 					= '每日首冲活动奖励';

		model('TradeDetails')->tradeDetails($financial_data);

		$this->where(array('uid'=>$uid))->update(array('first_recharge'=>2));

		$data['code'] 		= 1;
		$data['code_dec']	= '每日首冲活动奖励';
		return $data;
	}



}
