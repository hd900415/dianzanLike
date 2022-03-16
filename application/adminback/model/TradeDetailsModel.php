<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 作用：生成操作日志
 */

namespace app\admin\model;

use think\Model;

class TradeDetailsModel extends Model{
	//表名
	protected $table = 'ly_trade_details';

	/**
	 * 历史明细
	 * @return [type] [description]
	 */
	public function merchantDetailed(){
		$param = input('get.');
		// 查询条件组装
		$where = array(
			['uid', '=', session('admin_userid')],
			['types', '=', 2]
		);
		// 分页参数组装
		$pageParam = array();
		// 订单号
		if(isset($param['order_id']) && $param['order_id']){
			$where[] = array('order_number','=',$param['order_id']);
			$pageParam['order_id'] = $param['order_id'];
		}
		// 时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('trade_time','>=',strtotime($dateTime[0]));
			$where[] = array('trade_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		} else {
			$where[] = array('trade_time','>=',mktime(0,0,0,date('m'),date('d'),date('Y')));
			$where[] = array('trade_time','<=',mktime(23,59,59,date('m'),date('d'),date('Y')));
		}
		// 查询符合条件的数据
		$resultData = $this->where($where)->order('trade_time','desc')->paginate(15,false,['query'=>$pageParam]);
		// 获取配置
		$transactionType = config('custom.transactionType');
		// 重新赋值
		foreach ($resultData as $key => &$value) {
			$value['trade_type'] = ($value['trade_type'] == 6 || $value['trade_type'] == 7) ? 1 : $value['trade_type'];
			if (in_array($value['trade_type'], [2,11])) $value['trade_amount'] = '-'.$value['trade_amount'];
			$value['ordertypeStr'] = $transactionType[$value['trade_type']];
		}

		return array(
			'where'           =>	$pageParam,
			'data'            =>	$resultData->toArray()['data'], // 数据
			'page'            =>	$resultData->render(), // 分页
			'transactionType' =>	$transactionType,
		);
	}
}