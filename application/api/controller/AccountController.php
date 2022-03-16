<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
 
namespace app\api\controller;

use think\Cache;

use app\api\controller\BaseController;

class AccountController extends BaseController{

	/** 添加银行卡 **/
	public function addBankCard(){
		$data = model('UserBank')->addBankCard();
		return json($data);
	}
	
	/** 获取银行卡列表 **/
	public function getBankCardList(){
		$data = model('UserBank')->getBankCardList();
		return json($data);
	}
	
	/** 获取银行卡详细信息 **/
	public function getBankCardInfo(){
		$data = model('UserBank')->getBankCardInfo();
		return json($data);
	}
	
	/** 修改银行卡 **/
	public function changeBankCardInfo(){
		$data = model('UserBank')->changeBankCardInfo();
		return json($data);
	}
	
	/** 实名认证 **/
	public function realname(){
		$data = model('User')->realname();
		return json($data);
	}
			
	/**	获取验证码
	 * 			type 		param 				explain 							must 			default 			other
	 * @param 	string 		sign 				签名									1
	 * @param 	string 		code_rand 			时间标签								1
	 
	 * @return 	img         code_url            验证码图片
	**/
	public function Code(){
		
		ob_clean();
		
		$param 	= input('param.');
		
		if(!$param['code_rand']) exit();

		$image	= imagecreatetruecolor(100, 34);  
		$bgcolor = imagecolorallocate($image, 255, 255, 255);  
		imagefill($image, 0, 0, $bgcolor);  
	  
		$captch_code = '';  
		for($i=0;$i<4;$i++) { 
		 
			$fontsize = 6;  
			$fontcolor = imagecolorallocate($image, rand(0, 120), rand(0, 120),rand(0, 120));  
	  
			$data = '0123456789';  
			$fontcontent = substr($data, rand(0, strlen($data)-1), 1);  
			$captch_code .= $fontcontent;  
	  
			$x = ($i*100/4) + rand(5, 10);  
			$y = rand(5, 10);  
	  
			imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);  
		}
		
		cache('C_Code_'.$param['code_rand'],$captch_code,60);

		//增加点干扰元素  
		for($i=0; $i<200;$i++) {  
			$pointcolor = imagecolorallocate($image, rand(50,200), rand(50,200), rand(50,200));  
			imagesetpixel($image, rand(1,99), rand(1,29), $pointcolor);  
		}  
	  
		//增加线干扰元素  
		for($i=0;$i<3;$i++) {  
			$linecolor = imagecolorallocate($image, rand(80,220), rand(80,220), rand(80, 220));  
			imageline($image, rand(1,99), rand(1,29), rand(1,99), rand(1,29), $linecolor);  
		}
		
		header('content-type:image/png');		  
		imagepng($image);
		imagedestroy($image);
		
	}	
}