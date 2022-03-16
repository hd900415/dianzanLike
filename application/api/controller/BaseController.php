<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\controller;

use think\Controller;
use think\Cache;
//use GatewayClient\Gateway;

class BaseController extends Controller{
	//初始化方法
	protected function initialize(){	
		
	 	parent::initialize();

		header("Access-Control-Allow-Origin:*");
		header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
		header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");	
		//清除所有缓存
		//app('cache')->clear();
		
		//内存缓存 内存
		ini_set ('memory_limit', '800M');
		//接口白名单		
		
		//不用用户登录可以访问的页面
		$no_user_id_arr	= array('code','login','getdownloadurl','checkslogin','sendsmscode','register','checksmsresetpw','resetpassword','backdata','createorder','orderlist','orderrecordlist','repayment','signin','gettaskranklist','gettaskclasslist','getvipuserexpire','gettasklist','gettaskinfo','taskordertrial');

		$action = request()->action();
		
		//获取提交用户提交数据 并保存
		$param = input('param.');
		
		//更新vip会员
		/*$UserVipData	= model('UserVip')->where(array(['state','=',1],['etime','<',strtotime(date("Y-m-d",time()))]))->select()->toArray();
		
		foreach($UserVipData as $key=>$value){
			//会员过期
			$isup =  model('UserVip')->where('id' , $value['id'])->update(array('state'=>3));
			//更新会员等级
			if($isup){
				model('Users')->where('id', $value['uid'])->update(array('vip_level'=>1));//普通会员
			}
		}*/
		
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		if(!in_array($action,$no_user_id_arr)){

			$user_token = isset($param['token']) && $param['token'] ? $param['token'] : '';

			//检查 userid usertoken
			if(!$user_token){
				$data['code'] = 203;
				
			if($lang=='cn')	$data['code_dec'] = '没有登录';
			elseif($lang=='en') $data['code_dec'] 	= 'not logged on';
			elseif($lang=='id') $data['code_dec']	= 'tidak didaftar';
			elseif($lang=='ft') $data['code_dec']	= '沒有登錄';
			elseif($lang=='vi') $data['code_dec']	= 'chưa đăng nhập';
			elseif($lang=='es') $data['code_dec']	= 'Sin comentarios';
			elseif($lang=='ja') $data['code_dec']	= 'ログインしていません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีล็อกอิน';
			elseif($lang=='yd') $data['code_dec']	= 'लगइन नहीं है';
			
				ajax_return($data);
			}
			
			$user_token		= stripslashes($user_token);
			$userArr 		= explode(',',auth_code($user_token,'DECODE'));//用户信息数组
			$uid 			= $userArr[0];//uid
			$username 		= $userArr[1];//uid

			//检查缓存是否存在
			if(!cache('C_token_'.$uid)){
				$data['code']		= 203;//没有登录
				
			if($lang=='cn')	$data['code_dec']	= '没有登录';
			elseif($lang=='en') $data['code_dec'] 	= 'not logged on';
			elseif($lang=='id') $data['code_dec']	= 'tidak didaftar';
			elseif($lang=='ft') $data['code_dec']	= '沒有登錄';
			elseif($lang=='vi') $data['code_dec']	= 'chưa đăng nhập';
			elseif($lang=='es') $data['code_dec']	= 'Sin comentarios';
			elseif($lang=='ja') $data['code_dec']	= 'ログインしていません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีล็อกอิน';
			elseif($lang=='yd') $data['code_dec']	= 'लगइन नहीं है';
				ajax_return($data);
			}
			
			if(cache('C_token_'.$uid) != $user_token){
				$data['code']		= 204;//长时间没操作请重新登录
			if($lang=='cn') $data['code_dec']	= '长时间没操作请重新登录';
			elseif($lang=='en') $data['code_dec'] = 'No operation for a long time, please log in again!';
			elseif($lang=='id') $data['code_dec']	= 'Tidak ada operasi untuk waktu yang lama, silakan log masuk lagi';
			elseif($lang=='ft') $data['code_dec']	= '長時間沒操作請重新登入';
			elseif($lang=='vi') $data['code_dec']	= 'Không có hoạt động trong một thời gian dài, xin hãy đăng nhập lại lần nữa.';
			elseif($lang=='es') $data['code_dec']	= 'Por favor inicie de nuevo.';
			elseif($lang=='ja') $data['code_dec']	= '長い間操作していません。再登録してください。';
			elseif($lang=='th') $data['code_dec']	= 'กรุณาเข้าสู่ระบบอีกครั้งเมื่อคุณไม่ได้ทำงานเป็นเวลานาน';
			elseif($lang=='yd') $data['code_dec'] = 'लंबे समय के लिए कोई आपरेशन नहीं, कृपया फिर लॉग इन करें';
				ajax_return($data);
			}
			
			//检查用户是否存在
			$isuser = model('Users')->where(array('id'=>$uid,'username'=>$username,'state'=>1))->count();
			if(!$isuser){
				$data['code']		= 203;
				
			if($lang=='cn') $data['code_dec']	= '用户不存在';
			elseif($lang=='en') $data['code_dec'] = 'User does not exist!';
			elseif($lang=='id') $data['code_dec']	= 'pengguna tidak ada';
			elseif($lang=='ft') $data['code_dec']	= '用戶不存在';
			elseif($lang=='yd') $data['code_dec']	= 'उपयोक्ता मौजूद नहीं है';
			elseif($lang=='vi') $data['code_dec']	= 'người dùng không tồn tại';
			elseif($lang=='es') $data['code_dec']	= 'Usuario no existente';
			elseif($lang=='ja') $data['code_dec']	= 'ユーザが存在しません';
			elseif($lang=='th') $data['code_dec']	= 'ผู้ใช้ไม่มี';
				ajax_return($data);
			}
			
			//增加缓存的时间
			cache('C_token_'.$uid,$user_token,7200);

			//登录接口 添加提交数据日志
			foreach ($param as $key => $value) {
				$params[] = $key;
				$values[] = $value;
			}

			$homelog = array(
				'uid'		=>	$uid,
				'time'		=>	time(),
				'params'	=>	json_encode($params),
				'values'	=>	json_encode($values),
				'ip'		=>	$this->request->ip(),
				'func'		=>	$this->request->action(),
				'cla'		=>	$this->request->controller()
			);
			model('Homelog')->insert($homelog);
		}
    }

	
	public function getDownloadUrl(){
    	$setting = model('Setting')->field('android,iphone')->where('id','>',0)->findOrEmpty();
    	if (!$setting) return json(['code'=>0,'code_dec'=>'无法获取下载地址！']);

		$data['android'] = $setting['android'];
		$data['iphone']  = $setting['iphone'];

    	return json(['code'=>1,'data'=>$data]);
    }

}
