<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\controller;

use app\admin\controller\Common;

class OrderController extends CommonController{
	/**
	 * 空操作处理
	 */
	public function _empty(){
		return $this->index();
	}
	/**
	 * 订单明细
	 * @return [type] [description]
	 */
	public function index(){
		$data = model('Order')->index();

		return view('', [
			'where'           =>	$data['where'],
			'data'            =>	$data['data'],
			'page'            =>	$data['page'],
			'orderStates'     =>	$data['orderStates'],
			'transactionType' =>	$data['transactionType'],
		]);
	}
	/**
	 * 详情
	 * @return [type] [description]
	 */
	public function orderDetailed(){
		$data = model('Order')->orderDetailed();

		return view('', [
			'data' =>	$data,
		]);
	}

	/**
	 * 回调
	 */
	public function callBack(){
		$id = input('post.id/d');
		$order = model('Order')->where('id', $id)->findOrEmpty();
		if (!$order) return '订单不存在';

		$callBackData = array(
			'uid'              => $order['uid'],
			'merchantId'       => model('Merchant')->where('id', $order['mid'])->value('merchantid'),
			'timestamp'        => $order['timestamp'],
			'signatureMethod'  => 'HmacSHA256',
			'signatureVersion' => 1,
			'orderId'          => $order['orderid'],
			'status'           => 3,
			'jOrderId'         => $order['jorderid'],
			'notifyUrl'        => base64_decode($order['notifyurl']),
			'orderType'        => $order['ordertype'],
			'amount'           => $order['oamount'],
			'currency'         => $order['currency'],
			'actualAmount'     => $order['oactualamount'],
			'fee'              => $order['feeamount'],
			'payWay'           => $order['payway'],
			'payTime'          => $order['paytimes'],
			'jExtra'           => base64_decode($order['jextra']),
			'mkey'             => $order['mkey'],
		);
		model('api/Order')->Callback($callBackData);

		return 1;
	}
}