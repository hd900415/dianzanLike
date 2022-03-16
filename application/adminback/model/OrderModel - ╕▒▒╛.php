<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 作用：生成操作日志
 */

namespace app\admin\model;

use think\Model;

class OrderModel extends Model{
	//表名
	protected $table = 'ly_order';

	/**
	 * 订单明细
	 * @return [type] [description]
	 */
	public function index(){
		$param = input('get.');
		// 查询条件组装
		$where[] = array('mid','=',session('admin_userid'));
		// 分页参数组装
		$pageParam = array();
		// 订单号
		if(isset($param['order_id']) && $param['order_id']){
			$where[] = array('jorderid','=',$param['order_id']);
			$pageParam['order_id'] = $param['order_id'];
		}
		// 订单状态
		if(isset($param['status']) && $param['status']){
			$where[] = array('status','=',$param['status']);
			$pageParam['status'] = $param['status'];
		}
		// 时间
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('ordertimes','>=',strtotime($dateTime[0]));
			$where[] = array('ordertimes','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		} else {
			$where[] = array('ordertimes','>=',mktime(0,0,0,date('m'),date('d'),date('Y')));
			$where[] = array('ordertimes','<=',mktime(23,59,59,date('m'),date('d'),date('Y')));
		}
		// 查询符合条件的数据
		$resultData = $this->where($where)->order('ordertimes','desc')->paginate(15,false,['query'=>$pageParam]);
		// 数据集转数组
		$data = $resultData->toArray()['data'];
		// 获取配置
		$transactionType = config('custom.transactionType');
		$orderStates     = config('custom.orderStates');

		// 重新赋值
		foreach ($data as $key => &$value) {
			$value['statusStr']    = $orderStates[$value['status']];
			$value['ordertypeStr'] = $transactionType[$value['ordertype']];
		}

		return array(
			'where'           =>	$pageParam,
			'data'            =>	$data, // 数据
			'page'            =>	$resultData->render(), // 分页
			'orderStates'     =>	$orderStates,
			'transactionType' =>	$transactionType,
		);
	}

	/**
	 * 订单详情
	 * @return [type] [description]
	 */
	public function orderDetailed(){
		$param = input('param.');
		if (!$param) die('订单不存在');

		$info = $this->where('id', $param['id'])->findOrEmpty();
		if (!$info) die('订单不存在');

		// 获取配置
		$transactionType = config('custom.transactionType');
		$orderStates     = config('custom.orderStates');
		// 重新赋值
		$info['statusStr']    = $orderStates[$info['status']];
		$info['ordertypeStr'] = $transactionType[$info['ordertype']];
		if ($info['payid']) {
			$_qrcode = model('Qrcode')->field('payway,paywayurl')->where('id', $info['payid'])->findOrEmpty();
			$info['_qrcode'] = controller('common/common')->produceQrcode(['qrcode'=>$_qrcode['paywayurl'], 'imgName'=>trading_number(), 'logoPath'=>'./resource/'.$_qrcode['payway'].'.png']);
		}		

		return $info;
	}
}