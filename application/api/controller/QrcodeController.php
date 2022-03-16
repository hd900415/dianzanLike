<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
 
namespace app\api\controller;

use think\Cache;

use app\api\controller\BaseController;

class QrcodeController extends BaseController{
	
	public function addQrcode(){
		$data = model('Qrcode')->addQrcode();
		return json($data);
	}
	
	
	public function getQrcodeList(){
		$data = model('Qrcode')->getQrcodeList();
		return json($data);
	}
	
	
	public function getQrcodeInfo(){
		$data = model('Qrcode')->getQrcodeInfo();
		return json($data);
	}
	
	
	public function changeQrcodeInfo(){
		$data = model('Qrcode')->changeQrcodeInfo();
		return json($data);
	}
}