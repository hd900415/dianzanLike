<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\controller;

use app\manage\controller\Common;

class ActivityController extends CommonController{
	/**
	 * 空操作处理
	 */
	public function _empty(){
		return $this->lists();
	}

	/**
	 * 活动列表
	 */
	public function lists(){
		if (request()->isAjax()) {
			$param = input('param.');

			$count              = model('ActivityList')->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'sort';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'asc';

			//查询符合条件的数据
			$data = model('ActivityList')->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			foreach ($data as $key => &$value) {
				$value['start_time'] = date('Y-m-d H:i:s', $value['start_time']);
				$value['end_time']   = date('Y-m-d H:i:s', $value['end_time']);
			}

			return json([
				'code'  => 0,
				'msg'   => '',
				'count' => $count,
				'data'  => $data
			]);
		}

		return view();
	}

	/**
	 * 添加活动
	 */
	public function add(){
		if(request()->isAjax()){
			return model('ActivityList')->ActivityListAdd();
		}
		return $this->fetch();
	}

	/**
	 * 活动开关
	 */
	public function activityOnoff(){
		return model('ActivityList')->onOff();
	}

	/**
	 * 活动删除
	 */
	public function delete(){
		return model('ActivityList')->activityDel();
	}

	/**
	 * 编辑活动
	 */
	public function edit(){
		$data = model('ActivityList')->activityEditView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 活动记录
	 */
	public function activity_record(){
		if (request()->isAjax()) {
			$param = input('param.');

			//查询条件组装
			$where = array();
			//用户名
			if(isset($param['username']) && $param['username']){
				$where[] = array('users.username','=',$param['username']);
			}
			// 时间
			if(isset($param['datetime_range']) && $param['datetime_range']){
				$dateTime = explode(' - ', $param['datetime_range']);
				$where[] = array('date','>=',strtotime($dateTime[0]));
				$where[] = array('date','<=',strtotime($dateTime[1]));
			}else{
				$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
				$where[] = array('date','>=',$todayStart);
				$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
				$where[] = array('date','<=',$todayEnd);
			}

			$count              = model('UserActivity')->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'date';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			$data = model('UserActivity')->field('ly_user_activity.*,users.username')->join('users','ly_user_activity.uid = users.id','left')->where($where)->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			foreach ($data as $key => &$value) {
				$value['date'] = date('Y-m-d', $value['date']);
				$value['set_time'] = date('Y-m-d H:i:s', $value['set_time']);
				$value['stateStr']   = ($value['state'] == 1) ? '未领取' : '已领取';
			}

			return json([
				'code'  => 0,
				'msg'   => '',
				'count' => $count,
				'data'  => $data
			]);
		}

		return view();
	}

	/**
	 * 工资配置
	 */
	public function wage_list(){
		$data = model('WagePlan')->wageList();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('power',$data['power']);

		return $this->fetch();
	}

	/**
	 * 工资配置删除
	 */
	public function wage_delete(){
		return model('WagePlan')->wageDel();
	}

	/**
	 * 工资发放记录
	 */
	public function wage_record(){
		$data = model('WageDaily')->wageDaily();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('power',$data['power']);

		return $this->fetch();
	}

	/**
	 * 分红配置
	 */
	public function bonu_list(){
		$data = model('BonusPlan')->bonusList();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('power',$data['power']);

		return $this->fetch();
	}

	/**
	 * 分红方案删除
	 */
	public function bonu_delete(){
		return model('BonusPlan')->bonusDel();
	}

	/**
	 * 分红发放记录
	 */
	public function bonu_record(){
		$data = model('BonusList')->bonusRecord();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('power',$data['power']);

		return $this->fetch();
	}

	/**
	 * 活动缩略图上传
	 * @return [type] [description]
	 */
	public function coverUpload(){
		//二维码图片
		$file = request()->file('file');
		
		//上传路径
		$uploadPath = './upload/image';
		if(!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

		$info = $file->validate(['size'=>1000*1024*5,'ext'=>'jpg,png,gif,jpeg'])->move($uploadPath, '');
		if($info){
			// 成功上传后 获取上传信息
			$filePath = str_replace('\\', '/', $info->getSaveName());
			return json(['success'=>ltrim($uploadPath, '.').'/'.$filePath]);
		}else{
			// 上传失败获取错误信息
			return json(['success'=>$file->getError()]);
		}
	}

	/**
	 * 派发每日工资
	 */
	public function wage_grant(){
		if(request()->isAjax()){
			return model('WageGrant')->wageGrant();
		}
		return $this->fetch();
	}

	/**
	 * 派发每日分红
	 */
	public function bonusEveryday(){
		if(request()->isAjax()){
			return model('BonusDate')->bonusGrant();
		}
		return $this->fetch();
	}

	/**
	 * 派发亏损分红
	 */
	public function bonusLoss(){
		if(request()->isAjax()){
			return model('BonusLoss')->bonusGrant();
		}
		return $this->fetch();
	}

	/**
	 * 派发周期分红
	 */
	public function bonu_grant(){
		if(request()->isAjax()){
			return model('BonuGrant')->bonusGrant();
		}
		return $this->fetch();
	}

	/**
	 * 派发低频工资
	 */
	public function lowWageGrant(){
		if(request()->isAjax()){
			return model('LowWage')->lowWageGrant();
		}
		return view();
	}

	/**
	 * 派发周期工资
	 */
	public function wageWeekGrant(){
		if(request()->isAjax()){
			return model('WageWeek')->wageWeekGrant();
		}
		return view();
	}


	/**
	 * 锦上添花活动记录
	 */
	public function profitReturn(){
		$data = model('ProfitReturn')->profitReturn();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('power',$data['power']);
		
		return $this->fetch();
	}

	/**
	 * 锦上添花操作
	 */
	public function profitReturnEdit(){
		return model('ProfitReturn')->profitReturnEdit();
	}

	/**
	 * 佣金记录
	 * @return [type] [description]
	 */
	public function userFeeList(){
	    if (request()->isAjax()) {
	        $data = model('UserCommission')->userFeeListnew();
	        
	        foreach ($data['data'] as $key => $value) {
	            $guser = model('users')->where('id', '=', $value['gid'])->find();
	            if ($guser) {
	                $value['gname'] = $guser['username'];
	            } else {
	                $value['gname'] = '';
	            }
	            
	            if ($value['date']) {
	                $value['date_label'] = date('Y-m-d H:i:s', $value['date']);
	            } else {
	                $value['date_label'] = '';
	            }
	            if ($value['issue_time']) {
	                $value['issue_time_label'] = date('Y-m-d H:i:s', $value['issue_time']);
	            } else {
	                $value['issue_time_label'] = '';
	            }
	            
	            
	            $data['data'][$key] = $value;
	        }

    		return  json([
				'code'  => 0,
				'msg'   => '',
				'data' => $data['data'],
			    'count'	  => $data['count']
			]);
	    } else {
	        return view();
	    }
		
	}

	/**
	 * 发放每日佣金
	 * @return [type] [description]
	 */
	public function commissionGrant(){		
		if(request()->isAjax()){
			return model('UserCommission')->commissionGrant();
		}
		return view();
	}
}