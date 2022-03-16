<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace nicia;

// GatewayClient 3.0.0版本开始要使用命名空间
use GatewayClient\Gateway;

/**
 * 
 */
class ClassName{
	
	function __construct(){
		
	}

	/**
	 * 绑定UID
	 * @return [type] [description]
	 */
	public function bind(){
		/**
		 *====这个步骤是必须的====
		 *这里填写Register服务的ip和Register端口，注意端口不是gateway端口
		 *ip不能是0.0.0.0，端口在start_register.php中可以找到
		 *这里假设GatewayClient和Register服务都在一台服务器上，ip填写127.0.0.1。
		 *如果不在一台服务器则填写真实的Register服务的内网ip(或者外网ip)
		 **/
		Gateway::$registerAddress = '127.0.0.1:1236';

		// 假设用户已经登录，用户uid和群组id在session中
		$uid      = $_SESSION['uid'];
		$group_id = $_SESSION['group'];
		// client_id与uid绑定
		Gateway::bindUid($client_id, $uid);
		// 加入某个群组（可调用多次加入多个群组）
		Gateway::joinGroup($client_id, $group_id);
	}

	/**
	 * 推送数据
	 * @return [type] [description]
	 */
	public function send(){
		/**
		 *====这个步骤是必须的====
		 *这里填写Register服务的ip和Register端口，注意端口不是gateway端口
		 *ip不能是0.0.0.0，端口在start_register.php中可以找到
		 *这里假设GatewayClient和Register服务都在一台服务器上，ip填写127.0.0.1。
		 *如果不在一台服务器则填写真实的Register服务的内网ip(或者外网ip)
		 **/
		Gateway::$registerAddress = '127.0.0.1:1236';

		// 向任意uid的网站页面发送数据
		Gateway::sendToUid($uid, $message);
		// 向任意群组的网站页面发送数据
		Gateway::sendToGroup($group, $message);
	}
}