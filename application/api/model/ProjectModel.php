<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 用于获取系统设置数据
 */

namespace app\api\model;

use think\Model;

class ProjectModel extends Model{
	//表名
	protected $table = 'ly_project';
	
	//获取项目列表
	public function getProjectlist(){

		$ProjectGrouplist = model('ProjectGroup')->where(array('state'=>1))->order('id','ASC')->select()->toArray();
		$ProjectGroupdata = [];
		foreach ($ProjectGrouplist as $key => $value) {
			$ProjectGroupdata[$key]['group_id']   		= $value['id'];
			$ProjectGroupdata[$key]['group_name']		= $value['group_name'];
			$ProjectGroupdata[$key]['group_info']		= $value['group_info'];
			$Projectlist = model('Project')->field('id,title,Guarantee_institution,Project_scale,Repayment_method_info,Daily_income,Project_duration,Project_remaining,Repayment_method_info,Starting_money,state')->where(array(['Project_remaining','>',0],['state','<>',2],['gropid','=',$value['id']]))->order('add_time','DESC')->select()->toArray();

			$Projectdata = [];
			foreach ($Projectlist as $key2 => $value2) {
				$ProjectGroupdata[$key]['projectlist'][$key2] = $value2;
			}
		}
		
		return $data['projectgrouplist'] = $ProjectGroupdata;

		
		$param			= input('param.');
		
		$where   = array(['state','<>',2]);
		
		if (isset($param['gropid']) && $param['gropid']) {
			$where   = array(['state','<>',2],['gropid','=',$param['gropid']]);
		}
		
		$count   = $this->where($where)->count();
		
		if(!$count){
			$data['code']		= 0;
			$data['code_dec']	= '暂无项目';
			return $data;
		}
		
		//每页显示记录
		$pageSize = (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		//当前的页,还应该处理非数字的情况
		$pageNo = (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		//总页数
		$pageTotal = ceil($count / $pageSize);//当前页数大于最后页数，取最后	
		//记录数
		$limitOffset = ($pageNo - 1) * $pageSize;
		$orderdata	=	$this->where($where)
							->field('id,gropid,title,Guarantee_institution,Project_scale,Daily_income,Project_duration,Project_remaining,Starting_money,Repayment_method_info,state')
							->order('add_time','DESC')
							->limit($limitOffset, $pageSize)
							->select()->toArray();

		$data						=	[];
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;
		
		foreach($orderdata as $key => $value){
			$data['list'][$key]['id']							=	$value['id'];
			$data['list'][$key]['gropid']						=	$value['gropid'];
			$data['list'][$key]['title']						=	$value['title'];
			$data['list'][$key]['Guarantee_institution']		=	$value['Guarantee_institution'];
			$data['list'][$key]['Project_scale']				= 	$value['Project_scale'];
			$data['list'][$key]['Daily_income']					= 	$value['Daily_income'];
			$data['list'][$key]['Project_duration']				= 	$value['Project_duration'];
			$data['list'][$key]['Project_remaining']			=	$value['Project_remaining'];
			$data['list'][$key]['Starting_money']				=	$value['Starting_money'];
			$data['list'][$key]['state']						= 	$value['state'];
			$data['list'][$key]['Repayment_method_info']		= 	$value['Repayment_method_info'];
		}

		return $data;

	}
	
	
	//获取项目详细
	public function getProjectinfo(){
		$param			= input('param.');

		$where   = array(['state','<>',2],['id','=',$param['id']]);
		
		$Projectinfo	=	$this->where($where)->find();
		
		if(!$Projectinfo){
			$data['code']		= 0;
			$data['code_dec']	= '暂无项目';
			return $data;
		}
		switch($Projectinfo['Repayment_method']){
			case 1:case 5://1每日返息，到期还本 5 每日复利，保本保息
				$interest_time_info	=	'满<b>24小时</b>自动结息'; 
			break;
			case 4:
				$interest_time_info	=	'到期还本，到期付息';
			break;
			case 2:
				$interest_time_info	=	'满<b>7</b>天 自动结息';
			break;
			case 3:
				$interest_time_info	=	'满<b>30</b>天 自动结息';
			break;
		}
		
		$data									=	[];
		$data['code'] 							= 	1;
		$Projectinfo['interest_time_info']		=	$interest_time_info;	
		$data['info']							=	$Projectinfo;
		return $data;
	}


}