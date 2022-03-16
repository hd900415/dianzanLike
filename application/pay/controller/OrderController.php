<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
 
namespace app\pay\controller;

use think\Controller;
use think\Cache;
use GatewayClient\Gateway;

class OrderController extends Controller{
	
	//初始化方法
	protected function initialize(){

		header("Access-Control-Allow-Origin:*");
		
		header('Access-Control-Allow-Methods:POST');
		
		header('Access-Control-Allow-Headers:x-requested-with, content-type');
		
	}
	
	//创建订单接口
	//返回支付页面 paymentUrl
	//直接创建订单 跳转 页面提交入库
	public function createOrder(){

		$data = model('Order')->createOrder();
		return json($data);
	}
	
	//创建订单接口
	//返回支付页面 paymentUrl
	//直接创建订单 直接入库
	public function CreateOrderup(){

		$data = model('Order')->CreateOrderup();
		return json($data);
	}
	
	
	//新创建订单接口
	//返回支付页面 paymentUrl 加密商户提交数据 商户跳转到平台支付页面 由支付平台提交 创建订单
	//支付平台创建订单 跳转 页面提交入库
	public function newCreateOrder(){
		$data = model('Order')->newCreateOrder();
		return json($data);
	}
	
	// 第三方post提交 支付平台创建订单
	public function postCreateOrder(){
		$data = model('Order')->postCreateOrder();
		return json($data);
	}
	
	// 第三方get访问 支付平台创建订单
	public function getCreateOrder(){

		$data = model('Order')->getCreateOrder();
		return json($data);
		
	}

	//交易查询接口
	public function queryOrder(){
		$data = model('Order')->queryOrder();
		return json($data);
		
	}
	
	//获取订单二维码
	public function timingOrder(){
		$data = model('Order')->timingOrder();
		return json($data);
	}
	
	//定时执行
	public function expireOrder(){
		$data = model('Order')->expireOrder();
		return  date('Y-m-d H:i:s');
	}

}