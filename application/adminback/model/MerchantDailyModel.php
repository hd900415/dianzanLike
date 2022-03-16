<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\model;

use think\Model;

class MerchantDailyModel extends Model{
	//表名
	protected $table = 'ly_merchant_daily';

	public function everyday(){
		// 获取参数
		$param = input('get.');
		// 查询条件组装
		$pageUrl = "";
		// 分页参数组装
		// $pageParam = array();
		// 查询条件定义
		$where = array();
		// 用户名
		if(isset($param['username']) && $param['username']){
			$merInfo = model('Merchant')
					->field('ly_merchant.id')
					->join('merchant_team', 'ly_merchant.id = merchant_team.team')
					->where(array(
						['merchant_team.uid','=',session('admin_userid')],
						['ly_merchant.username','=',trim($param['username'])]
					))->find();

			if (!$merInfo) return [];
			$uid = $merInfo['id'];

			$pageUrl .= '&username='.$param['username'];
		} else {
			$uid = session('admin_userid');
		}
		// 时间搜索
		if(isset($param['date_range']) && $param['date_range']){
			$dateTime  = explode(' - ', $param['date_range']);
			$startDate = strtotime($dateTime[0]);
			$endDate   = strtotime($dateTime[1]);
			$pageUrl   .= '&date_range='.$param['date_range'];
		} else {
			$startDate = mktime(0,0,0,date('m'),date('d'),date('Y')) - 86400 * 14;
			$endDate   = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$param['date_range'] = date('Y-m-d',$startDate).' - '.date('Y-m-d',$endDate);
		}

		// 字段判定
		$profitField = (session('admin_types') == 2) ? 'fee' : 'rebate';

		$i = 0;
		do {			
			if (session('admin_types') == 1) {
				// 商户注册数
				$data[$i]['regNum'] = model('Merchant')->join('merchant_team','ly_merchant.id = merchant_team.team')->where(array(['merchant_team.uid','=',$uid],['merchant_team.team','<>',$uid],['ly_merchant.reg_time','>=',$endDate],['ly_merchant.reg_time','<=',$endDate+86399],['types','=',2]))->count();
			}
			// 有效订单数
			$data[$i]['orderNum'] = model('Order')->join('merchant_team','ly_order.mid = merchant_team.team')->where(array(['merchant_team.uid','=',$uid],['ly_order.ordertimes','>=',$endDate],['ly_order.ordertimes','<=',$endDate+86399],['ly_order.uid','<>',0]))->count();

			if (session('admin_types') == 2) {		
				// 总订单数
				$data[$i]['orderNumAll'] = model('Order')->join('merchant_team','ly_order.mid = merchant_team.team')->where(array(['merchant_team.uid','=',$uid],['ly_order.ordertimes','>=',$endDate],['ly_order.ordertimes','<=',$endDate+86399]))->count();
				// 已成交数量
				$data[$i]['orderSuccess'] = model('Order')->join('merchant_team','ly_order.mid = merchant_team.team')->where(array(['merchant_team.uid','=',$uid],['ly_order.ordertimes','>=',$endDate],['ly_order.ordertimes','<=',$endDate+86399]))->whereIn('ly_order.status',[2,3])->count();
				// 成功率
				$data[$i]['c_percent'] = ($data[$i]['orderNum']) ? round($data[$i]['orderSuccess'] / $data[$i]['orderNum'] * 100, 2) : 0;
			}			
			// 总流水
			$data[$i]['details'] = model('MerchantDaily')->join('merchant_team','ly_merchant_daily.uid = merchant_team.team')->where(array(['merchant_team.uid','=',$uid],['ly_merchant_daily.date','=',$endDate]))->sum('order');
			// 个人收益/手续费
			$data[$i]['profit'] = $this->where(array(['date','>=',$endDate],['date','<=',$endDate+86399],['uid','=',$uid]))->value($profitField);			
			
			$data[$i]['date'] = $endDate;
			$endDate -= 86400;
			$i++;

		} while ($endDate >= $startDate);

		//全部合计
		$sumField = (session('admin_types') == 1) ? array('regNum','orderNum','details','profit') : array('orderNumAll','orderNum','details','profit');
		foreach ($sumField as $key => &$value) {
			$totalAll[$value] = 0;
			foreach ($data as $k => $v) {
				$totalAll[$value] += $v[$value];
			}			
		}
		if (session('admin_types') == 2) {
			$totalAll['c_percent'] = ($totalAll['orderNumAll']) ? round($totalAll['orderNum'] / $totalAll['orderNumAll'] * 100, 2) : 0;
		}

		//分页
		$pageNum = isset($param['page']) && $param['page'] ? $param['page'] : 1 ;
		$pageInfo = model('ArrPage')->page($data, 15, $pageNum, $pageUrl);
		$page = $pageInfo['links'];
		$source = $pageInfo['source'];
		
		//本页总计
		foreach ($sumField as $key => &$value) {
			$totalPage[$value] = 0;
			foreach ($source as $k => $v) {
				$totalPage[$value] += $v[$value];
			}
		}
		if (session('admin_types') == 2) {
			$totalPage['c_percent'] = ($totalPage['orderNumAll']) ? round($totalPage['orderNum'] / $totalPage['orderNumAll'] * 100, 2) : 0;
		}

		return array(
			'data'      => $source,
			'page'      => $page,
			'where'     => $param,
			'totalAll'  => $totalAll,
			'totalPage' => $totalPage,
			'adminType' => session('admin_types'),
		);
	}
}