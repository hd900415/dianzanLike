<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 对流水的相关操作
 */

namespace app\manage\model;

use app\common\model\TradeDetailsModel as Td;

class TradeDetailsModel extends Td{
	//表名
	protected $table = 'ly_trade_details';

	/**
	 * 资金流水列表
	 */
	public function detailsList($types){
		$param = input('get.');

		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		$where[] = array('types','=',$types);

		if (isset($param['isUser'])) {
			$where[] = array('types','=',$param['isUser']);
			$pageParam['isUser'] = $param['isUser'];
		}
		//搜索类型
		if(isset($param['search_type']) && $param['search_type'] && isset($param['search_content']) && $param['search_content']){
			switch ($param['search_type']) {
				case 'remarks':
					$where[] = array('remarks','like','%'.trim($param['search_content']).'%');
					break;
				default:
					$where[] = array($param['search_type'],'=',trim($param['search_content']));
					break;
			}
			$pageParam['search_type'] = $param['search_type'];
			$pageParam['search_content'] = $param['search_content'];
		}
		//交易类型
		if(isset($param['trade_type']) && $param['trade_type']){
			$where[] = array('trade_type','=',$param['trade_type']);
			$pageParam['trade_type'] = $param['trade_type'];
		}
		//交易金额
		if(isset($param['price1']) && $param['price1']){
			$where[] = array('trade_amount','>=',$param['price1']);
			$pageParam['price1'] = $param['price1'];
		}
		//交易金额
		if(isset($param['price2']) && $param['price2']){
			$where[] = array('trade_amount','<=',$param['price2']);
			$pageParam['price2'] = $param['price2'];
		}
		//时间
		if(isset($param['datetime_range']) && $param['datetime_range']){
			$dateTime = explode(' - ', $param['datetime_range']);
			$where[] = array('trade_time','>=',strtotime($dateTime[0]));
			$where[] = array('trade_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime_range'] = $param['datetime_range'];
		}else{
			$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$where[] = array('trade_time','>=',$todayStart);
			$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
			$where[] = array('trade_time','<=',$todayEnd);
		}

		//查询符合条件的数据
		$resultData = $this->where($where)->order(['trade_time'=>'desc','id'=>'desc'])->paginate(16,false,['query'=>$pageParam]);
		//数据集转数组
		$tradeList = $resultData->toArray()['data'];
		//部分元素重新赋值
		$tradeType   = config('custom.transactionType');//交易类型
		$orderStates = config('custom.orderStates');
		$orderColor  = config('manage.color');
		$adminColor  = config('manage.adminColor');
		foreach ($tradeList as $key => &$value) {
			$value['tradeType']      = $tradeType[$value['trade_type']];
			$value['tradeTypeColor'] = $adminColor[$value['trade_type']];
			$value['statusStr']      = config('custom.tradedetailsStatus')[$value['state']];
			$value['statusColor']    = $orderColor[$value['state']];
			$value['front_type_str'] = config('custom.front_type')[$value['front_type']];
			$value['payway_str']     = config('custom.payway')[$value['payway']];
		}

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',4],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'tradeList'		=>	$tradeList,
			'page'			=>	$resultData->render(),//分页
			'where'			=>	$pageParam,
			'tradeType'		=>	$tradeType,
			'power'			=>	$power,
			'types'		=>  $types,
		);
	}

	/**
	 * 流水详情
	 */
	public function financialDateils(){
		$param = input('get.');

		$detailsInfo = $this->where('id',$param['id'])->find();

		if ($detailsInfo['uid']) $detailsInfo['mName'] = model('Users')->where('id', $detailsInfo['uid'])->value('username');
		if ($detailsInfo['sid']) $detailsInfo['uName'] = model('Users')->where('id', $detailsInfo['sid'])->value('username');
		//交易类型
		$detailsInfo['tradeType'] = config('custom.transactionType')[$detailsInfo['trade_type']];

		return $detailsInfo;
	}
}
