<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\controller;

use app\admin\controller\Common;

class DetailedController extends CommonController{
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
		$data = model('TradeDetails')->merchantDetailed();

		return view('', [
			'where'           =>	$data['where'],
			'data'            =>	$data['data'],
			'page'            =>	$data['page'],
			'transactionType' =>	$data['transactionType'],
		]);
	}
}