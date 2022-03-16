<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
 
namespace app\api\controller;

use app\api\controller\BaseController;

class UserMessageController extends BaseController{
	
	// 留言和评论
	public function makeMessage(){
		$data = model('UserMessage')->makeMessage();
		return json($data);
	}
	
	// 删除留言及评论
	public function deleteMessage(){
		$data = model('UserMessage')->deleteMessage();
		return json($data);
	}
	
	// 获取留言及评论列表
	public function getMessageList(){
		$data = model('UserMessage')->getMessageList();
		return json($data);
	}
	
}