<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\admin\controller;

use think\Controller;

class IndexController extends Controller{
	/**
	 * 空操作处理
	 */
	public function _empty(){
		return $this->index();
	}
	
    public function index(){
		//是否登录
		$is_admin_login = session('is_admin_login');		
		if(!$is_admin_login) return $this->fetch('login');

		//获取用户权限
		$adminRole = model('AdminRole')->getAdminsRoleByUsersId();
		//获取标题
		$adminTitle = model('Setting')->where('id','>','0')->value('admin_title');
		
		return view('', [
			'title'          => $adminTitle,
			'admin_username' => session('admin_username'),
			'admin_userid'   => session('admin_userid'),
			'adminRole'      => $adminRole
		]);
	}
	
	//登录提交
	public function login(){
		if (!$this->request->isAjax()) return 'nouser';
		return model('Merchant')->checkLogin();
	}
	
	//验证码
	public function code(){

		ob_clean();
		
		$image = imagecreatetruecolor(100, 34);  
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
		session('code',$captch_code);  
	  
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
	//退出
	public function logout(){
		//删除session 包括用户登录数据 ，添加文章数据
		session('is_admin_login', null);
		session('admin_username', null);
		session('admin_userid', null);
		header("location:/admin/index");
		exit();
		// return $this->success('退出成功', '/admin/index', 2);
	}

	public function home(){
		//获取标题
		$adminTitle = model('Setting')->where('id','>','0')->value('admin_title');
		return view('', [
			'title'          => $adminTitle,
			'admin_username' => session('admin_username'),
			'admin_userid'   => session('admin_userid'),
		]);
	}

}
