<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 对每日报表的相关操作
 */

namespace app\manage\model;

// use think\Model;

use think\Cache;
use GatewayClient\Gateway;

use app\common\model\UserDailyModel as U;

class UserDailyModel extends U{
	//表名
	protected $table = 'ly_user_daily';

	/**
	 * 二维数组排序
	 * @param  array $multi_array 待排序数组
	 * @param  string $sort_key    排序字段
	 * @param  string $sort        排序类型
	 * @return array              排序后数组
	 */
	public function multi_array_sort($multi_array,$sort_key,$sort=SORT_DESC){
		if(is_array($multi_array) && $multi_array){
			foreach ($multi_array as $row_array){
				if(is_array($row_array)){
					$key_array[] = $row_array[$sort_key];
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
		array_multisort($key_array,$sort,$multi_array);
		return $multi_array;
	}

	/**
	 * 全局统计
	 */
	public function counts(){
		$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$dataTime = array(
			//当天
			// 'today'	=>	'date >= '.$todayStart.' and date <= '.mktime(23,59,59,date('m'),date('d'),date('Y')),
			'today'	=>	array(
				['user_type','<>',3],
				['date','>=',$todayStart],
				['date','<=',mktime(23,59,59,date('m'),date('d'),date('Y'))],
			),
			//昨天
			// 'yesterday'	=>	'date >= '.($todayStart-86400).' and date < '.$todayStart,
			'yesterday'	=>	array(
				['user_type','<>',3],
				['date','>=',$todayStart-86400],
				['date','<',$todayStart],
			),
			//本周
			// 'week'	=>	'date >= '.($todayStart-date('N')*86400).' and date < '.($todayStart+(7-date('N'))*86400),
			'week'	=>	array(
				['user_type','<>',3],
				['date','>=',$todayStart-date('N')*86400],
				['date','<',$todayStart+(7-date('N'))*86400],
			),
			//本月
			// 'month'	=>	'date >= '.mktime(0,0,0,date('m'),1,date('Y')).' and date <= '.mktime(23,59,59,date('m'),date('t'),date('Y')),
			'month'	=>	array(
				['user_type','<>',3],
				['date','>=',mktime(0,0,0,date('m'),1,date('Y'))],
				['date','<=',mktime(23,59,59,date('m'),date('t'),date('Y'))],
			),
		);
		/**
		 * 今日数据
		 */
		$todayUserTaskList = model('UserDaily')->alias('ud')
							->field([
								'reg_time',
								'l_t_o_n',
								'w_t_o_n',
								's_t_o_n',
							])
							->join('users','ud.uid=users.id')
							->whereTime('ud.date', 'between', [$todayStart, $todayStart+86399])
							->select()->toArray();

		$todayStatis['todayCount']    = 0; // 今日做任务总人数
		$todayStatis['todayRegCount'] = 0; // 今日新注册会员做任务人数
		$todayStatis['todayLed']      = 0; // 今日任务领取
		$todayStatis['todaySuccess']  = 0; // 今日任务成功
		$todayStatis['todayFail']     = 0; // 今日任务失败

		foreach ($todayUserTaskList as $key => $value) {

			if ($value['reg_time'] >= $todayStart) $todayStatis['todayRegCount']++;
			// 領取
			if ($value['l_t_o_n']) {
				$todayStatis['todayCount']++;
				$todayStatis['todayLed'] += $value['l_t_o_n'];
			}
			// 完成
			if ($value['w_t_o_n']) {
				$todayStatis['todayCount']++;
				$todayStatis['todaySuccess'] += $value['w_t_o_n'];
			}
			// 失敗
			if ($value['s_t_o_n']) {
				$todayStatis['todayCount']++;
				$todayStatis['todayFail'] += $value['s_t_o_n'];
			}
		}

		//获取时间段内数据
		$dataTimeArray = array();
		$decimalPlace = config('manage.decimalPlace');	// 获取小数保留位数
		foreach ($dataTime as $key => $value) {
			$dataTimeArray[$key] = $this->field([
				'SUM(`recharge`)'   => 'recharge',
				'SUM(`withdrawal`)' => 'withdrawal',
				'SUM(`task`)'       => 'task',
				'SUM(`rebate`)'     => 'rebate',
				'SUM(`regment`)'    => 'regment',
				'SUM(`other`)'      => 'other',
				'SUM(`buymembers`)' => 'buymembers',
				'SUM(`spread`)'     => 'spread',
				'SUM(`pump`)'       => 'pump',
				'SUM(`revoke`)'     => 'revoke',
				'SUM(`commission`)' => 'commission',
			])->where($value)->find()->toArray();
			// 小数位数
			foreach ($dataTimeArray as $key2 => &$value2) {
				foreach ($value2 as $k => &$v) if(is_numeric($v)) $v = round($v, $decimalPlace);
			}
		}

		/**
		 * TOP10(当日有数据的用户)
		 */
		// $top10Array = $this->field('ly_user_daily.*,user_total.balance')->join('user_total','ly_user_daily.uid=user_total.uid')->where('date',$todayStart)->order('task','desc')->limit(10)->select()->toArray();
		// $todayUserArray = $this->multi_array_sort($todayUser->toArray(),'order');
		// if ($top10Array) {
			// $top10Array = array_slice($todayUserArray,0,10);
		// 	foreach ($top10Array as $key => &$value) {
		// 		foreach ($top10Array as $key2 => &$value2) {
		// 			foreach ($value2 as $k => &$v) if(is_numeric($v)) $v = round($v, $decimalPlace);
		// 		}
		// 		$value['rank'] = '第'.($key+1).'名';
		// 	}
		// }

		//今日注册
		$total['todayReg']     = model('Users')->where(array(['reg_time','>=',$todayStart],['reg_time','<',$todayStart+86400],['user_type','neq','3']))->count();
		//昨日注册
		$total['yesterdayReg'] = model('Users')->where(array(['reg_time','>=',$todayStart-86400],['reg_time','<',$todayStart],['user_type','neq','3']))->count();
		//本月注册
		$total['monthReg']     = model('Users')->where(array(['reg_time','>=',mktime(0,0,0,date('m'),1,date('Y'))],['reg_time','<=',mktime(23,59,59,date('m'),date('t'),date('Y'))],['user_type','neq','3']))->count();
		//总人数
		$total['countUser']    = model('Users')->where('user_type','neq','3')->count();
		//余额
		$userBalance            = model('UserTotal')->field(['SUM(`balance`)'=>'balance','SUM(`total_balance`)'=>'total_balance'])->find();
		$total['balance']       = $userBalance['balance'];
		$total['total_balance'] = $userBalance['total_balance'];

		// 当前在线人数
		$total['online'] = 0;
		$userList = model('Users')->column('id');
		foreach ($userList as $key => $value) {
			if (cache('C_token_'.$value)) $total['online']++;
		}

		// 各VIP等级数据
		$gradeData['grade'] = model('UserGrade')->field('name,grade')->select()->toArray();
		foreach ($gradeData['grade'] as $key => &$value) {
			// 今日新增
			$value['gradeData']['todayAdd'] = model('UserVip')->where('grade', $value['grade'])->whereTime('stime', 'between', [$todayStart, $todayStart+86399])->count();
			// 总数
			$value['gradeData']['total'] = model('UserVip')->where('grade', $value['grade'])->whereTime('etime', '>=', time())->count();
		}
		$gradeData['total'] = model('UserVip')->whereTime('etime', '>=', time())->count();

		// 当前抢单人数
		/**
		 *====这个步骤是必须的====
		 *这里填写Register服务的ip和Register端口，注意端口不是gateway端口
		 *ip不能是0.0.0.0，端口在start_register.php中可以找到
		 *这里假设GatewayClient和Register服务都在一台服务器上，ip填写127.0.0.1。
		 *如果不在一台服务器则填写真实的Register服务的内网ip(或者外网ip)
		 **/
		// Gateway::$registerAddress = '127.0.0.1:1238';
		// $total['q_order'] = Gateway::getClientIdCountByGroup('sell');

		return array(
			'gradeData'     => $gradeData,
			'todayStatis'   => $todayStatis,
			'dataTimeArray' =>	$dataTimeArray,
			// 'top10Array'    =>	$top10Array,
			'total'         =>	$total
		);
	}

	/**
	 * 每日报表
	 */
	public function everyday(){
		// 获取参数
		$param = input('get.');

		// 查询条件组装
		$pageUrl = "";
		if (isset($param['isUser']) && $param['isUser']) $pageUrl .= "&isUser=".$param['isUser'];
		// 分页参数组装
		// $pageParam = array();
		// 查询条件定义
		$where = array();
		// 用户名
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','=',trim($param['username']));
			$pageUrl .= '&username='.$param['username'];
		}
		// 时间搜索
		if(isset($param['date_range']) && $param['date_range']){
			$dateTime  = explode(' - ', $param['date_range']);
			$startDate = strtotime($dateTime[0]);
			$endDate   = strtotime($dateTime[1]);
			$where[]   = array('date', '>=', $startDate);
			$where[]   = array('date', '<=', $endDate);
			$pageUrl   .= '&date_range='.$param['date_range'];
		} else {
			$startDate = mktime(0,0,0,date('m'),date('d'),date('Y')) - 86400 * 7;
			$endDate   = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[]   = array('date', '>=', $startDate);
			$where[]   = array('date', '<=', $endDate);
			$param['date_range'] = date('Y-m-d',$startDate).' - '.date('Y-m-d',$endDate);
		}

		if (isset($param['username']) && $param['username']) {
			$data = $this->where($where)->order('date','desc')->select()->toArray();
		} else {
			$data = array();
			$day = ($endDate - $startDate)/86400;
			for ($i=0; $i <= $day; $i++) {
				$data[$i] = $this->field([
					'SUM(`recharge`)'   => 'recharge',
					'SUM(`withdrawal`)' => 'withdrawal',
					'SUM(`task`)'       => 'task',
					'SUM(`rebate`)'     => 'rebate',
					'SUM(`regment`)'    => 'regment',
					'SUM(`other`)'      => 'other',
					'SUM(`buymembers`)' => 'buymembers',
					'SUM(`spread`)'     => 'spread',
					'SUM(`pump`)'       => 'pump',
					'SUM(`revoke`)'     => 'revoke',
					'SUM(`commission`)' => 'commission',
				])->where('date', $endDate)->find()->toArray();

				$data[$i]['date'] = $endDate;
				$endDate -= 86400;
			}
		}

		$decimalPlace = config('manage.decimalPlace');	// 获取小数保留位数
		foreach ($data as $key => &$value) {
			// 小数位数
			foreach ($value as $k => $v) {
				if ($k != 'date') $data[$key][$k] = round($v, $decimalPlace);
			}
		}

		//全部合计
		$sumField = ['recharge','withdrawal','task','rebate','regment','other','buymembers','spread','pump','revoke','commission'];
		foreach ($sumField as $key => &$value) {
			$totalAll[$value] = 0;
			foreach ($data as $k => $v) {
				$totalAll[$value] += $v[$value];
			}
		}

		//分页
		$pageNum = isset($param['page']) && $param['page'] ? $param['page'] : 1 ;
		$pageInfo = model('ArrPage')->page($data,15,$pageNum,$pageUrl);
		$page = $pageInfo['links'];
		$source = $pageInfo['source'];

		//本页总计
		foreach ($sumField as $key => &$value) {
			$totalPage[$value] = 0;
			foreach ($source as $k => $v) {
				$totalPage[$value] += $v[$value];
			}
		}

		return array(
			'data'		=>	$source,
			'page'		=>	$page,
			'where'		=>	$param,
			'totalAll'	=>	$totalAll,
			'totalPage'	=>	$totalPage,
		);
	}

	/**
	 * 盈亏报表
	 */
	public function profit(){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where[] = array('user_type', 'neq', 3);
		//分页参数组装
		$pageParam = array();

		//用户名
		if(isset($param['username']) && $param['username']){
			$where[] = array('username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		//开始时间
		if(isset($param['startdate']) && $param['startdate']){
			$startDate = strtotime($param['startdate']);
			$where[] = array('date','>=',$startDate);
			$pageParam['startdate'] = $param['startdate'];
		}else{
			$startDate = mktime(0,0,0,date('m'),date('d'),date('Y')) - 86400;
			$where[] = array('date','>=',$startDate);
			$param['startdate'] = date('Y-m-d',$startDate);
		}
		//结束时间
		if(isset($param['enddate']) && $param['enddate']){
			$endDate = strtotime($param['enddate']);
			$where[] = array('date','<=',$endDate);
			$pageParam['enddate'] = $param['enddate'];
		}else{
			$endDate = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('date','<=',$endDate);
			$param['enddate'] = date('Y-m-d',$endDate);
		}

		//查询符合条件的数据
		$resultData = $this->where($where)->order(['date'=>'desc','id'=>'desc'])->paginate(16,false,['query'=>$pageParam]);
		//数据集转数组
		$profitList = $resultData->toArray()['data'];
		//部分元素重新赋值
		$decimalPlace = config('manage.decimalPlace');	// 获取小数保留位数
		foreach ($profitList as $key => &$value) {
			foreach ($value as $k => &$v) if(is_numeric($v)) $v = round($v, $decimalPlace);
			$value['loss'] = $value['order'] - $value['rebate'] - $value['activity']/*- $value['bonus']*/;
			$value['loss'] = round($value['loss'],$decimalPlace);
		}

		return array(
			'profitList'	=>	$profitList,
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$pageParam,
		);
	}

	/**
	 * 排行报表
	 */
	public function rank(){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where[] = $whereUser[] = array('user_type','<>','3');
		// $where = array();
		$whereStr = 'user_type != 3 and ';
		//分页参数组装
		$pageUrl = '?page={page}';

		//用户名
		if(isset($param['username']) && $param['username']){
			$whereUser[] = array('username','=',trim($param['username']));
			$pageUrl .= '&username='.$param['username'];
		}
		// 时间
		if(isset($param['datetime_range']) && $param['datetime_range']){
			$dateTime = explode(' - ', $param['datetime_range']);
			$where[] = array('date','>=',strtotime($dateTime[0]));
			$where[] = array('date','<=',strtotime($dateTime[1]));
			$pageUrl .= '&datetime_range='.$param['datetime_range'];
		}

		//获取所有用户
		$resultData = model('Users')->field('ly_users.id,username,user_total.balance')->join('user_total','ly_users.id=user_total.uid','left')->where($whereUser)->order('id','asc')->cursor();
		foreach ($resultData as $key => $value) {
			$userInfo[] = $value->toArray();
		}

		$reportArray = array();
		$decimalPlace = config('manage.decimalPlace');	// 获取小数保留位数
		foreach ($userInfo as $key => $value) {
			//获取用户时间段内各项数据
			$reportArray[$key] = $this->field([
				'SUM(`recharge`)'   => 'recharge',
				'SUM(`withdrawal`)' => 'withdrawal',
				'SUM(`order`)'      => 'order',
				'SUM(`payment`)'    => 'payment',
				'SUM(`repayment`)'  => 'repayment',
				'SUM(`regment`)'    => 'regment',
				'SUM(`popment`)'    => 'popment',
				'SUM(`other`)'      => 'other',
			])->where('uid', $value['id'])->where($where)->find()->toArray();

			foreach ($reportArray[$key] as $k => &$v) if(is_numeric($v)) $v = round($v, $decimalPlace);

			$reportArray[$key]['loss'] = round($reportArray[$key]['order'] - $reportArray[$key]['rebate'] - $reportArray[$key]['activity']/*- $reportArray[$key]['bonus']*/,$decimalPlace);
			$reportArray[$key]['username'] = $value['username'];
			$reportArray[$key]['balance'] = round($value['balance'], $decimalPlace);
		}
		//过滤零结算用户
		if(isset($param['filter']) && $param['filter']){
			$pageUrl .= '&filter='.$param['filter'];
			foreach ($reportArray as $key => $value) {
				$filterArray = $value;
				unset($filterArray['username']);
				unset($filterArray['balance']);
				if(!array_filter($filterArray)) unset($reportArray[$key]);
			}
		}
		if(!$reportArray) return array(
			'data'		=>	array(),
			'page'		=>	'',
			'where'		=>	$param,
		);
		// 排序字段
		$param['orderby'] = (isset($param['orderby']) && $param['orderby']) ? $param['orderby'] : 'betting';
		$pageUrl .= '&orderby='.$param['orderby'];
		// 数组排序
		$data = $this->multi_array_sort($reportArray,$param['orderby']);
		//分页
		$pageNum = isset($param['page']) && $param['page'] ? $param['page'] : 1 ;
		$pageInfo = model('ArrPage')->page($data,16,$pageNum,$pageUrl);
		$page = $pageInfo['links'];
		$source = $pageInfo['source'];

		return array(
			'data'		=>	$source,
			'page'		=>	$page,
			'where'		=>	$param,
		);
	}

	/**
	 * 团队报表
	 */
	public function teamStatistic($userList,$startDate,$endDate,$sid,$filter=false){
		$array = array();
		// where
		// $where[] = ['user_type','<>',3];
		$where[] = ['date','>=',$startDate];
		$where[] = ['date','<=',$endDate];

		foreach ($userList as $key => &$value) {
			//数据获取
			if($value['id']==$sid){
				$array[$value['id']] = $this->field([
					'SUM(`recharge`)'   => 'recharge',
					'SUM(`withdrawal`)' => 'withdrawal',
					'SUM(`task`)'       => 'task',
					'SUM(`rebate`)'     => 'rebate',
					'SUM(`regment`)'    => 'regment',
					'SUM(`other`)'      => 'other',
					'SUM(`buymembers`)' => 'buymembers',
					'SUM(`spread`)'     => 'spread',
					'SUM(`pump`)'       => 'pump',
					'SUM(`revoke`)'     => 'revoke',
					'SUM(`commission`)' => 'commission',
				])->where('uid', $value['id'])->where($where)->findOrEmpty();
			}else{
				$array[$value['id']] = $this->field([
					'SUM(`recharge`)'   => 'recharge',
					'SUM(`withdrawal`)' => 'withdrawal',
					'SUM(`task`)'       => 'task',
					'SUM(`rebate`)'     => 'rebate',
					'SUM(`regment`)'    => 'regment',
					'SUM(`other`)'      => 'other',
					'SUM(`buymembers`)' => 'buymembers',
					'SUM(`spread`)'     => 'spread',
					'SUM(`pump`)'       => 'pump',
					'SUM(`revoke`)'     => 'revoke',
					'SUM(`commission`)' => 'commission',
				])->join('user_team', 'ly_user_daily.uid = user_team.team')->where('user_team.uid', $value['id'])->where($where)->findOrEmpty();
			}
			if (is_object($array[$value['id']])) $array[$value['id']] = $array[$value['id']]->toArray();
			// 发布任务数量
			$array[$value['id']]['relTaskNumber']  = model('UserTeam')->alias('ut')->join('task','ut.team=task.uid')->where('ut.uid','=',$value['id'])->whereIn('status', [3,4])->whereTime('add_time', 'between', [$startDate, $endDate])->count();
			// 接手任务数量
			$array[$value['id']]['takeTaskNumber'] = model('UserTeam')->alias('ut')->join('user_task','ut.team=user_task.uid')->where('ut.uid','=',$value['id'])->whereTime('add_time', 'between', [$startDate, $endDate])->count();

			if ($filter && array_filter($array[$value['id']])) {
				unset($array[$value['id']]);
				continue;
			}

			$array[$value['id']]['id']        = $value['id'];
			$array[$value['id']]['sid']       = $value['sid'];
			$array[$value['id']]['username']  = $value['username'];
			$array[$value['id']]['lock']      = $value['state'];
			// 团队总人数
			$array[$value['id']]['teamCount'] = model('UserTeam')->where('uid', $value['id'])->count();
		}
		// 总计
		$sumField = ['recharge','withdrawal','task','rebate','regment','other','buymembers','spread','pump','revoke','commission'];
		foreach ($sumField as $key => &$value) {
			$array['totalAll'][$value] = 0;
			foreach ($array as $k => $v) {
				$array['totalAll'][$value] += $v[$value];
			}
		}

		return $array;
	}

	/**
	 * 团队销量
	 */
	public function teamSales(){
		//获取参数
		$param = input('get.');
		//查询条件组装
		$where[] = $whereUser[] = array('user_type','<>','3');
		//分页参数组装
		$pageParam = array();

		//用户名
		if(isset($param['username']) && $param['username']){
			$whereUser[] = array('username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		//开始时间
		if(isset($param['startdate']) && $param['startdate']){
			$startDate = strtotime($param['startdate']);
			$where[] = array('date','>=',$startDate);
			//$where .= 'date >= '.$startDate;
			$pageParam['startdate'] = $param['startdate'];
		}else{
			$startDate = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('date','>=',$startDate);
			//$where .= 'date >= '.$startDate;
		}
		//结束时间
		if(isset($param['enddate']) && $param['enddate']){
			$endDate = strtotime($param['enddate']);
			$where[] = array('date','<=',$endDate);
			//$where .= ' and date <= '.$endDate;
			$pageParam['enddate'] = $param['enddate'];
		}else{
			$endDate = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('date','<=',$endDate);
			//$where .= ' and date <= '.$endDate;
		}

		//获取所有用户
		$resultData = model('Users')->field('id,username')->where($whereUser)->paginate(16,false,['query'=>$pageParam]);
		$userList = $resultData->toArray()['data'];

		$dataArr = array();
		foreach ($userList as $key => $value) {
			$dataArr[$key]['username'] = $value['username'];
			$where[] = array('user_team.uid','=',$value['id']);
			$dataTemp = $this->field(['SUM(betting)'=>'betting','SUM(betting_xy28)'=>'betting_xy28'])->join('user_team', 'ly_user_daily.uid = user_team.team')->where($where)->find()->toArray();
			$dataArr[$key]['betting'] = $dataTemp['betting'] + $dataTemp['betting_xy28'];
		}

		$data = ($dataArr) ? $this->multi_array_sort($dataArr, 'betting') : [];

		return array(
			'data'		=>	$data,
			'page'		=>	$resultData->render(),//分页
			'where'		=>	$pageParam,
		);
	}
}