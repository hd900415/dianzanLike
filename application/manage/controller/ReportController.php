<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\controller;

/**
 * 编写：祝踏岚
 * 报表控制器
 */

use app\manage\controller\Common;

class ReportController extends CommonController{
	/**
	 * 空操作处理
	 */
	public function _empty(){
		return $this->lottery();
	}

	/**
	 * 彩种报表
	 */
	public function lottery(){
		$data = model('PlayClassTotal')->lotteryReport();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('lotteryList',$data['lotteryList']);

		return $this->fetch();
	}

	/**
	 * 游戏报表
	 */
	public function game(){
		$data = model('PlayGameTotal')->gameReport();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('lotteryList',$data['lotteryList']);

		return $this->fetch();
	}

	/**
	 * 全局统计
	 */
	public function counts(){
		$param = input('get.');
		$data = (isset($param['isUser']) && $param['isUser'] == 2) ? model('MerchantDaily')->counts() : model('UserDaily')->counts();

		$this->assign('gradeData',$data['gradeData']);
		$this->assign('todayStatis',$data['todayStatis']);
		$this->assign('dataTimeArray',$data['dataTimeArray']);
		// $this->assign('top10Array',$data['top10Array']);
		$this->assign('total',$data['total']);
		$this->assign('where',$param);

		return $this->fetch();
	}

	/**
	 * 每日报表
	 */
	public function data(){
		$param = input('get.');
		$data = (isset($param['isUser']) && $param['isUser'] == 2) ? model('MerchantDaily')->everyday() : model('UserDaily')->everyday();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('totalAll',$data['totalAll']);
		$this->assign('totalPage',$data['totalPage']);

		return $this->fetch();
	}

	/**
	 * 每期报表
	 */
	public function no(){
		$data = model('PlayNoTotal')->everyNo();

		$this->assign('noList',$data['noList']);
		$this->assign('lotteryList',$data['lotteryList']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);

		return $this->fetch();
	}

	/**
	 * 盈亏报表
	 */
	public function profit(){
		// 获取参数
		$param = input('get.');

		// 查询条件组装
		$pageUrl = "";
		// 分页参数组装
		// $pageParam = array();
		// 查询条件定义
		$where = array();
		// 时间搜索
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime  = explode(' - ', $param['datetime']);
			$startDate = strtotime($dateTime[0]);
			$endDate   = strtotime($dateTime[1]);
			$where[]   = array('date', '>=', $startDate);
			$where[]   = array('date', '<=', $endDate);
			$pageUrl   .= '&datetime='.$param['datetime'];
		} else {
			$startDate = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 86400 * 14;
			$endDate   = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$where[]   = array('date', '>=', $startDate);
			$where[]   = array('date', '<=', $endDate);
			$param['datetime'] = date('Y-m-d',$startDate).' - '.date('Y-m-d',$endDate);
		}

		$data = array();
		$day = ($endDate - $startDate)/86400;
		for ($i=0; $i <= $day; $i++) {
			// 会员
			$data[$i]['user'] = model('UserDaily')->field([
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
			])->where('date', $endDate)->findOrEmpty();
			// 商户
			$data[$i]['merchant'] = model('MerchantDaily')->field([
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
			])->where('date', $endDate)->findOrEmpty();
			// 盈亏 商户手续费 - 代理商户返点 - 会员收益返还
			$data[$i]['profitLoss']     = $data[$i]['merchant']['fee'] - $data[$i]['merchant']['rebate'] - $data[$i]['user']['giveback'];
			// 现金盈亏 会员买币 - 会员提币 - 商户提现
			$data[$i]['cashProfitLoss'] = $data[$i]['user']['buy'] - $data[$i]['user']['recovery'] - $data[$i]['merchant']['withdrawal'];

			$data[$i]['date'] = $endDate;
			$endDate -= 86400;
		}

		//全部合计
		$sumField = array('recharge','withdrawal','order','giveback','fee','commission','activity','recovery','rob','buy','sell','rebate');
		foreach ($sumField as $key => &$value) {
			$totalAll['user'][$value]     = 0;
			$totalAll['merchant'][$value] = 0;
			$totalAll['profitLoss']       = 0;
			$totalAll['cashProfitLoss']   = 0;
			foreach ($data as $k => $v) {
				$totalAll['user'][$value]     += $v['user'][$value];
				$totalAll['merchant'][$value] += $v['merchant'][$value];
				$totalAll['profitLoss']       += $v['profitLoss'];
				$totalAll['cashProfitLoss']   += $v['cashProfitLoss'];
			}
		}

		//分页
		$pageNum = isset($param['page']) && $param['page'] ? $param['page'] : 1 ;
		$pageInfo = model('ArrPage')->page($data,15,$pageNum,$pageUrl);
		$page = $pageInfo['links'];
		$source = $pageInfo['source'];

		//本页总计
		foreach ($sumField as $key => &$value) {
			$totalPage['user'][$value]     = 0;
			$totalPage['merchant'][$value] = 0;
			$totalPage['profitLoss']       = 0;
			$totalPage['cashProfitLoss']   = 0;
			foreach ($source as $k => $v) {
				$totalPage['user'][$value]     += $v['user'][$value];
				$totalPage['merchant'][$value] += $v['merchant'][$value];
				$totalPage['profitLoss']       += $v['profitLoss'];
				$totalPage['cashProfitLoss']   += $v['cashProfitLoss'];
			}
		}

		return view('', [
			'data'		=>	$source,
			'page'		=>	$page,
			'where'		=>	$param,
			'totalAll'	=>	$totalAll,
			'totalPage'	=>	$totalPage,
		]);
	}

	/**
	 * 排行报表
	 */
	public function ranking(){
		$data = model('UserDaily')->rank();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);

		return $this->fetch();
	}

	/**
	 * 团队报表
	 */
	public function team_statistic(){
		$param = input('get.');
		$data = (isset($param['isUser']) && $param['isUser'] == 2) ? model('Merchant')->teamStatistic() : model('Users')->teamStatistic();

		$this->assign('data',$data['data']);
		$this->assign('total',$data['total']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);

		return $this->fetch();
	}

	/**
	 * 团队销量
	 */
	public function team_sales(){
		$data = model('UserDaily')->teamSales();

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);

		return $this->fetch();
	}

	/**
	 * 体育每日
	 */
	public function sportDaily(){
		$data = model('UserDailyThird')->thirdDaily('sports');

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('totalAll',$data['totalAll']);
		$this->assign('totalPage',$data['totalPage']);

		return $this->fetch();
	}

	/**
	 * 体育团队
	 */
	public function sportTeam(){
		$data = model('Users')->thirdTeam('sport');

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);

		return $this->fetch();
	}

	/**
	 * 真人每日
	 */
	public function personDaily(){
		$data = model('UserDailyThird')->thirdDaily('person');

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('totalAll',$data['totalAll']);
		$this->assign('totalPage',$data['totalPage']);

		return $this->fetch();
	}

	/**
	 * 真人团队
	 */
	public function personTeam(){
		$data = model('Users')->thirdTeam('person');

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);

		return $this->fetch();
	}

	/**
	 * 棋牌每日
	 */
	public function chessDaily(){
		$data = model('UserDailyThird')->thirdDaily('chess');

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('totalAll',$data['totalAll']);
		$this->assign('totalPage',$data['totalPage']);

		return $this->fetch();
	}

	/**
	 * 棋牌团队
	 */
	public function chessTeam(){
		$data = model('Users')->thirdTeam('chess');

		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);

		return $this->fetch();
	}

	/**
	 * 锁定团队
	 * @return [type] [description]
	 */
	public function lockTeam(){
		if (!request()->isAjax()) return '提交失败';
		$param = input('param.');
		if (!$param) return '提交失败';

		$res = model('Users')->join('user_team','ly_users.id=user_team.team')->where('user_team.uid', $param['id'])->setField('state', $param['value']);
		if (!$res) return '操作失败';

		return 1;
	}
}