<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\controller;

use app\manage\controller\Common;

use app\api\model\YuebaoModel;
use app\api\model\YuebaojiluModel;
use app\api\model\UsersModel;

class YuebaoController extends CommonController{
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

			$count              = model('YuebaoList')->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'id';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'asc';

			//查询符合条件的数据
			$data = model('YuebaoList')->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();


			return json([
				'code'  => 0,
				'msg'   => '',
				'count' => $count,
				'data'  => $data
			]);
		}

		return view();
	}
	
	public function jilulist(){
	    if (request()->isAjax()) {
			$param = input('param.');

			// 总记录数
			$YuebaoModel = new YuebaoModel();
			$YuebaojiluModel = new YuebaojiluModel();
			$UsersModel = new UsersModel();
			
			$count              = $YuebaojiluModel->count();
			
			// 每页记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; 
			// 当前页
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1;
			// 偏移量
			$limitOffset        = ($param['page'] - 1) * $param['limit'];
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'id';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'asc';

			//查询符合条件的数据
			$data = $YuebaojiluModel->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			
			foreach ($data as $key => $value) {
			    $user = $UsersModel->where('id', $value['uid'])->find();
			    if ($user) {
			          $value['username'] = $user['username'];
			    } else {
			          $value['username'] = '';
			    }
			    
			    
			    $yuebao_item = $YuebaoModel->where('id', $value['yuebaoid'])->find();
			    if ($yuebao_item) {
			        $value['title']   = $yuebao_item['title'];
			    } else {
			        $value['title'] = '';
			    }
			    
			  
			    
			    
			   
			    
			    $data[$key] = $value;
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
			return model('YuebaoList')->YuebaoListAdd();
		}
		return $this->fetch();
	}

	/**
	 * 活动开关
	 */
	public function yuebaoOnoff(){
		return model('YuebaoList')->onOff();
	}

	/**
	 * 活动删除
	 */
	public function delete(){
		return model('YuebaoList')->yuebaoDel();
	}

	/**
	 * 编辑活动
	 */
	public function edit(){
		$data = model('YuebaoList')->yuebaoEditView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}
}