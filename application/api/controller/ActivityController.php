<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\controller;

use think\Controller;

class ActivityController extends Controller{
	//初始化方法
	protected function initialize(){		
	 	parent::initialize();		
		header('Access-Control-Allow-Origin:*');
		//header('Access-Control-Allow-Credentials: true');
		//header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
		//header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, authKey, sessionId");
    }
	

    /**  获取活动列表  **/
	public function getActivityList(){
		$data = model('Activity')->getActivityList();
		return json($data);
	}
	
	
	/**  获取用户活动记录列表  **/
	public function getUserActivityList(){
		$data = model('Activity')->getUserActivityList();
		return json($data);
	}
	
}
