<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\controller;

use think\Controller;
use think\Db;
header("Access-Control-Allow-Origin: *");
class TaskController extends Controller{
    	//发布任务
	public function publishTask(){
		$data = model('Task')->publishTask();
		return json($data);
	}

	//获取任务列表
	public function getTaskList(){
		$data = model('Task')->getTaskList();
		return json($data);
	}
	
	
	//获取任务类型列表
	public function getTaskClassList(){
		$data = model('TaskClass')->getTaskClassList();
		return json($data);
	}
	//获取任务信息
	public function getTaskinfo(){
		$data = model('Task')->getTaskinfo();
		return json($data);
	}
	
	//撤销任务
	public function revokeTask(){
		$data = model('Task')->revokeTask();
		return json($data);
	}

	//领取任务
	public function receiveTask(){
		$data = model('Task')->receiveTask();
		return json($data);
	}
	
	//领取的任务列表
	public function taskOrderlist(){
		$data = model('Task')->taskOrderlist();
		return json($data);
	}
	
	//领取的任务信息
	public function taskOrderInfo(){
		$data = model('Task')->taskOrderInfo();
		return json($data);
	}

	//提交审核
	public function taskOrderSubmit(){
		$data = model('Task')->taskOrderSubmit();
		return json($data);
	}
	
	//审核
	public function taskOrderTrial(){
		$data = model('Task')->taskOrderTrial();
		return json($data);
	}
    public function index(){
        $time = date('Y-m-d H:i:s',time());
        $data = Db::table('ly_yuebao_pay')->where(array('status'=>1))->whereTime('end_time','<',$time)->select();
        if(empty($data)){
            exit;
        }
        foreach ($data as $k => $v){
            $balance = $v['money'] + ($v['money'] * $v['lilv']);
            Db::table('ly_user_total')->where(array('uid'=>$v['uid']))->setInc('balance', $balance);
            Db::table('ly_yuebao_pay')->where(array('id'=>$v['id']))->update(array('status'=>2));
        }
    }
}