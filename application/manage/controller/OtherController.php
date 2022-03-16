<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\controller;

use app\manage\controller\Common;
use think\facade\Cache;

class OtherController extends CommonController{

	/**
	 * 后端操作日志
	 */
	public function after_operation_log(){
		if (request()->isAjax()) {
			//获取参数
			$param = input('post.');
			//查询条件组装
			$where = array();
			//用户名
			if(isset($param['username']) && $param['username']){
				$where[] = array('username','=',$param['username']);
			}
			//IP
			if(isset($param['ip']) && $param['ip']){
				$where[] = array('ip','=',$param['ip']);
			}
			//IP
			if(isset($param['isadmin']) && $param['isadmin']){
				$where[] = array('isadmin','=',$param['isadmin']);
			}
			// 内容
			if(isset($param['log']) && $param['log']){
				$where[] = array('log','like','%'.$param['log'].'%');
			}
			// 时间
			if(isset($param['datetime_range']) && $param['datetime_range']){
				$dateTime = explode(' - ', $param['datetime_range']);
				$where[] = array('time','>=',strtotime($dateTime[0]));
				$where[] = array('time','<=',strtotime($dateTime[1]));
			}else{
				$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
				$where[] = array('time','>=',$todayStart);
				$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
				$where[] = array('time','<=',$todayEnd);
			}
			$where2   = $where;
			$where[]  = ['isadmin','=',1];
			$where2[] = ['isadmin','=',3];

			$count              = model('Actionlog')->whereOr([$where, $where2])->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'time';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			$data = model('Actionlog')->whereOr([$where, $where2])->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			foreach ($data as $key => &$value) {
				$value['time']    = date('Y-m-d H:i:s', $value['time']);
				$value['isadmin'] = ($value['isadmin'] == 1) ? '运营后台' : '商户后台';
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
	 * 前端操作日志
	 */
	public function front_operation_log(){
		if (request()->isAjax()) {
			//获取参数
			$param = input('post.');
			//查询条件组装
			$where = array();
			//用户名
			if(isset($param['username']) && $param['username']){
				$where[] = array('username','=',$param['username']);
			}
			//IP
			if(isset($param['ip']) && $param['ip']){
				$where[] = array('ip','=',$param['ip']);
			}
			// 时间
			if(isset($param['datetime_range']) && $param['datetime_range']){
				$dateTime = explode(' - ', $param['datetime_range']);
				$where[] = array('time','>=',strtotime($dateTime[0]));
				$where[] = array('time','<=',strtotime($dateTime[1]));
			}else{
				$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
				$where[] = array('time','>=',$todayStart);
				$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
				$where[] = array('time','<=',$todayEnd);
			}
			// 内容
			if(isset($param['log']) && $param['log']){
				$where2   = $where;
				$where[]  = ['params','like','%'.$param['log'].'%'];
				$where2[] = ['values','like','%'.$param['log'].'%'];
			}

			if(isset($param['log']) && $param['log']){
				$count = model('Homelog')->join('users','ly_homelog.uid=users.id')->whereOr([$where, $where2])->count(); // 总记录数
			} else {
				$count = model('Homelog')->join('users','ly_homelog.uid=users.id')->where($where)->count(); // 总记录数
			}

			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'time';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			if(isset($param['log']) && $param['log']){
				$data = model('Homelog')->field('ly_homelog.*,users.username')->join('users','ly_homelog.uid=users.id')->whereOr([$where, $where2])->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			} else {
				$data = model('Homelog')->field('ly_homelog.*,users.username')->join('users','ly_homelog.uid=users.id')->where($where)->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			}

			foreach ($data as $key => &$value) {
				$value['time']    = date('Y-m-d H:i:s', $value['time']);
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
	 * 后台登录日志
	 */
	public function after_login_log(){
		if (request()->isAjax()) {
			//获取参数
			$param = input('post.');
			//查询条件组装
			$where = array(['isadmin','=',1]);

			//用户名
			if(isset($param['username']) && $param['username']){
				$where[] = array('username','=',trim($param['username']));
			}
			//用户名
			if(isset($param['ip']) && $param['ip']){
				$where[] = array('ip','=',trim($param['ip']));
			}
			// 时间
			if(isset($param['datetime_range']) && $param['datetime_range']){
				$dateTime = explode(' - ', $param['datetime_range']);
				$where[]  = array('time','>=',strtotime($dateTime[0]));
				$where[]  = array('time','<=',strtotime($dateTime[1]));
			}else{
				$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
				$where[]    = array('time','>=',$todayStart);
				$todayEnd   = mktime(23,59,59,date('m'),date('d'),date('Y'));
				$where[]    = array('time','<=',$todayEnd);
			}

			$count              = model('Loginlog')->where($where)->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'time';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			$data = model('Loginlog')->where($where)->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			foreach ($data as $key => &$value) {
				$value['time'] = date('Y-m-d H:i:s', $value['time']);
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
	 * 前台登录日志
	 */
	public function front_login_log(){
		if (request()->isAjax()) {
			//获取参数
			$param = input('post.');
			//查询条件组装
			$where = array(['isadmin','=',2]);

			//用户名
			if(isset($param['username']) && $param['username']){
				$where[] = array('username','=',trim($param['username']));
			}
			//用户名
			if(isset($param['ip']) && $param['ip']){
				$where[] = array('ip','=',trim($param['ip']));
			}
			// 时间
			if(isset($param['datetime_range']) && $param['datetime_range']){
				$dateTime = explode(' - ', $param['datetime_range']);
				$where[]  = array('time','>=',strtotime($dateTime[0]));
				$where[]  = array('time','<=',strtotime($dateTime[1]));
			}else{
				$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
				$where[]    = array('time','>=',$todayStart);
				$todayEnd   = mktime(23,59,59,date('m'),date('d'),date('Y'));
				$where[]    = array('time','<=',$todayEnd);
			}

			$count              = model('Loginlog')->where($where)->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'time';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			$data = model('Loginlog')->where($where)->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			foreach ($data as $key => &$value) {
				$value['time'] = date('Y-m-d H:i:s', $value['time']);
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
	 * 清楚缓存
	 * @return [type] [description]
	 */
	public function clear_cache_front(){
		Cache::clear();
		die('缓存已清空');
	}

}
