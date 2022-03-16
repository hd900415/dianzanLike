<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 作用：生成操作日志
 */

namespace app\admin\model;

use think\Model;

class MerchantModel extends Model{
	//表名
	protected $table = 'ly_merchant';

	/**
     * 用户登录验证
     */
    public function checkLogin(){
		$verifyCode = input('post.code/d');
		if (session('code') != $verifyCode) return 'code';

		$where['username'] = input('post.user/s');
		
		//判断用户是否存在
		$userInfo = $this->where($where)->where('islock', 1)->field('id,username,password,types')->find();	// 商户
		$userInfo2 = model('Users')->where($where)->where(['state'=>1, 'is_admin'=>1])->field('id,username,password,user_type')->find();	// 码商
		if(!$userInfo && !$userInfo2) return 'nouser';
		
		//检查密码
		$password = input('post.pass/s');
		if($userInfo && auth_code($userInfo['password'], 'DECODE') != $password) return 'nouserpwd';
		if($userInfo2 && auth_code($userInfo2['password'], 'DECODE') != $password) return 'nouserpwd';

		// 记录IP
		if ($userInfo) $this->where('id', $userInfo['id'])->setField('last_ip', request()->ip());
		if ($userInfo2) model('Users')->where('id', $userInfo2['id'])->setField('last_ip', request()->ip());
		/**
		 * 保存session
		 */
		session('is_admin_login', 1);
		// 商户类型
		session('admin_types', ($userInfo) ? $userInfo['types'] : 'user');
		// 商户名
		session('admin_username', ($userInfo) ? $userInfo['username'] : $userInfo2['username']);
		// 商户ID
		session('admin_userid', ($userInfo) ? $userInfo['id'] : $userInfo2['id']);
		// 存入缓存
		cache('adminOnline'.session('admin_types').session('admin_userid'), session_id(), 86400);

		return 'succ';
	}

	/**
	 * 绑定信息
	 */
	public function bindInfo($type){
		$param = input('post.');
		if (!$param) return '操作失败';

		//数据验证
		$validate = validate('app\admin\validate\Info');
		if ($type == 'phone') {
			if(!$validate->scene('bindPhone')->check($param)) return $validate->getError();
			$res = $this->where('id', session('admin_userid'))->setField('phone', $param['phone']);

			//添加操作日志
			model('Actionlog')->actionLog(session('admin_username'),'绑定手机，号码：'.$param['phone'],3);
		} else {
			if(!$validate->scene('bindMail')->check($param)) return $validate->getError();
			$res = $this->where('id', session('admin_userid'))->setField('mail', $param['mail']);

			//添加操作日志
			model('Actionlog')->actionLog(session('admin_username'),'绑定邮箱，账号：'.$param['mail'],3);
		}
		if (!$res) return '操作失败';

		return 1;
	}

	/**
	 * 生成APIKEY
	 */
	public function setApiKey(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');

		$phone = model('Merchant')->where('id', session('admin_userid'))->value('phone');
		$code = cache('C_Code_'.$phone);
		if (!$code || !isset($param['code']) || $param['code'] != $code) return '网络异常，请重新生成';

		$str = trading_number();
		$merchantkey = auth_code($str, 'ENCODE');
		$res = $this->where('id', session('admin_userid'))->setField('merchantkey', $merchantkey);

		//添加操作日志
		model('Actionlog')->actionLog(session('admin_username'),'生成APIKEY',3);

		if (!$res) return '生成失败，请重新尝试';
		return 1;
	}
	/**
	 * 修改密码
	 * @return [type] [description]
	 */
	public function editPwd(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');

		$validate = validate('app\admin\validate\Info');
		if(!$validate->scene('editPwd')->check($param)) return $validate->getError();

		$res = $this->where('id', session('admin_userid'))->setField('password', auth_code($param['new_password'], 'ENCODE'));
		if (!$res) return '生成失败，请重新尝试';

		//添加操作日志
		model('Actionlog')->actionLog(session('admin_username'),'修改登录密码',3);

		session('is_admin_login', null);
		session('admin_username', null);
		session('admin_userid', null);
		return 1;
	}

	/**
	 * 设置交易密码
	 * @return [type] [description]
	 */
	public function setPayPwd(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');

		$validate = validate('app\admin\validate\Info');
		if(!$validate->scene('setPayPwd')->check($param)) return $validate->getError();

		$res = $this->where('id', session('admin_userid'))->setField('pay_pwd', auth_code($param['pay_pwd'], 'ENCODE'));
		if (!$res) return '生成失败，请重新尝试';

		//添加操作日志
		model('Actionlog')->actionLog(session('admin_username'),'设置交易密码',3);

		return 1;
	}

	/**
	 * 资质认证
	 * @return [type] [description]
	 */
	public function verifySub(){
		if(!request()->isAjax()) return '非法提交';

		$res = $this->where('id', session('admin_userid'))->update(['status'=>2]);
		if (!$res) return '提交失败';

		return 1;
	}

	public function lookMe(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');
		if (!$param) return '提交失败';

		$validate = validate('app\admin\validate\Info');
		if(!$validate->scene('lookMe')->check($param)) return $validate->getError();

		if (isset($param['platform_name']) && $param['platform_name']) $upddateArr['platform_name'] = $param['platform_name'];
		if (isset($param['platform_type']) && $param['platform_type']) $upddateArr['platform_type'] = $param['platform_type'];
		if (isset($param['remarks']) && $param['remarks']) $upddateArr['remarks'] = $param['remarks'];
		if (isset($param['ip_white']) && $param['ip_white']) $upddateArr['ip_white'] = $param['ip_white'];

		$res = $this->where('id', session('admin_userid'))->update($upddateArr);
		if (!$res) return '保存失败，请重新尝试';

		return 1;
	}

	/**
	 * 团队费率调整
	 * @return [type] [description]
	 */
	public function feeEdit(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');
		if (!$param) return '提交失败';

		$validate = validate('app\admin\validate\Baseinfo');
		if(!$validate->scene('teamFeeEdit')->check($param)) return $validate->getError();

		// 获取当前商户和该商户的直属商户费率
		$merInfo = $this->field('id,alipay_fee,wechat_fee,bank_fee')->where('id', session('admin_userid'))->whereOr('sid', session('admin_userid'))->select()->toArray();
		// 提取数据
		foreach ($merInfo as $key => $value) {
			if ($value['id'] == session('admin_userid')) {
				$selfData = $value;
			} else {
				$alipayFee[] = $value['alipay_fee'];
				$wechatFee[] = $value['wechat_fee'];
				$bankFee[]   = $value['bank_fee'];
			}
		}
		sort($alipayFee);
		sort($wechatFee);
		sort($bankFee);
		// 下调限制
		if ((isset($param['alipay_fee']) && $param['alipay_fee'] < 0) && abs($param['alipay_fee']) > $alipayFee[0]) {
			return '支付宝下调费率可操作范围为：0.1-'.($alipayFee[0] - $selfData['alipay_fee']);
		}
		if ((isset($param['wechat_fee']) && $param['wechat_fee'] < 0) && abs($param['wechat_fee']) > $wechatFee[0]) {
			return '微信下调费率可操作范围为：0.1-'.($wechatFee[0] - $selfData['wechat_fee']);
		}
		if ((isset($param['bank_fee']) && $param['bank_fee'] < 0) && abs($param['bank_fee']) > $bankFee[0]) {
			return '银行下调费率可操作范围为：0.1-'.($bankFee[0] - $selfData['bank_fee']);
		}

		$log = '';
		// 获取平台设置
		$setting = model('Setting')->field('m_alipay_fee_min,m_alipay_fee_max,m_wechat_fee_min,m_wechat_fee_max,m_bank_fee_min,m_bank_fee_max')->findOrEmpty();
		// 获取团队商户信息
		$teamInfo = $this->field('team,alipay_fee,wechat_fee,bank_fee')->join('merchant_team', 'ly_merchant.id = merchant_team.team')->where(array(['merchant_team.uid', '=', session('admin_userid')], ['merchant_team.team', '<>', session('admin_userid')]))->select()->toArray();
		foreach ($teamInfo as $key => $value) {
			$updateArr = array();
			if (isset($param['alipay_fee']) && $param['alipay_fee']) {
				$updateArr['alipay_fee'] = $value['alipay_fee'] + $param['alipay_fee'];
				if ($updateArr['alipay_fee'] < $selfData['alipay_fee']) $updateArr['alipay_fee'] = $selfData['alipay_fee'];
				if ($updateArr['alipay_fee'] > $setting['m_alipay_fee_max']) $updateArr['alipay_fee'] = $setting['m_alipay_fee_max'];
				$log .= '支付宝'.$param['alipay_fee'];
			}
			if (isset($param['wechat_fee']) && $param['wechat_fee']) {
				$updateArr['wechat_fee'] = $value['wechat_fee'] + $param['wechat_fee'];
				if ($updateArr['wechat_fee'] < $selfData['wechat_fee']) $updateArr['wechat_fee'] = $selfData['wechat_fee'];
				if ($updateArr['wechat_fee'] > $setting['m_wechat_fee_max']) $updateArr['wechat_fee'] = $setting['m_wechat_fee_max'];
				$log .= '微信'.$param['wechat_fee'];
			}
			if (isset($param['bank_fee']) && $param['bank_fee']) {
				$updateArr['bank_fee'] = $value['bank_fee'] + $param['bank_fee'];
				if ($updateArr['bank_fee'] < $selfData['bank_fee']) $updateArr['bank_fee'] = $selfData['bank_fee'];
				if ($updateArr['bank_fee'] > $setting['m_bank_fee_max']) $updateArr['bank_fee'] = $setting['m_bank_fee_max'];
				$log .= '银行'.$param['bank_fee'];
			}
			if (!$updateArr) continue;
			// 修改团队成员费率
			$this->where('id', $value['team'])->update($updateArr);
		}

		//添加操作日志
		model('Actionlog')->actionLog(session('admin_username'),'调整团队费率，'.$log,3);

		return 1;
	}

	public function agentCentre(){
		$param = input('get.');
		// 查询条件组装
		$where[] = ['sid', '=', session('admin_userid')];
		// 分页参数组装
		$pageParam = array();
		// 商户号
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','=',$param['username']);
			$pageParam['username'] = $param['username'];
		}
		// 商户类型
		if(isset($param['types']) && $param['types']){
			$where[] = array('types','=',$param['types']);
			$pageParam['types'] = $param['types'];
		}
		// 商户状态
		if(isset($param['status']) && $param['status']){
			$where[] = array('status','=',$param['status']);
			$pageParam['status'] = $param['status'];
		}
		// 时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('reg_time','>=',strtotime($dateTime[0]));
			$where[] = array('reg_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}
		// 查询符合条件的数据
		$resultData = $this->where($where)->order('reg_time','desc')->paginate(15,false,['query'=>$pageParam]);
		// 获取配置
		$merchantType = config('custom.merchantType');
		$merchantVerify = config('custom.merchantVerify');
		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));
		// 重新赋值
		foreach ($resultData as $key => &$value) {
			$value['typesStr']  = $merchantType[$value['types']];
			$value['statusStr'] = $merchantVerify[$value['status']];
			// 商户总数量
			$value['merNum'] = model('Merchant')->join('merchant_team','ly_merchant.id = merchant_team.team')->where(array(['merchant_team.uid','=',$value['id']],['team','<>',$value['id']],['ly_merchant.types','=',2]))->count();
			// 当日新增商户数量
			$value['todayNum'] = model('Merchant')->join('merchant_team','ly_merchant.id = merchant_team.team')->where(array(['merchant_team.uid','=',$value['id']],['team','<>',$value['id']],['ly_merchant.types','=',2],['ly_merchant.reg_time','>=',$today]))->count();
		}
		
		return array(
			'data'           =>	$resultData->toArray()['data'], // 数据
			'page'           =>	$resultData->render(), // 分页
			'where'          =>	$pageParam,
			'merchantType'   =>	$merchantType,
			'merchantVerify' =>	$merchantVerify,
		);
	}

	public function merAdd(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');
		if (!$param) return '提交失败';

		$validate = validate('app\admin\validate\Agentcentre');
		if(!$validate->scene('merAdd')->check($param)) return $validate->getError();
		// 密码加密并添加注册时间
		$param['sid']        = session('admin_userid');
		$param['password']   = auth_code($param['password'],'ENCODE');
		$param['merchantid'] = md5(trading_number());
		$param['reg_time']   = time();

		//添加用户数据
		$insertUsers = $this->allowField(true)->save($param);
		if(!$insertUsers) return '添加失败';
		$insertId = $this->id;
		
		//将该账户添加至user_total表
		$insertTotal = model('MerchantTotal');
		$insertTotalId = $insertTotal->insertGetId(['uid'=>$insertId]);
		if(!$insertTotalId){
			$this->destroy($this->id);
			return '添加失败';
		}

		// 注册至团队表
		$insertTeam = model('manage/MerchantTeam')->reg($insertId);
		if (!$insertTeam) {
			$this->destroy($this->id);
			$insertTotal->where('uid', $insertId)->delete();
			return '添加失败';
		}

		//添加操作日志
		model('Actionlog')->actionLog(session('admin_username'),'添加用户名为'.$param['username'].'的商户',1);

		return 1;
	}

	public function merEdit(){
		if(!request()->isAjax()) return '非法提交';
		$param = input('post.');
		if (!$param) return '提交失败';

		if (isset($param['alipay_fee'])) $checkArr['alipay_fee_edit'] = $param['alipay_fee'];
		if (isset($param['wechat_fee'])) $checkArr['wechat_fee_edit'] = $param['wechat_fee'];
		if (isset($param['bank_fee'])) $checkArr['bank_fee_edit']     = $param['bank_fee'];

		$validate = validate('app\admin\validate\Agentcentre');
		if(!$validate->scene('merEdit')->check($checkArr)) return $validate->getError();

		// 获取当前商户和该商户的直属商户费率
		$merInfo = $this->field('id,alipay_fee,wechat_fee,bank_fee')->where('id', session('admin_userid'))->whereOr('sid', session('admin_userid'))->select()->toArray();
		// 提取数据
		foreach ($merInfo as $key => $value) {
			if ($value['id'] == session('admin_userid')) {
				$selfData = $value;
			} else {
				$alipayFee[] = $value['alipay_fee'];
				$wechatFee[] = $value['wechat_fee'];
				$bankFee[]   = $value['bank_fee'];
			}
		}
		sort($alipayFee);
		sort($wechatFee);
		sort($bankFee);
		// 下调限制
		if ((isset($param['alipay_fee']) && $param['alipay_fee'] < 0) && abs($param['alipay_fee']) > $alipayFee[0]) {
			return '支付宝下调费率可操作范围为：0.1-'.($alipayFee[0] - $selfData['alipay_fee']);
		}
		if ((isset($param['wechat_fee']) && $param['wechat_fee'] < 0) && abs($param['wechat_fee']) > $wechatFee[0]) {
			return '微信下调费率可操作范围为：0.1-'.($wechatFee[0] - $selfData['wechat_fee']);
		}
		if ((isset($param['bank_fee']) && $param['bank_fee'] < 0) && abs($param['bank_fee']) > $bankFee[0]) {
			return '银行下调费率可操作范围为：0.1-'.($bankFee[0] - $selfData['bank_fee']);
		}

		$log = '';
		// 获取平台设置
		$setting = model('Setting')->field('m_alipay_fee_min,m_alipay_fee_max,m_wechat_fee_min,m_wechat_fee_max,m_bank_fee_min,m_bank_fee_max')->findOrEmpty();
		// 获取团队商户信息
		$teamInfo = $this->field('team,alipay_fee,wechat_fee,bank_fee')->join('merchant_team', 'ly_merchant.id = merchant_team.team')->where(array(['merchant_team.uid', '=', $param['id']]))->select()->toArray();
		foreach ($teamInfo as $key => $value) {
			$updateArr = array();
			if (isset($param['alipay_fee']) && $param['alipay_fee']) {
				$updateArr['alipay_fee'] = $value['alipay_fee'] + $param['alipay_fee'];
				if ($updateArr['alipay_fee'] < $selfData['alipay_fee']) $updateArr['alipay_fee'] = $selfData['alipay_fee'];
				if ($updateArr['alipay_fee'] > $setting['m_alipay_fee_max']) $updateArr['alipay_fee'] = $setting['m_alipay_fee_max'];
				$log .= '支付宝'.$param['alipay_fee'];
			}
			if (isset($param['wechat_fee']) && $param['wechat_fee']) {
				$updateArr['wechat_fee'] = $value['wechat_fee'] + $param['wechat_fee'];
				if ($updateArr['wechat_fee'] < $selfData['wechat_fee']) $updateArr['wechat_fee'] = $selfData['wechat_fee'];
				if ($updateArr['wechat_fee'] > $setting['m_wechat_fee_max']) $updateArr['wechat_fee'] = $setting['m_wechat_fee_max'];
				$log .= '微信'.$param['wechat_fee'];
			}
			if (isset($param['bank_fee']) && $param['bank_fee']) {
				$updateArr['bank_fee'] = $value['bank_fee'] + $param['bank_fee'];
				if ($updateArr['bank_fee'] < $selfData['bank_fee']) $updateArr['bank_fee'] = $selfData['bank_fee'];
				if ($updateArr['bank_fee'] > $setting['m_bank_fee_max']) $updateArr['bank_fee'] = $setting['m_bank_fee_max'];
				$log .= '银行'.$param['bank_fee'];
			}
			if (!$updateArr) continue;
			// 修改团队成员费率
			$this->where('id', $value['team'])->update($updateArr);
		}

		//添加操作日志
		model('Actionlog')->actionLog(session('admin_username'),'调整团队费率，'.$log,3);

		return 1;
	}
}