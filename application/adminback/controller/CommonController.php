<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\controller;

use think\Controller;

class CommonController extends Controller{
	//ThinkPHP构造函数
    public function initialize(){
    	header('Access-Control-Allow-Origin:*');
		ini_set ('session.cookie_lifetime',86400);
		ini_set ('session.gc_maxlifetime',86400);
		
		//判断是否登陆
		$is_admin_login = session('is_admin_login');
		if(!isset($is_admin_login) || empty($is_admin_login)) {
			return (request()->isAjax()) ? '未登录！' : $this->success('未登录！', '/admin/index');
		}

		if (session('admin_types') == 2 && cache('adminOnline'.session('admin_types').session('admin_userid')) != session_id()) {
			session('is_admin_login', null);
			session('admin_username', null);
			session('admin_userid', null);
			echo '<script>alert("您的登录已过期或账号已在别处登录！");top.location.href="/admin/index"</script>';
		}

		//判断权限
		$roleUrl = request()->controller().'/'.request()->action();
		$is_role =	model('AdminRole')->checkUsersRole($roleUrl);

		if(!$is_role) return (request()->isAjax()) ? '您没有权限操作！' : $this->success('您没有权限操作！', '/manage/index');
	}

	public function sendSMSCode(){
		$adminUserId = session('admin_userid');
		$userPhone = model('Merchant')->where('id', session('admin_userid'))->value('phone');
		if (!$userPhone) return '请先绑定手机后再操作！';
		// 发送手机验证码
		$sendSMSCode = controller('common/common');
		$sendResult  = $sendSMSCode->sendSMSCode($userPhone);
		
		if ($sendResult['code'] != 1) return '短信验证码发送失败！';
		return 1;
	}

	public function checkSMSCode(){
		$param = input('post.');

		$validate = validate('app\admin\validate\Common');
		if(!$validate->scene('checkSMSCode')->check($param)) return $validate->getError();

		$userPhone = model('Merchant')->where('id', session('admin_userid'))->value('phone');

		$code = cache('C_Code_'.$userPhone);
		if (is_null($code)) return '验证码已失效';

		if ($param['code'] != $code) return '验证码错误';
		return 1;
	}
}
