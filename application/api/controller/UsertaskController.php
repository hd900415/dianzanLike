<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
 
namespace app\api\controller;

use app\api\controller\BaseController;

class UsertaskController extends BaseController{
	//领取任务————状态：进行中
	public function reciveTask(){
		$data = model('UserTask')->reciveTask();
		return json($data);
	}
	
	
	//提交任务————状态：审核中、完成、失败
	public function submitTask(){
		$data = model('UserTask')->submitTask();
		return json($data);
	}
	
	
	//获取任务列表
	public function getTaskList(){
		$data = model('UserTask')->getTaskList();
		return json($data);
	}
	
	//获取任务完成排名列表
	public function getTaskRankList(){
		$data = model('UserTask')->getTaskRankList();
		return json($data);
	}
	
	
	//获取任务类型列表
	public function getTaskClassList(){
		$data = model('TaskClass')->getTaskClassList();
		return json($data);
	}
	
	
}