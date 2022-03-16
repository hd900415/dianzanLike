<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\controller;

use app\manage\controller\Common;
/**
	A-阿大
	庄家控制器
**/

class BankerController extends CommonController{
	//空操作处理
	public function _empty(){
		return $this->room_list();
	}
	
	//房间列表
	public function room_list(){

		$data = model('Room')->room_list();
				
		//数据
		$this->assign('list',$data['list']);
		//分页
		$this->assign('page',$data['page']);
		//权限
		$this->assign('power',$data['power']);

		return $this->fetch();
	}
	
	//编辑房间
	public function room_edit(){
		
		if($this->request->isAjax()){
			$param = model('Room')->room_edit();
			return $param;
		}

		$data = model('Room')->where('id',input('get.id/d'))->find();
		
		$this->assign('data',$data);
		
		return $this->fetch();

	}

	//房间开关
	public function room_on_off(){
		
		$param = model('Room')->room_on_off();
		return $param;
	
	}
	
	//撤销房间
	public function room_delete(){
		
		$param = model('Room')->room_delete();
		return $param;

	}
	//投注列表
	public function bet(){
		
		//获取彩种玩法
		if(request()->isAjax()){
			return model('PlayGame')->getThreePlay();
		}
		
		$data = model('BettingRoom')->betList();
		
		$this->assign('betList',$data['betList']);
		//分页统计
		$this->assign('pageTotal',$data['pageTotal']);
		//分页
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('orderState',$data['orderState']);
		$this->assign('lotteryList',$data['lotteryList']);
		//彩种玩法
		$this->assign('lotteryPlay',$data['lotteryPlay']);
		//权限
		$this->assign('power',$data['power']);

		return $this->fetch();
		

	}
	//庄闲流水
	public function financial(){
		
		$data = model('RoomUserDetails')->financial();
		$this->assign('tradeList',$data['tradeList']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('tradeType',$data['tradeType']);
		$this->assign('power',$data['power']);
		return $this->fetch();
	}
	
	//房间流水
	public function room_financial(){
		
		$data = model('RoomTradeDetails')->financial();
		$this->assign('tradeList',$data['tradeList']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('tradeType',$data['tradeType']);
		$this->assign('power',$data['power']);
		return $this->fetch();
	}
	//房间统计
	public function room_details(){
		//获取彩种玩法
		if(request()->isAjax()){
			return model('PlayGame')->getThreePlay();
		}
		
		$data = model('RoomDetails')->room_details();
		
		$this->assign('betList',$data['betList']);
		//分页统计
		$this->assign('pageTotal',$data['pageTotal']);
		//分页
		$this->assign('page',$data['page']);
		
		$this->assign('where',$data['where']);
		
		$this->assign('lotteryList',$data['lotteryList']);
		//彩种玩法
		$this->assign('lotteryPlay',$data['lotteryPlay']);

		return $this->fetch();
	}
	//上庄列表
	public function room_line(){
		$data = model('RoomLine')->room_list();
				
		//数据
		$this->assign('list',$data['list']);
		//分页
		$this->assign('page',$data['page']);
		//权限
		$this->assign('power',$data['power']);

		return $this->fetch();
	}
	//庄闲每日
	public function report_data(){
		
		$data = model('RoomUserDaily')->everyday();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('totalAll',$data['totalAll']);
		$this->assign('totalPage',$data['totalPage']);
		return $this->fetch();
	}
	//房间每日
	public function room_data(){
		
		$data = model('RoomDaily')->everyday();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('totalAll',$data['totalAll']);
		$this->assign('totalPage',$data['totalPage']);
		return $this->fetch();
	}
	//每期报表
	public function room_no(){
		
		$data = model('RoomNoDetails')->everyNo();

		$this->assign('noList',$data['noList']);
		$this->assign('lotteryList',$data['lotteryList']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);

		return $this->fetch();
	}
	//团队报表
	public function team_statistic(){
		
		$data = model('Users')->RoomteamStatistic();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);

		return $this->fetch();

	}
}
