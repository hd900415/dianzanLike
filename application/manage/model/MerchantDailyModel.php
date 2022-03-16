<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 对每日报表的相关操作
 */

namespace app\manage\model;

use think\Model;
use think\Cache;

class MerchantDailyModel extends Model{
	//表名
	protected $table = 'ly_merchant_daily';

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
		//获取时间段内数据
		$dataTimeArray = array();		
		$decimalPlace = config('manage.decimalPlace');	// 获取小数保留位数
		foreach ($dataTime as $key => $value) {
			$dataTimeArray[$key] = $this->field([
				'SUM(`recharge`)'   => 'recharge',
				'SUM(`withdrawal`)' => 'withdrawal',
				'SUM(`order`)'      => 'order',
				'SUM(`giveback`)'   => 'giveback',
				'SUM(`fee`)'        => 'fee',
				'SUM(`commission`)' => 'commission',
				'SUM(`activity`)'   => 'activity',
				'SUM(`recovery`)'   => 'recovery',
				'SUM(`rob`)'        => 'rob',
				'SUM(`buy`)'        => 'buy',
				'SUM(`sell`)'       => 'sell',
				'SUM(`rebate`)'     => 'rebate',
			])->where($value)->find()->toArray();
			// 小数位数
			foreach ($dataTimeArray as $key2 => &$value2) {
				foreach ($value2 as $k => &$v) if(is_numeric($v)) $v = round($v, $decimalPlace);
			}
		}
		
		//TOP10(当日有数据的用户)
		$top10Array = $this->field('ly_merchant_daily.*,merchant_total.balance')->join('merchant_total','ly_merchant_daily.uid=merchant_total.uid')->where('date',$todayStart)->order('order','desc')->limit(10)->select()->toArray();
		// $todayUserArray = $this->multi_array_sort($todayUser->toArray(),'order');
		if ($top10Array) {
			// $top10Array = array_slice($todayUserArray,0,10);
			foreach ($top10Array as $key => &$value) {
				foreach ($top10Array as $key2 => &$value2) {
					foreach ($value2 as $k => &$v) if(is_numeric($v)) $v = round($v, $decimalPlace);
				}
				$value['rank'] = '第'.($key+1).'名';
			}
		}

		//今日注册
		$total['todayReg']     = model('Merchant')->where(array(['reg_time','>=',$todayStart],['reg_time','<',$todayStart+86400]))->count();
		//昨日注册
		$total['yesterdayReg'] = model('Merchant')->where(array(['reg_time','>=',$todayStart-86400],['reg_time','<',$todayStart]))->count();
		//本月注册
		$total['monthReg']     = model('Merchant')->where(array(['reg_time','>=',mktime(0,0,0,date('m'),1,date('Y'))],['reg_time','<=',mktime(23,59,59,date('m'),date('t'),date('Y'))]))->count();
		//总人数
		$total['countUser'] = model('Merchant')->count();
		//余额
		$total['balance']   = model('MerchantTotal')->sum('balance');
		// 总订单数
		$total['order']     = model('Order')->where(array(['ordertimes','>=',$todayStart],['ordertimes','<=',$todayStart+86399]))->count();
		// 未接单的数量
		$total['w_order']   = model('Order')->where(array(['ordertimes','>=',$todayStart],['ordertimes','<=',$todayStart+86399],['uid','=',''],['status','<>',4]))->count();
		// 已成交数量
		$total['c_order']   = model('Order')->where(array(['ordertimes','>=',$todayStart],['ordertimes','<=',$todayStart+86399]))->whereIn('status',[2,3])->count();
		// 已接单的数量
		$total['j_order']   = $total['order'] - $total['w_order'];
		// 接单率
		$total['j_percent'] = ($total['order']) ? round($total['j_order'] / $total['order'] * 100, 2) : 0;
		// 成交率
		$total['c_percent'] = ($total['j_order']) ? round($total['c_order'] / $total['j_order'] * 100, 2) : 0;

		return array(
			'dataTimeArray'		=>	$dataTimeArray,
			'top10Array'		=>	$top10Array,
			'total'				=>	$total
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
		$where2 = array();
		// 用户名
		if(isset($param['username']) && $param['username']){
			$where[]  = array('username','=',trim($param['username']));
			$where2[]  = array('merchant.username','=',trim($param['username']));
			$pageUrl  .= '&username='.$param['username'];
		}
		// 商户类型
		if(isset($param['types']) && $param['types']){
			$where[]  = array('user_type','=',$param['types']);
			$where2[] = array('merchant.types','>=',$param['types']);
			$pageUrl  .= '&types='.$param['types'];
		}
		// 时间搜索
		if(isset($param['date_range']) && $param['date_range']){
			$dateTime  = explode(' - ', $param['date_range']);
			$startDate = strtotime($dateTime[0]);
			$endDate   = strtotime($dateTime[1]);
			$pageUrl   .= '&date_range='.$param['date_range'];
		} else {
			$startDate = mktime(0,0,0,date('m'),date('d'),date('Y')) - 86400 * 7;
			$endDate   = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$param['date_range'] = date('Y-m-d',$startDate).' - '.date('Y-m-d',$endDate);
		}

		$i = 0;
		do {			
			$data[$i] = $this->field([
				'SUM(`recharge`)'   => 'recharge',
				'SUM(`withdrawal`)' => 'withdrawal',
				'SUM(`order`)'      => 'order',
				'SUM(`giveback`)'   => 'giveback',
				'SUM(`fee`)'        => 'fee',
				'SUM(`commission`)' => 'commission',
				'SUM(`activity`)'   => 'activity',
				'SUM(`recovery`)'   => 'recovery',
				'SUM(`rob`)'        => 'rob',
				'SUM(`buy`)'        => 'buy',
				'SUM(`sell`)'       => 'sell',
				'SUM(`rebate`)'     => 'rebate',
			])->where($where)->where('date', $endDate)->find()->toArray();

			// 订单总量
			$data[$i]['sumOrder']  = model('Order')->join('merchant','ly_order.mid=merchant.id')->where($where2)->where(array(['ly_order.ordertimes','>=',$endDate],['ly_order.ordertimes','<=',$endDate+86399]))->count();
			// 未接单数量
			$data[$i]['w_order']   = model('Order')->join('merchant','ly_order.mid=merchant.id')->where($where2)->where(array(['ly_order.ordertimes','>=',$endDate],['ly_order.ordertimes','<=',$endDate+86399],['ly_order.uid','=',0]))->count();
			// 已成交数量
			$data[$i]['c_order']   = model('Order')->join('merchant','ly_order.mid=merchant.id')->where($where2)->where(array(['ly_order.ordertimes','>=',$endDate],['ly_order.ordertimes','<=',$endDate+86399]))->whereIn('ly_order.status',[2,3])->count();
			// 已接单的数量				
			$data[$i]['j_order']   = $data[$i]['sumOrder'] - $data[$i]['w_order'];
			// 接单率
			$data[$i]['j_percent'] = ($data[$i]['sumOrder']) ? round($data[$i]['j_order'] / $data[$i]['sumOrder'] * 100, 2) : 0;
			// 成交率	
			$data[$i]['c_percent'] = ($data[$i]['j_order']) ? round($data[$i]['c_order'] / $data[$i]['j_order'] * 100, 2) : 0;

			$data[$i]['date'] = $endDate;
			$endDate -= 86400;
			$i++;

		} while ($endDate >= $startDate);
		
		//全部合计
		$sumField = array('recharge','withdrawal','order','giveback','fee','commission','activity','recovery','rob','buy','sell','rebate','j_order','w_order','c_order','sumOrder');
		foreach ($sumField as $key => &$value) {
			$totalAll[$value] = 0;
			foreach ($data as $k => $v) {
				$totalAll[$value] += $v[$value];
			}
		}
		$totalAll['j_percent'] = ($totalAll['sumOrder']) ? round($totalAll['j_order'] / $totalAll['sumOrder'] * 100, 2) : 0;
		$totalAll['c_percent'] = ($totalAll['j_order']) ? round($totalAll['c_order'] / $totalAll['j_order'] * 100, 2) : 0;
		
		//分页
		$pageNum = isset($param['page']) && $param['page'] ? $param['page'] : 1 ;
		$pageInfo = model('ArrPage')->page($data,16,$pageNum,$pageUrl);
		$page = $pageInfo['links'];
		$source = $pageInfo['source'];
		
		//本页总计
		foreach ($sumField as $key => &$value) {
			$totalPage[$value] = 0;
			foreach ($source as $k => $v) {
				$totalPage[$value] += $v[$value];
			}
		}
		$totalPage['j_percent'] = ($totalPage['sumOrder']) ? round($totalPage['j_order'] / $totalPage['sumOrder'] * 100, 2) : 0;
		$totalPage['c_percent'] = ($totalPage['j_order']) ? round($totalPage['c_order'] / $totalPage['j_order'] * 100, 2) : 0;

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
			$value['loss'] = $value['betting'] + $value['betting_xy28'] - $value['winning'] - $value['winning_xy28'] - $value['rebate'] - $value['activity'] - $value['wage']/*- $value['bonus']*/;
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
		//开始时间
		if(isset($param['startdate']) && $param['startdate']){
			$startDate = strtotime($param['startdate']);
			$where[] = array('date','>=',$startDate);
			//$whereStr .= 'date >= '.$startDate.' and ';
			$pageUrl .= '&startdate='.$param['startdate'];
		}else{
			$startDate = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('date','>=',$startDate);
			//$whereStr .= 'date >= '.$startDate.' and ';
			$param['startdate'] = date('Y-m-d',$startDate);
		}
		//结束时间
		if(isset($param['enddate']) && $param['enddate']){
			$endDate = strtotime($param['enddate']);
			$where[] = array('date','<=',$endDate);
			//$whereStr .= 'date <= '.$endDate.' and ';
			$pageUrl .= '&enddate='.$param['enddate'];
		}else{
			$endDate = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('date','<=',$endDate);
			//$whereStr .= 'date <= '.$endDate.' and ';
			$param['enddate'] = date('Y-m-d',$endDate);
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
				'SUM(recharge)'          => 'recharge',
				'SUM(withdrawals)'       => 'withdrawals',
				'SUM(betting)'           => 'betting',
				'SUM(betting_xy28)'      => 'betting_xy28',
				'SUM(winning)'           => 'winning',
				'SUM(winning_xy28)'      => 'winning_xy28',
				'SUM(hang_order)'        => 'hang_order',
				'SUM(hang_order_xy28)'   => 'hang_order_xy28',
				'SUM(cancel)'            => 'cancel',
				'SUM(rebate)'            => 'rebate',
				'SUM(transfer)'          => 'transfer',
				'SUM(transaction)'       => 'transaction',
				'SUM(bonus)'             => 'bonus',
				'SUM(activity)'          => 'activity',
				'SUM(wage)'              => 'wage',
				'SUM(other)'             => 'other',
				'SUM(chess_transaction)' => 'chess_transaction',
				'SUM(chess_transfer)'    => 'chess_transfer',
				'SUM(gowater)'           => 'gowater'
			])->where('uid', $value['id'])->where($where)->find()->toArray();
			
			foreach ($reportArray[$key] as $k => &$v) if(is_numeric($v)) $v = round($v, $decimalPlace);

			$reportArray[$key]['loss'] = round($reportArray[$key]['betting'] + $reportArray[$key]['betting_xy28'] - $reportArray[$key]['winning'] - $reportArray[$key]['winning_xy28'] - $reportArray[$key]['rebate'] - $reportArray[$key]['activity'] - $reportArray[$key]['wage']/*- $reportArray[$key]['bonus']*/,$decimalPlace);
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
	public function teamStatistic($userList,$startDate,$endDate,$sid){
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
					'SUM(`order`)'      => 'order',
					'SUM(`giveback`)'   => 'giveback',
					'SUM(`fee`)'        => 'fee',
					'SUM(`commission`)' => 'commission',
					'SUM(`activity`)'   => 'activity',
					'SUM(`recovery`)'   => 'recovery',
					'SUM(`rob`)'        => 'rob',
					'SUM(`buy`)'        => 'buy',
					'SUM(`sell`)'       => 'sell',
					'SUM(`rebate`)'     => 'rebate',
				])->where('uid', $value['id'])->where($where)->findOrEmpty();
			}else{
				$array[$value['id']] = $this->field([
					'SUM(`recharge`)'   => 'recharge',
					'SUM(`withdrawal`)' => 'withdrawal',
					'SUM(`order`)'      => 'order',
					'SUM(`giveback`)'   => 'giveback',
					'SUM(`fee`)'        => 'fee',
					'SUM(`commission`)' => 'commission',
					'SUM(`activity`)'   => 'activity',
					'SUM(`recovery`)'   => 'recovery',
					'SUM(`rob`)'        => 'rob',
					'SUM(`buy`)'        => 'buy',
					'SUM(`sell`)'       => 'sell',
					'SUM(`rebate`)'     => 'rebate',
				])->join('merchant_team', 'ly_merchant_daily.uid = merchant_team.team')->where('merchant_team.uid', $value['id'])->where($where)->findOrEmpty();
			}
			if (is_object($array[$value['id']])) $array[$value['id']] = $array[$value['id']]->toArray();
			$array[$value['id']]['sid'] = $value['sid'];
			$array[$value['id']]['username'] = $value['username'];
		}
		// 过滤零结算数据行
		foreach ($array as $key => $value) {
			$not_in = $value;
			unset($not_in['sid']);
			unset($not_in['username']);
			if(!array_filter($not_in)){
				// array_splice($allData, $key, 1);
				unset($array[$key]);
			}
		}
		// 总计
		$sumField = array('recharge','withdrawal','order','giveback','fee','commission','activity','recovery','rob','buy','sell','rebate');
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

	/**
	 * 商户每日
	 */
	public function merDaily(){
		// 获取参数
		$param = input('get.');

		// 查询条件组装
		$pageUrl = "";
		// 分页参数组装
		// $pageParam = array();
		// 查询条件定义
		$where = array();
		$where2 = array();
		// 用户名
		if(isset($param['username']) && $param['username']){
			$where[]  = array('username','=',trim($param['username']));
			$where2[] = array('merchant.username','=',trim($param['username']));
			$pageUrl  .= '&username='.$param['username'];
		}
		// 商户类型
		if(isset($param['types']) && $param['types']){
			$where[]  = array('user_type','=',$param['types']);
			$where2[] = array('merchant.types','>=',$param['types']);
			$pageUrl  .= '&types='.$param['types'];
		}
		// 时间搜索
		if(isset($param['date']) && $param['date']){
			$today   = strtotime($param['date']);
			$pageUrl .= '&date='.$param['date'];
		} else {
			$today         = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$param['date'] = date('Y-m-d',$today);
		}

		$where[]  = array('date','=',$today);
		$where2[] = array('ly_order.ordertimes','>=',$today);
		$where2[] = array('ly_order.ordertimes','<=',$today+86399);

		$data = $this->where($where)->select()->toArray();

		foreach ($data as $key => &$value) {
			// 订单总量
			$value['sumOrder']  = model('Order')->join('merchant','ly_order.mid=merchant.id')->where('mid', $value['uid'])->where($where2)->count();
			// 未接单数量
			$value['w_order']   = model('Order')->join('merchant','ly_order.mid=merchant.id')->where('mid', $value['uid'])->where($where2)->where('ly_order.uid','=',0)->count();
			// 已成交数量
			$value['c_order']   = model('Order')->join('merchant','ly_order.mid=merchant.id')->where('mid', $value['uid'])->where($where2)->whereIn('ly_order.status',[2,3])->count();
			// 已接单的数量				
			$value['j_order']   = $value['sumOrder'] - $value['w_order'];
			// 接单率
			$value['j_percent'] = ($value['sumOrder']) ? round($value['j_order'] / $value['sumOrder'] * 100, 2) : 0;
			// 成交率	
			$value['c_percent'] = ($value['j_order']) ? round($value['c_order'] / $value['j_order'] * 100, 2) : 0;
		}
		
		//全部合计
		$sumField = array('recharge','withdrawal','order','giveback','fee','commission','activity','recovery','rob','buy','sell','rebate','j_order','w_order','c_order','sumOrder');
		foreach ($sumField as $key => &$value) {
			$totalAll[$value] = 0;
			foreach ($data as $k => $v) {
				$totalAll[$value] += $v[$value];
			}
		}
		$totalAll['j_percent'] = ($totalAll['j_order']) ? round($totalAll['j_order'] / $totalAll['sumOrder'] * 100, 2) : 0;
		$totalAll['c_percent'] = ($totalAll['c_order']) ? round($totalAll['c_order'] / $totalAll['j_order'] * 100, 2) : 0;
		
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
		$totalPage['j_percent'] = ($totalPage['j_order']) ? round($totalPage['j_order'] / $totalPage['sumOrder'] * 100, 2) : 0;
		$totalPage['c_percent'] = ($totalPage['c_order']) ? round($totalPage['c_order'] / $totalPage['j_order'] * 100, 2) : 0;

		return array(
			'data'		=>	$source,
			'page'		=>	$page,
			'where'		=>	$param,
			'totalAll'	=>	$totalAll,
			'totalPage'	=>	$totalPage,
		);
	}

}