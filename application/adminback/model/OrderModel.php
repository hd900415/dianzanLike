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
		if (session('admin_types') == 1) {
			$where[] = array('merchant_team.uid','=',session('admin_userid'));
			$resultData = $this->field('ly_order.*')->join('merchant_team','ly_order.mid=merchant_team.team')->where($where)->order('ordertimes','desc')->paginate(15,false,['query'=>$pageParam]);
		} else {
			$where[] = array('mid','=',session('admin_userid'));
			// 查询符合条件的数据
			$resultData = $this->where($where)->order('ordertimes','desc')->paginate(15,false,['query'=>$pageParam]);
		}		
		// 数据集转数组
		$data = $resultData->toArray()['data'];
		// 获取配置
		$transactionType = config('custom.transactionType');
		$orderStates     = config('custom.orderStates');
		$payway          = config('custom.payway');

		// 重新赋值
		foreach ($data as $key => &$value) {
			$value['paywayStr']    = $payway[$value['payway']];
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

		// 重新赋值
		$info['paywayStr']    = config('custom.payway')[$info['payway']];
		$info['statusStr']    = config('custom.orderStates')[$info['status']];
		$info['ordertypeStr'] = config('custom.transactionType')[$info['ordertype']];
		if ($info['payid']) {
			$_qrcode = model('Qrcode')->field('payway,paywayurl')->where('id', $info['payid'])->findOrEmpty();

			$qrcodeArray = ['qrcode'=>$_qrcode['paywayurl'], 'imgName'=>trading_number()];
			if (is_file('./resource/'.$_qrcode['payway'].'.png')) $qrcodeArray['logoPath'] = './resource/'.$_qrcode['payway'].'.png';
			
			$info['_qrcode'] = controller('common/common')->produceQrcode($qrcodeArray);
		}		

		return $info;
	}
}