<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\controller;

use app\manage\controller\Common;

/**
	A-阿大
	游戏设置控制器
**/

class GameController extends CommonController{
	//空操作处理
	public function _empty(){
		return $this->classify();
	}

	//游戏分类
	public function classify(){
		$data = model('PlayClass')->classify();

		$this->assign('list',$data['list']);
		$this->assign('power',$data['power']);
		
		return $this->fetch();
	}
	
	//添加游戏分类
	public function classify_add(){
		
		if($this->request->isAjax()){
			
			$param = model('PlayClass')->classify_add();
			return $param;
		}
		return $this->fetch();
	}
	
	//编辑分类
	public function classify_edit(){
		if($this->request->isAjax()){
			$param =  model('PlayClass')->classify_edit();
			return $param;
		}
		$data = model('PlayClass')->getPlayClassById();
		$this->assign('data',$data);
		return $this->fetch();
		
	}
	
	//删除彩种
	public function classify_delete(){
		$param =  model('PlayClass')->classify_delete();
		return $param;
	}

	
	
	//系统玩法
	public function play(){
		
		$data = model('PlayBasics')->play();
		
		$this->assign('PlayClass',$data['PlayClass']);

		$this->assign('list',$data['list']);

		$this->assign('power',$data['power']);

		return $this->fetch($data['table']);
	}
	
	//添加玩法
	public function play_add(){
				
		if(request()->isAjax()){
			return model('PlayBasics')->add_play();
		}
		$param = input('get.');

		$this->assign('data',$param);
		
		return $this->fetch();
	}
	//编辑玩法
	public function play_edit(){

		if(request()->isAjax()){
			$param = input('post.');
			return model($param['modelName'])->play_edit();
		}

		$param = input('get.');

		$data = model($param['modelName'])->getPlayByIdEdit($param['id']);

		$this->assign('data',$data);
		$this->assign('modelName',$param['modelName']);

		return $this->fetch();

	}
	//删除玩法
	public function play_delete(){
		$param = model('PlayBasics')->play_delete();
		return $param;

	}
	//玩法开关
	public function play_on_off(){
		return model('PlayGame')->setFieldValue();	
	}
	//玩法默认
	public function play_defaults(){
		return model('PlayGame')->setFieldValue();
	}
	//玩法默认
	public function playIsopen(){
		return model('PlayGame')->setFieldValue();
	}
	
	//彩种玩法
	public function lottery_play(){
		
		$data = model('PlayGame')->lottery_play();
		
		$this->assign('playInfo',$data['playInfo']);
		$this->assign('classInfo',$data['classInfo']);
		$this->assign('modelName',$data['modelName']);		
		$this->assign('power',$data['power']);
		
		return $this->fetch();
	}
	
	//彩种列表
	public function lottery(){
		
		$data = model('PlayClass')->classify();
		
		$this->assign('list',$data['list']);
		
		$this->assign('power',$data['power']);
		
		return $this->fetch();

	} 
	
	
	//添加彩种
	public function lottery_add(){
		if($this->request->isAjax()){
			$param =  model('PlayClass')->lottery_add();
			return $param;
		}

		$types = input('get.types/s');//获取参数
		$this->assign('types',$types);
		$this->assign('betTable',config('custom.betTable'));//存放的分表
		$this->assign('model',config('custom.model'));//开奖模型
		$this->assign('hoverType',config('custom.hoverType'));//菜单类型
		$this->assign('model',config('custom.model'));//开奖模型

		return $this->fetch();
	}
	
	//编辑彩种
	public function lottery_edit(){
		if($this->request->isAjax()){
			$param =  model('PlayClass')->lottery_edit();
			return $param;
		}
		
		$data = model('PlayClass')->getLotteryByIdEdit();
		$this->assign('data',$data);
		$this->assign('betTable',config('custom.betTable'));//存放的分表
		$this->assign('model',config('custom.model'));//开奖模型
		$this->assign('hoverType',config('custom.hoverType'));//菜单类型
		$this->assign('model',config('custom.model'));//开奖模型
		return $this->fetch();
	}
	
	//删除彩种
	public function lottery_delete(){
		$param =  model('PlayClass')->lottery_delete();
		return $param;
	}
	//彩种开关
	public function lottery_on_off(){
		return model('PlayClass')->setFieldValue();
	}
	//彩种热门开关
	public function lottery_hot(){
		return model('PlayClass')->setFieldValue();
	}
	//彩种采集开关
	public function lottery_collect(){
		return model('PlayClass')->setFieldValue();
	}
	//生成系统时间
	public function generate_set_time(){
		if($this->request->isAjax()){
			$param =  model('PlayTime')->generate_set_time();
			return $param;
		}
		$data = model('PlayClass')->getLotteryByIdEdit();
		$this->assign('data',$data);
		return $this->fetch();
	}
	
	//系统时间
	public function set_time(){
		
		$data = model('PlayTime')->set_time();
		
		$this->assign('list',$data['list']);
		$this->assign('page',$data['page']);
		$this->assign('lottery',$data['lottery']);
		$this->assign('power',$data['power']);

		//输出变量到页面
		return $this->fetch();
	}
	
	//添加系统时间
	public function set_time_add(){
		if($this->request->isAjax()){
			$param =  model('PlayTime')->set_time_add();
			return $param;
		}
		$data = model('PlayClass')->where('class',input('get.lottery/s'))->find();
		$this->assign('data',$data);
		return $this->fetch();

	}
	
	//修改系统时间
	public function set_time_edit(){
		
		if($this->request->isAjax()){
			$param =  model('PlayTime')->set_time_edit();
			return $param;
		}
		$data = model('PlayTime')->where('id',input('get.id/d'))->find();
		$this->assign('data',$data);
		return $this->fetch();
		
	}
	//删除系统时间
	public function set_time_delete(){
		
		$param =  model('PlayTime')->set_time_delete();
		return $param;

	}
	//系统期号购买开关
	public function set_no_buy(){
		$param = model('PlayTime')->set_no_buy();
		return $param;
	}

	//彩种时间
	public function lottery_time(){

		$data = model('PlayTime')->lottery_time();

		$this->assign('list',$data['list']);
		$this->assign('page',$data['page']);
		$this->assign('lottery',$data['lottery']);
		$this->assign('power',$data['power']);
		$this->assign('thistime',time());


		//输出变量到页面
		return $this->fetch();
	}
	
	//彩种期号购买开关
	public function no_buy(){
		$param = model('PlayTime')->no_buy();
		return $param;
	}
	//编辑彩种期号
	public function no_edit(){
		
		if($this->request->isAjax()){
			$param =  model('PlayTime')->no_edit();
			return $param;
		}
		$data = model(input('get.lottery/s'))->where('id',input('get.id/d'))->find();
		$this->assign('lottery',input('get.lottery/s'));
		$this->assign('data',$data);
		return $this->fetch();
	
	}
	
	//彩种列表
	public function no(){
		
		$data = model('PlayClass')->classify();
		
		$this->assign('list',$data['list']);
		
		$this->assign('power',$data['power']);
		
		return $this->fetch();

	} 
	
	//彩种期号
	public function lottery_no(){

		$data = model('PlayTime')->lottery_time();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('power',$data['power']);
		$this->assign('thistime',time());
		$this->assign('zzk3', ['zzwxk3','zzbdk3','zzsck3','zzcqk3','dfk3','df3fk3','df5fk3','df10fk3',]);
		
		//输出变量到页面
		return $this->fetch();
	}
	
	//手动开奖
	public function no_manual_open_code(){

		if($this->request->isAjax()){
			$param =  model('PlayTime')->no_manual_open_code();
			return $param;
		}
		
		$param = input('get.');

		$param['no'] = isset($param['no']) ? $param['no'] : '';
		$this->assign('lottery',$param['lottery']);
		$this->assign('no',$param['no']);
		$data = model('PlayClass')->field('name,class,class_name')->where('class',$param['lottery'])->find();
		$this->assign('data',$data);
		return $this->fetch();

	}
	//修复
	public function no_repair_open_code(){
		$param =  model('PlayTime')->no_repair_open_code();
		return $param;

	}	
	
	//预先开奖
	public function no_beforehand_open_code(){

		if($this->request->isAjax()){
			$param =  model('PlayTime')->no_beforehand_open_code();
			return $param;
		}
		
		$param = input('get.');

		$param['no'] = isset($param['no']) ? $param['no'] : '';

		$this->assign('lottery',$param['lottery']);
		$this->assign('no',$param['no']);

		$data = model('PlayClass')->where('class',$param['lottery'])->field('class,class_name')->find();
		$this->assign('data',$data);

		return $this->fetch();

	}

	/**
	 * 热门排序
	 * @return [type] [description]
	 */
	public function hotSort(){
		if($this->request->isAjax()){
			return model('PlayClass')->hotSort();
		}

		$data = model('PlayClass')->getMore(array(
			['name','neq',''],
			['hot','=',1],
			['state','=',1]
		), 'id,class,class_name,sort', 'sort', 'asc');

		return view('', [
			'data'	=>	$data
		]);
	}

	/**
	 * 每日时间手动生成
	 */
	public function createTime(){
		if (request()->isAjax()) {
			return model('PlayTime')->createTime();
		}

		$param = input('get.');

		return view('', [
			'lottery'	=>	($param['lottery']) ? $param['lottery'] : '' ,
		]);
	}

	/**
	 * 批量开奖
	 */
	public function openMany(){
		if (request()->isAjax()) {
			return model('PlayTime')->openMany();
		}

		$data = model('PlayTime')->openManyView();
		
		return view('', [
			'data'		=>	$data['data'],
			'where'		=>	$data['where'],
		]);
	}

	/**
	 * 快捷按钮获取开奖号码
	 */
	public function getKjOpenCode(){
		if (!request()->isAjax()) return '非法提交';
		$param = input('post.');
		if (!$param) return '提交失败';

		$openCode = model('PlayTime')->getKjOpenCode($param['lottery'], $param['size'], $param['eao']);

		return $openCode;
	}

	public function rankOpenCode(){
		$array = range(1,10);
		shuffle($array); //随机排序
		foreach ($array as $key => &$value) {
			$value = str_pad($value, 2, '0', STR_PAD_LEFT);
		}
		return implode(',', $array);
	}

}