<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\controller;

use think\Controller;

class CommonController extends Controller{
	//ThinkPHP构造函数
    public function initialize(){

    	header('Access-Control-Allow-Origin:*');

		ini_set ('session.cookie_lifetime',86400);

		ini_set ('session.gc_maxlifetime',86400);
		
		//判断是否登陆
		$is_admin_login = session('is_manage_login');

		if(!isset($is_admin_login) || empty($is_admin_login)) {
			if (request()->isAjax()) {
				return '未登录！';
			} else {
				return $this->success('未登录！', '/manage/index');
			}
		}

		// 检查IP
		// $isIpWhite = model('Setting')->where('id','>',0)->value('manage_ip_white');
		// if ($isIpWhite == 2) {
		// 	$ip = request()->ip();
		// 	$ipList = model('IpWhite')->column('ip');
		// 	if (!in_array($ip, $ipList)) {
		// 		session('is_manage_login', null);
		// 		session('manage_username', null);
		// 		session('manage_userid', null);
		// 		echo '<script>alert("您的登录已过期或账号已在别处登录！");top.location.href="/manage/index"</script>';
		// 	}
		// }

		//判断权限
		$where_role = array(
			'uid'		=>	session('manage_userid'),//管理员
			'role_url'	=>	request()->controller().'/'.request()->action(),//获取控制器/方法
			'state'		=>	1,//管理员
		);
		$is_role =	model('ManageUserRole')->checkUsersRole($where_role);

		if(!$is_role){
			if (request()->isAjax()) {
				return '您没有权限操作！';
			} else {
				return $this->success('您没有权限操作！', '/manage/index');
			}			
		}
	}

	/*
	// 获取传输协议
	$isHttps = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')) ? 'https' : 'http';
	return $isHttps.'://'.$_SERVER['HTTP_HOST'];
	 */
}
