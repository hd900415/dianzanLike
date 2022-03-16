<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;
use think\Cache;
use think\facade\Request;

class UsersModel extends Model{
	
	protected $table = 'ly_users';
	
	// 邀请码用户注册
	public function register(){
		$param = input('param.');
		
		// 新用户信息		
		$username		= $param['username'];		// 用户名
		$password		= input('post.password/s');	// 密码
		$smscode		= input('post.smscode/d');	// 验证码
		$dest			= (input('post.dest')) ? input('post.dest') : 86;	// 国家区号
		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
// 		if($smscode){
// 			$cachesmscode	= cache('C_Code_'.$username);		
// 			if(!$smscode)   return ['code'=>0,'code_dec'=>'请输入验证码'];
// 			if($cachesmscode != $smscode){
// 				$data['code'] = 0;
// 				if($lang=='cn')	$data['code_dec'] = '验证码错误';
// 				else $data['code_dec'] = 'Verification code error!';
// 				return $data;
// 			}
// 			//删除验证码缓存
// 			cache('C_Code_'.$username, NULL);
// 		}else{
			
			$code_rand		= (input('post.code_rand')) ? input('post.code_rand') : '';// 随机码
			$code			= (input('post.code')) ? input('post.code') : '';// 验证码
			$cache_code		= cache('C_Code_'.$code_rand);
			if(!$cache_code || $cache_code != $code || !$code){
				if($lang=='cn')	$data['code_dec'] = '验证码错误';
				elseif($lang=='en') $data['code_dec'] = 'Verification code error!';
			elseif($lang=='id') $data['code_dec']	= 'Galat kode verifikasi';
			elseif($lang=='ft') $data['code_dec']	= '驗證碼錯誤';
			elseif($lang=='yd') $data['code_dec']	= 'सत्यापन कोड त्रुटि';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi mã kiểm tra';
			elseif($lang=='es') $data['code_dec']	= 'Código de verificación';
			elseif($lang=='ja') $data['code_dec']	= '認証コードエラー';
			elseif($lang=='th') $data['code_dec']	= 'รหัสการตรวจสอบข้อผิดพลาด';
				
				return $data;
			}
			cache('C_Code_'.$code_rand, NULL);

// 		}
		//密码是否一致
		$re_password	=	input('post.re_password/s');	// 密码
		
		if($password != $re_password or !$password){
			$data['code'] = 0;
			if($lang=='cn')	$data['code_dec'] = '密码不一致';
			elseif($lang=='en') $data['code_dec'] = 'Passwords are inconsistent!';
			elseif($lang=='id') $data['code_dec']	= 'Tidak konsistens kata sandi';
			elseif($lang=='ft') $data['code_dec']	= '密碼不一致';
			elseif($lang=='yd') $data['code_dec']	= 'पासवर्ड असंगतता';
			elseif($lang=='vi') $data['code_dec']	= 'Không có mật khẩu';
			elseif($lang=='es') $data['code_dec']	= 'Contraseña incorrecta.';
			elseif($lang=='ja') $data['code_dec']	= 'パスワードが一致しません';
			elseif($lang=='th') $data['code_dec']	= 'รหัสผ่านไม่ตรงกัน';
			return $data;
		}

		// 检测用户是否已注册
		$countUser = $this->field('id')->where('username', $username)->count();

		if($countUser){
			$data['code'] 		= 0;
			if($lang=='cn')	$data['code_dec'] 	= '该手机号已经注册';
			elseif($lang=='en') $data['code_dec'] = 'The mobile number has been registered!';
			elseif($lang=='id') $data['code_dec']	= 'Nomor ponsel telah terdaftar';
			elseif($lang=='ft') $data['code_dec']	= '該手機號已經注册';
			elseif($lang=='yd') $data['code_dec']	= 'मोबाइल फोन संख्या रेजिस्टर किया गया है';
			elseif($lang=='vi') $data['code_dec']	= 'Số điện thoại di động đã được đăng ký';
			elseif($lang=='es') $data['code_dec']	= 'El número de teléfono está registrado.';
			elseif($lang=='ja') $data['code_dec']	= 'この携帯番号は登録済みです。';
			elseif($lang=='th') $data['code_dec']	= 'เบอร์โทรศัพท์ที่ลงทะเบียนไว้แล้ว';
			return $data;
		}
		
		//  生成邀请码必须唯一
		$is_repeat	= 1;
		$chk_idcode	= 1;
		$idCodearr = $this->column('idcode');
		while($is_repeat == $chk_idcode){	// 防止邀请码重复
			list($msec, $sec) = explode(' ', microtime());
			$msectime	= (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
			$new_idcode		= substr($msectime,-7);		// 邀请码
			if(!in_array($new_idcode,$idCodearr)){
				$chk_idcode	=	0;
			}
		}
		$recommend		= (input('post.recommend')) ? input('post.recommend') : 0;// 推荐人 邀请码
		if($recommend == 0 || $username==$recommend){
			$data['code'] 		= 0;
			if($lang=='cn')	$data['code_dec'] 	= '请填写邀请码!';
			elseif($lang=='en') $data['code_dec'] 	= 'Pleale input recommend code!';
			elseif($lang=='id') $data['code_dec']	= 'Silakan isi kode undangan';
			elseif($lang=='ft') $data['code_dec']	= '請填寫邀請碼';
			elseif($lang=='yd') $data['code_dec']	= 'कृपया निमन्त्रण कोड भरें!';
			elseif($lang=='vi') $data['code_dec']	= 'Xin hãy điền mã thư mời.';
			elseif($lang=='es') $data['code_dec']	= 'Rellene la invitación.';
			elseif($lang=='ja') $data['code_dec']	= '招待番号を記入してください';
			elseif($lang=='th') $data['code_dec']	= 'กรุณากรอกรหัสเชิญ';
			return $data;
		}
		
		$qq				= (input('post.qq')) ? input('post.qq') : '';// QQ
		$weixin			= (input('post.weixin')) ? input('post.weixin') : '';// QQ
		$suserinfo		= array();
		if($recommend){
			$where = [
				['ly_users.idcode','=',$recommend],
				['ly_users.state','=',1],
			];
			$where2 = [
				['ly_users.username','=',$recommend],
				['ly_users.state','=',1],
			];
			
			$suserinfo = model('Users')->field('ly_users.id,ly_users.vip_level,ly_users.idcode,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->whereOr([$where, $where2])->find();
		}
		
		$sid	= 0;
		if($suserinfo){
			$sid	=	$suserinfo['id'];
		}else{
			$data['code'] 		= 0;
			if($lang=='cn')	$data['code_dec'] 	= '邀请码不存在!';
			elseif($lang=='en') $data['code_dec'] 	= 'Recommend code error!';
			elseif($lang=='id') $data['code_dec']	= 'Kode undangan tidak ada';
			elseif($lang=='ft') $data['code_dec']	= '邀請碼不存在';
			elseif($lang=='yd') $data['code_dec']	= 'निमन्त्रण कोड मौजूद नहीं है';
			elseif($lang=='vi') $data['code_dec']	= 'Mã mời không tồn tại';
			elseif($lang=='es') $data['code_dec']	= 'La invitación no existe.';
			elseif($lang=='ja') $data['code_dec']	= '招待コードが存在しません';
			elseif($lang=='th') $data['code_dec']	= 'รหัสเชิญไม่มี';
			return $data;
		}
		
		//邀请码的每日注册人数限制
		$t = time();
		$start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
		$end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));

		
		$s_code_reg_num		=	model('Setting')->where('id',1)->value('reg_code_num');
		if($s_code_reg_num>0){

			$code_reg_num 		=	$this->where(array(['recommend','=',$suserinfo['idcode']],['reg_time','>=',$start],['reg_time','<=',$end]))->count();
			if($code_reg_num >= $s_code_reg_num){
				$data['code'] 		= 0;
			if($lang=='cn')	$data['code_dec'] 	= '邀请码不存在!';
			elseif($lang=='en') $data['code_dec'] 	= 'Recommend code error!';
			elseif($lang=='id') $data['code_dec']	= 'Kode undangan tidak ada';
			elseif($lang=='ft') $data['code_dec']	= '邀請碼不存在';
			elseif($lang=='yd') $data['code_dec']	= 'निमन्त्रण कोड मौजूद नहीं है';
			elseif($lang=='vi') $data['code_dec']	= 'Mã mời không tồn tại';
			elseif($lang=='es') $data['code_dec']	= 'La invitación no existe.';
			elseif($lang=='ja') $data['code_dec']	= '招待コードが存在しません';
			elseif($lang=='th') $data['code_dec']	= 'รหัสเชิญไม่มี';
			return $data;
			}
		}
		
		
		// 用户表注册新用户
		$new_user_data	= [
			'username'    		=> $username,
			'uid'         		=> $new_idcode,//uid 
			'password'    		=> auth_code($password,'ENCODE'),
			'phone'       		=> $username,
			'sid'         		=> $sid,//$db_sid,	// 上级id
			'reg_time'    		=> time(),	// 注册时间	
			'idcode'      		=> $new_idcode,// 邀请码
			'dest'        		=> $dest,
			'state'      		 => 1,		//
			'qq'		  		=> $qq,
			'weixin'	  		=> $weixin,
			'vip_level'	  		=> 1,		// 普通会员
			'header'	  		=> 'head_1.png',
			'recommend'	  		=> $suserinfo['idcode'],//统一用idcode 做邀请码 统计 	
			'last_ip'	  		=> request()->ip(),
		];

		$insert_id = model('Users')->insertGetId($new_user_data);
		if (!$insert_id){
			$data['code'] 		= 0;
			if($lang=='cn')	$data['code_dec'] 	= '注册失败';
			elseif($lang=='en') $data['code_dec'] 	= 'Login has failed!';
			elseif($lang=='id') $data['code_dec']	= 'login telah gagal';
			elseif($lang=='ft') $data['code_dec']	= '注册失敗';
			elseif($lang=='yd') $data['code_dec']	= 'लागइन असफल';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi đăng nhập';
			elseif($lang=='es') $data['code_dec']	= 'Fallo de registro';
			elseif($lang=='ja') $data['code_dec']	= '登録に失敗しました';
			elseif($lang=='th') $data['code_dec']	= 'ล้มเหลวในการลงทะเบียน';
		    return $data;
		}

		// 汇总表
		$insertTotalId = model('UserTotal')->insertGetId(array('uid' => $insert_id));
		if (!$insertTotalId) {
			$this->where('id',$insert_id)->delete();
			$data['code'] 		= 0;
			if($lang=='cn')	$data['code_dec'] 	= '注册失败';
			elseif($lang=='en') $data['code_dec'] 	= 'Login has failed!';
			elseif($lang=='id') $data['code_dec']	= 'login telah gagal';
			elseif($lang=='ft') $data['code_dec']	= '注册失敗';
			elseif($lang=='yd') $data['code_dec']	= 'लागइन असफल';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi đăng nhập';
			elseif($lang=='es') $data['code_dec']	= 'Fallo de registro';
			elseif($lang=='ja') $data['code_dec']	= '登録に失敗しました';
	    	elseif($lang=='th') $data['code_dec']	= 'ล้มเหลวในการลงทะเบียน';
		    return $data;
		}
		
		// 会员加入团队
		$insertTeam	= model('UserTeam')->addUserTeam($insert_id);
		if (!$insertTeam) {
			$this->where('id',$insert_id)->delete();
			model('UserTotal')->where('id',$insertTotalId)->delete();
			$data['code'] 	= 0;
			if($lang=='cn')	$data['code_dec'] 	= '注册失败';
			elseif($lang=='en') $data['code_dec'] 	= 'Login has failed!';
			elseif($lang=='id') $data['code_dec']	= 'login telah gagal';
			elseif($lang=='ft') $data['code_dec']	= '注册失敗';
			elseif($lang=='yd') $data['code_dec']	= 'लागइन असफल';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi đăng nhập';
			elseif($lang=='es') $data['code_dec']	= 'Fallo de registro';
			elseif($lang=='ja') $data['code_dec']	= '登録に失敗しました';
	    	elseif($lang=='th') $data['code_dec']	= 'ล้มเหลวในการลงทะเบียน';
		    return $data;
		}
		
		//用上级 邀请好友奖励
		/*if($suserinfo){
			//注册奖励 
			$regment			= model('Setting')->where('id',1)->value('regment');
			$is_up_to 			= model('UserTotal')->where('uid', $suserinfo['id'])->setInc('balance', $regment);
			if($is_up_to){
				//加总金额
				model('UserTotal')->where('uid', $suserinfo['id'])->setInc('total_balance', $regment);
				// 流水
				$financial_data_p['uid'] 					= $suserinfo['id'];
				$financial_data_p['username'] 				= $suserinfo['username'];
				$financial_data_p['order_number'] 			= 'D'.trading_number();
				$financial_data_p['trade_number'] 			= 'L'.trading_number();
				$financial_data_p['trade_type'] 			= 7;
				$financial_data_p['trade_before_balance']	= $suserinfo['balance'];
				$financial_data_p['trade_amount'] 			= $regment;
				$financial_data_p['account_balance'] 		= $suserinfo['balance'] + $regment;
				$financial_data_p['remarks'] 				= '邀请奖励';
				$financial_data_p['types'] 					= 1;	// 用户1，商户2
				model('common/TradeDetails')->tradeDetails($financial_data_p);
			}
		}*/
		//邀请奖励
		
		$data['code']	= 1;
		if($lang=='cn')	$data['code_dec'] 	= '注册成功';
		elseif($lang=='en') $data['code_dec'] 	= 'Login was successful!';
			elseif($lang=='id') $data['code_dec']	= 'login berhasil';
			elseif($lang=='ft') $data['code_dec']	= '注册成功';
			elseif($lang=='yd') $data['code_dec']	= 'लागइन सफल होता है';
			elseif($lang=='vi') $data['code_dec']	= 'đã đăng nhập thành công';
			elseif($lang=='es') $data['code_dec']	= 'Inscripción exitosa';
			elseif($lang=='ja') $data['code_dec']	= '登録成功';
			elseif($lang=='th') $data['code_dec']	= 'ลงทะเบียนเรียบร้อยแล้ว';
	    return $data;
	}
	

	// 登录系统
	public function login(){
		$param = input('param.');
		
		//前端到短信验证页面
		$username	=	$param['username'];
		$password	=	$param['password'];
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		//获取用户信息
		$userinfo = $this->where(array(['username','=',$username],['state','=',1]))->find();

		//用户名不存在
		if(!$userinfo){
			$data['code'] = 2;
			if($lang=='cn')	$data['code_dec'] = '用户名不存在';
			elseif($lang=='en') $data['code_dec'] 	= 'Username does not exist!';
			elseif($lang=='id') $data['code_dec']	= 'nama pengguna tidak ada';
			elseif($lang=='ft') $data['code_dec']	= '用戶名不存在';
			elseif($lang=='yd') $data['code_dec']	= 'उपयोक्ता नाम मौजूद नहीं है';
			elseif($lang=='vi') $data['code_dec']	= 'tên người dùng không tồn tại';
			elseif($lang=='es') $data['code_dec']	= 'El nombre de usuario no existe';
			elseif($lang=='ja') $data['code_dec']	= 'ユーザ名が存在しません';
			elseif($lang=='th') $data['code_dec']	= 'ชื่อผู้ใช้';
			return $data;
		}
		
		//检查密码
		if(auth_code($userinfo['password'],'DECODE') != $password){
			$data['code']		= 6;
			if($lang=='cn')	$data['code_dec']	= '用户名/密码错误';
			elseif($lang=='en') $data['code_dec'] 	= 'Username / password error!';
			elseif($lang=='id') $data['code_dec']	= 'Nama pengguna / kesalahan kata sandi';
			elseif($lang=='ft') $data['code_dec']	= '用戶名/密碼錯誤';
			elseif($lang=='yd') $data['code_dec']	= 'प्रयोक्ता नाम / पासवर्ड त्रुटि';
			elseif($lang=='vi') $data['code_dec']	= 'Tên người dùng/ lỗi mật khẩu';
			elseif($lang=='es') $data['code_dec']	= 'Error de usuario / contraseña';
			elseif($lang=='ja') $data['code_dec']	= 'ユーザ名/パスワードエラー';
			elseif($lang=='th') $data['code_dec']	= 'ชื่อผู้ใช้และรหัสผ่านผิดพลาด';
			return $data;
		}

		// 是否冻结
		if ($userinfo['state'] == 3 && $lang == 'cn') return ['code'=>0, 'code_dec'=>'您的账号违规，已被冻结，请联系上级处理！'];
		if ($userinfo['state'] == 3 && $lang == 'en') return ['code'=>0, 'code_dec'=>'Your account is illegal and has been frozen. Please contact your superior for handling！'];
		if ($userinfo['state'] == 3 && $lang == 'id') return ['code'=>0, 'code_dec'=>'Akaun Anda ilegal dan telah dibekukan. Silakan hubungi atasan Anda untuk menangani'];
		if ($userinfo['state'] == 3 && $lang == 'ft') return ['code'=>0, 'code_dec'=>'您的帳號違規，已被凍結，請聯系上級處理'];
		if ($userinfo['state'] == 3 && $lang == 'yd') return ['code'=>0, 'code_dec'=>'तुम्हारा खाता अवैध है और जल्दी हो गया है. कृपया उत्तम से संपर्क करें!'];
		if ($userinfo['state'] == 3 && $lang == 'vi') return ['code'=>0, 'code_dec'=>'Tài khoản của bạn là bất hợp pháp và đã bị đóng băng.'];
		if ($userinfo['state'] == 3 && $lang == 'es') return ['code'=>0, 'code_dec'=>'Su cuenta está bloqueada. Por favor, llame a sus superiores.'];
		if ($userinfo['state'] == 3 && $lang == 'ja') return ['code'=>0, 'code_dec'=>'あなたのアカウントは規則に違反して、すでに凍結されました。上司に連絡して処理してください。'];
		if ($userinfo['state'] == 3 && $lang == 'th') return ['code'=>0, 'code_dec'=>'บัญชีผู้ใช้ของคุณถูกบล็อกกรุณาติดต่อผู้บังคับบัญชาของคุณ'];
		
		//用户所在地
		$address = model('Loginlog')->GetIpLookup();
		if (!$address) {
			$address = '';
		}

		//获取用户端
		$logintype =  model('Loginlog')->getBrowserType();
		if ($logintype == 2) {
			if($lang=='cn')	$type = '前台手机网页版';
			else $type = 'Mobile client';
		}else{
			if($lang=='cn')	$type = '前台网页版';
			else $type = 'PC client';
		}

		// 添加登陆日志
		$loginlog = array(
			'uid'			=> $userinfo['id'],
			'username'		=> $userinfo['username'],
			'os'			=> get_os(),
			'browser'		=> get_broswer(),
			'ip'			=> request()->ip(),
			'time'			=> time(),
			'address'		=> $address,
			'type'			=> $type
		);
		
		$is_v = 1;
		if(request()->ip()==$userinfo['last_ip']){
			$is_v = 0;
		}		
		
		$is_insert_user_loginlog = model('Loginlog')->insert($loginlog);
		if(!$is_insert_user_loginlog){
			$data['code'] = 0;
			if($lang=='cn')	$data['code'] = '登录失败';
			elseif($lang=='en') $data['code'] = 'Login failed!';
			elseif($lang=='id') $data['code']	= 'Login gagal';
			elseif($lang=='ft') $data['code']	= '登入失敗';
			elseif($lang=='yd') $data['code']	= 'लागइन असफल';
			elseif($lang=='vi') $data['code']	= 'Lỗi đăng nhập';
			elseif($lang=='es') $data['code']	= 'Fallo de registro';
			elseif($lang=='ja') $data['code']	= 'ログイン失敗';
			elseif($lang=='th') $data['code']	= 'ล็อกอินล้มเหลว';
			return $data;
		}

		//更新用户登录状态
		$is_user_update = $this->where('id',$userinfo['id'])->data(array('last_ip'=>request()->ip(),'last_login'=>time(),'login_error'=>0))->setInc('login_number',1);
		if(!$is_user_update){
			$data['code'] = 0;
			if($lang=='cn')	$data['code'] = '登录失败';
			elseif($lang=='en') $data['code'] = 'Login failed!';
			elseif($lang=='id') $data['code']	= 'Login gagal';
			elseif($lang=='ft') $data['code']	= '登入失敗';
			elseif($lang=='yd') $data['code']	= 'लागइन असफल';
			elseif($lang=='vi') $data['code']	= 'Lỗi đăng nhập';
			elseif($lang=='es') $data['code']	= 'Fallo de registro';
			elseif($lang=='ja') $data['code']	= 'ログイン失敗';
			elseif($lang=='th') $data['code']	= 'ล็อกอินล้มเหลว';
			return $data;
		}

		$token = auth_code($userinfo['id'].','.$userinfo['username'],'ENCODE');		
		cache('C_token_'.$userinfo['id'],$token,7200);		
		if(!cache('C_token_'.$userinfo['id'])){
			$data['code'] = 0;
			if($lang=='cn')	$data['code'] = '登录失败';
			elseif($lang=='en') $data['code'] = 'Login failed!';
			elseif($lang=='id') $data['code']	= 'Login gagal';
			elseif($lang=='ft') $data['code']	= '登入失敗';
			elseif($lang=='yd') $data['code']	= 'लागइन असफल';
			elseif($lang=='vi') $data['code']	= 'Lỗi đăng nhập';
			elseif($lang=='es') $data['code']	= 'Fallo de registro';
			elseif($lang=='ja') $data['code']	= 'ログイン失敗';
			elseif($lang=='th') $data['code']	= 'ล็อกอินล้มเหลว';
			return $data;
		}
		
		
		$UserVipModel = model('UserVip');
		//会员自动升级
		$user_grades = model('UserGrade')->order('grade desc')->select();
		$user_total = model('UserTotal')->where('uid','=', $userinfo['id'])->find();
		foreach ($user_grades as $key => $user_grade) {
		    if ($user_total['balance'] >= $user_grade['amount']) {
		        //用户vip升级,  需求：用户登录，余额大于升级的金额，就改变用户的等级，（只改等级，其他的什么也不做）
		        $result = $UserVipModel->upgradeVip($userinfo['id'], $user_total['balance'], $user_grade);
		         
		         if ($result) {
		              ////扣除用户的余额
		              //model('UserTotal')->where('uid', '=', $userinfo['id'])->setDec('balance', $user_grade['amount']);
		         }
		         break;
		         
		    }
		}
		
		
		$userTotal = model('UserTotal')->where('uid',$userinfo['id'])->find();
		
		//返回数据
		if($lang=='cn')	$codeDec = '登录成功';
		elseif($lang=='en') $codeDec = 'Login successful!';
			elseif($lang=='id') $codeDec	= 'Login berhasil';
			elseif($lang=='ft') $codeDec	= '登入成功';
			elseif($lang=='yd') $codeDec	= 'लागइन सफल';
			elseif($lang=='vi') $codeDec	= 'Thành công Đăng nhập';
			elseif($lang=='es') $codeDec	= 'Acceso concedido.';
			elseif($lang=='ja') $codeDec	= 'ログイン成功';
			elseif($lang=='th') $codeDec	= 'เข้าสู่ระบบสำเร็จ';
		$data['code']              			= 1;
		$data['code_dec']           		= $codeDec;
		$data['info']['token']      		= cache('C_token_'.$userinfo['id']);
		$data['info']['userid']    			= $userinfo['id'];
		$data['info']['uid']    			= $userinfo['uid'];
		$data['info']['username']  			= $userinfo['username'];
		$data['info']['realname']  			= $userinfo['realname'];

		$data['info']['is_v']  				= $is_v;
		$data['info']['ip']  				= request()->ip();
		
		$data['info']['dest']  				= $userinfo['dest'];//国家区号
		$data['info']['phone']  			= $userinfo['phone'];//手机号
		
		$is_realname	=	0;
		
		if($userinfo['realname']){
			$is_realname	=	1;
		}
		
		$data['info']['is_realname']  					= $is_realname;//是否实名
		
		$data['info']['vip_level']  					= $userinfo['vip_level'];
		$UserGrade	= model('UserGrade')->where('grade', $userinfo['vip_level'])->find();
		
		$data['info']['balance']  						= $userTotal['balance'];//可用金额

		$data['info']['state'] 							= $userinfo['state'];
		$data['info']['pump']							= floatval(number_format($UserGrade['pump']/100,3));//抽水
		
		$data['info']['number']							= $UserGrade['number'];//每日任务
		
		$UserVip	=	model('UserVip')->where(array(['uid','=',$userinfo['id']],['state','=',1],['grade','=',$userinfo['vip_level']]))->find();
		
		if($UserVip){
			if($lang=='en'){
				$data['info']['useridentity']	=	$UserVip['en_name'];
			}elseif($lang=='cn'){
				$data['info']['useridentity']	=	$UserVip['name'];
			}elseif($lang=='id'){
				$data['info']['useridentity']	=	$UserVip['ydn_name'];
			}elseif($lang=='ft'){
				$data['info']['useridentity']	=	$UserVip['ft_name'];
			}elseif($lang=='yd'){
				$data['info']['useridentity']	=	$UserVip['yd_name'];
			}elseif($lang=='vi'){
				$data['info']['useridentity']	=	$UserVip['yn_name'];
			}elseif($lang=='es'){
				$data['info']['useridentity']	=	$UserVip['xby_name'];
			}elseif($lang=='ja'){
				$data['info']['useridentity']	=	$UserVip['ry_name'];
			}elseif($lang=='th'){
				$data['info']['useridentity']	=	$UserVip['ty_name'];
			}
			$data['info']['stime']			=	date('Y-m-d',$UserVip['stime']);
			$data['info']['etime']			=	date('Y-m-d',$UserVip['etime']);
		}else{
			if($lang=='en'){
				$data['info']['useridentity']	=	$UserGrade['en_name'];
			}elseif($lang=='cn'){
				$data['info']['useridentity']	=	$UserGrade['name'];
			}elseif($lang=='id'){
				$data['info']['useridentity']	=	$UserGrade['ydn_name'];
			}elseif($lang=='ft'){
				$data['info']['useridentity']	=	$UserGrade['ft_name'];
			}elseif($lang=='yd'){
				$data['info']['useridentity']	=	$UserGrade['yd_name'];
			}elseif($lang=='vi'){
				$data['info']['useridentity']	=	$UserGrade['yn_name'];
			}elseif($lang=='es'){
				$data['info']['useridentity']	=	$UserGrade['xby_name'];
			}elseif($lang=='ja'){
				$data['info']['useridentity']	=	$UserGrade['ry_name'];
			}elseif($lang=='th'){
				$data['info']['useridentity']	=	$UserGrade['ty_name'];
			}
			$data['info']['stime']			=	'';
			$data['info']['etime']			=	'';
		}
		
		$data['info']['header'] 						= $userinfo['header'];//头像
		
		$data['info']['sid'] 							= $userinfo['sid'];//直属上级id

		$data['info']['credit'] 						= $userinfo['credit'];//信用
		$data['info']['idcode'] 						= $userinfo['idcode'];//邀请码		
		$data['info']['qq'] 							= $userinfo['qq'];//邀请码
		
		$data['info']['weixin'] 						= $userinfo['weixin'];//邀请码
		$data['info']['douyin'] 						= $userinfo['douyin'];//邀请码
		$data['info']['kuaishou'] 						= $userinfo['kuaishou'];//邀请码
		$data['info']['alipay'] 						= $userinfo['alipay'];//邀请码
		$data['info']['alipay_name'] 					= $userinfo['alipay_name'];//邀请码	
		$data['info']['is_housekeeper'] 				= $userinfo['is_housekeeper'];//云管家
		
		if($userinfo['fund_password']){
			$data['info']['is_fund_password']				=	1;
		}else{
			$data['info']['is_fund_password']				=	0;
		}
		$data['info']['susername']			=	'';
		if($userinfo['sid']){//获取上级的用户名 且开启自动加好友的
			$data['info']['susername']		=	$this->where(array(['id','=',$userinfo['sid']],['state','=',1],['is_auto_f','=',1]))->value('username');
		}
		return $data;
	}
	
	/*
		获取统计信息
	*/
	public function getStatisticsInfo(){
		$param		= input('param.');
		$token		= $param['token'];
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];
		
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		$userinfo	= $this->where('id', $uid)->find();
		if(!$userinfo){
			$data['code'] = 2;
			if($lang=='cn')	$data['code_dec'] = '用户名不存在';
			elseif($lang=='en') $data['code_dec'] 	= 'Username does not exist!';
			elseif($lang=='id') $data['code_dec']	= 'nama pengguna tidak ada';
			elseif($lang=='ft') $data['code_dec']	= '用戶名不存在';
			elseif($lang=='yd') $data['code_dec']	= 'उपयोक्ता नाम मौजूद नहीं है';
			elseif($lang=='vi') $data['code_dec']	= 'tên người dùng không tồn tại';
			elseif($lang=='es') $data['code_dec']	= 'El nombre de usuario no existe';
			elseif($lang=='ja') $data['code_dec']	= 'ユーザ名が存在しません';
			elseif($lang=='th') $data['code_dec']	= 'ชื่อผู้ใช้';
			return $data;
		}
		
		$UserGrade	= model('UserGrade')->where('grade', $userinfo['vip_level'])->find();
		if(!$userinfo){
			$data['code'] = 0;
			if($lang=='cn')	$data['code_dec'] = '用户等级不存在';
			elseif($lang=='en') $data['code_dec'] 	= 'User level does not exist!';
			elseif($lang=='id') $data['code_dec']	= 'Tingkat pengguna tidak ada';
			elseif($lang=='ft') $data['code_dec']	= '使用者等級不存在';
			elseif($lang=='yd') $data['code_dec']	= 'प्रयोक्ता स्तर मौजूद नहीं है';
			elseif($lang=='vi') $data['code_dec']	= 'Không có cấp người dùng';
			elseif($lang=='es') $data['code_dec']	= 'Nivel de usuario';
			elseif($lang=='ja') $data['code_dec']	= 'ユーザレベルが存在しません。';
			elseif($lang=='th') $data['code_dec']	= 'ระดับผู้ใช้ไม่มี';
			return $data;
		}
		
		$data	= [];
		
		$data['info']['yesterday_earnings']  			= round($this->earnings(array('type'=>'yesterday_earnings','uid'=>$userinfo['id'])),3);//昨日收益
		$data['info']['today_earnings']  				= round($this->earnings(array('type'=>'today_earnings','uid'=>$userinfo['id'])),3);//今日收益
		$data['info']['week_earnings']  				= round($this->earnings(array('type'=>'week_earnings','uid'=>$userinfo['id'])),3);//本周收益
		$data['info']['month_earnings']  				= round($this->earnings(array('type'=>'month_earnings','uid'=>$userinfo['id'])),3);//本月收益
		$data['info']['last_month_earnings']  			= round($this->earnings(array('type'=>'last_month_earnings','uid'=>$userinfo['id'])),3);//上月收益
		$data['info']['today_o_num']  					= $this->earnings(array('type'=>'today_o_num','uid'=>$userinfo['id']));//今日完成
		$data['info']['today_s_o_num']  				= $UserGrade['number'] - $this->earnings(array('type'=>'today_j_num','uid'=>$userinfo['id']));//今日剩余任务(单)
		$data['info']['total_profit']  					= round($this->earnings(array('type'=>'total_profit','uid'=>$userinfo['id'])),3);//总收益
		
		$data['code']	= 1;
		return $data;
	}

	
	//收益计算
	public function earnings($param){
		
		$t = time();
		switch($param['type']){
			case 'today_f_num':
				$start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
				$end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
				return model('Task')->where(array(['uid','=',$param['uid']],['add_time','>=',$start],['add_time','<=',$end]))->count();
			break;

			case 'today_j_num':
				$start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
				return model('UserDaily')->where(array(['uid','=',$param['uid']],['date','=',$start]))->value('l_t_o_n');
				//$end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
				//return model('UserTask')->where(array(['uid','=',$param['uid']],['add_time','>=',$start],['add_time','<=',$end]))->count();
			break;
			case 'today_o_num'://今天完成的次数
				$start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
				return model('UserDaily')->where(array(['uid','=',$param['uid']],['date','=',$start]))->value('w_t_o_n');
				//$end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
				//return model('UserTask')->where(array(['uid','=',$param['uid']],['status','=',3],['complete_time','>=',$start],['complete_time','<=',$end]))->count();
			break;
			case 'total_profit'://总收益
				return model('UserDaily')->where(array(['uid','=',$param['uid']]))->sum('commission') + model('UserDaily')->where(array(['uid','=',$param['uid']]))->sum('rebate');
			break;
			case 'yesterday_earnings'://昨日收益
				$start 	= mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t))-24*60*60;
				$end 	= mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t))-24*60*60;
				return model('UserDaily')->where(array([['uid','=',$param['uid']],['date','>=',$start],['date','<',$end]]))->sum('commission') + model('UserDaily')->where(array([['uid','=',$param['uid']],['date','>=',$start],['date','<',$end]]))->sum('rebate');
			
			break;
			case 'today_earnings'://今日收益
				$start 	= mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
				$end 	= mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
				return model('UserDaily')->where(array([['uid','=',$param['uid']],['date','>=',$start],['date','<=',$end]]))->sum('commission') + model('UserDaily')->where(array([['uid','=',$param['uid']],['date','>=',$start],['date','<=',$end]]))->sum('rebate');
			break;
			case 'week_earnings'://本周收益
				
				$today = date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d'),date('Y')));
				$today_end = date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1);
				$w = date('w',strtotime($today));
				$start 	= mktime(0,0,0,date('m'),date('d')-$w+1,date('Y'));
				$end 	= mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
				return model('UserDaily')->where(array([['uid','=',$param['uid']],['date','>=',$start],['date','<=',$end]]))->sum('commission') + model('UserDaily')->where(array([['uid','=',$param['uid']],['date','>=',$start],['date','<=',$end]]))->sum('rebate');
			break;
			case 'month_earnings'://本月
			
				$now=time();
				
				$end	=	strtotime('+1 month');
				$start	=	mktime(0,0,0,date('m',$now),1,date('Y',$now));//当前月开始时间
				
				$end	=	mktime(0,0,0,date('m',$end),1,date('Y',$end))-1;//当前月结束时间

				return model('UserDaily')->where(array([['uid','=',$param['uid']],['date','>=',$start],['date','<=',$end]]))->sum('commission') + model('UserDaily')->where(array([['uid','=',$param['uid']],['date','>=',$start],['date','<=',$end]]))->sum('rebate');
			break;
			
			case 'last_month_earnings':
				
				$start 				= mktime(0,0,0,date('m')-1,str_pad(1,2,0,STR_PAD_LEFT),date('Y'));
		
				$last_month_days 	= date('t',strtotime(date('Y').'-'.(date('m')-1).'-'.str_pad(1,2,0,STR_PAD_LEFT)));
	
				$end 		= mktime(0,0,0,date('m')-1,$last_month_days,date('Y'))+24*60*60;
				
				return model('UserDaily')->where(array([['uid','=',$param['uid']],['date','>=',$start],['date','<=',$end]]))->sum('commission') + model('UserDaily')->where(array([['uid','=',$param['uid']],['date','>=',$start],['date','<',$end]]))->sum('rebate');
			break;
			
		}
		
		return 0;
	
	}
	//验证短信接口
	public function checkSlogin(){
		//登录成功后，短信验证
		$param			= input('param.');
		$token			= $param['token'];
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];	//uid
		$username     	= $userArr[1];	//username
		$code 			= $param['code']; //验证码
		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

		//手机号 密码 验证码
		$code =	$param['code'];
		
		$cache_code	= cache('C_Code_'.$username);
		
		if(empty($code) or $code != $cache_code){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec'] = '验证码错误';
			elseif($lang=='en') $data['code_dec'] = 'Verification code error!';
			elseif($lang=='id') $data['code_dec']	= 'Galat kode verifikasi';
			elseif($lang=='ft') $data['code_dec']	= '驗證碼錯誤';
			elseif($lang=='yd') $data['code_dec']	= 'सत्यापन कोड त्रुटि';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi mã kiểm tra';
			elseif($lang=='es') $data['code_dec']	= 'Código de verificación';
			elseif($lang=='ja') $data['code_dec']	= '認証コードエラー';
			elseif($lang=='th') $data['code_dec']	= 'รหัสการตรวจสอบข้อผิดพลาด';
			return $data;
		}
		
		$this->where('id',$uid)->data(array('last_login'=>time(),'last_ip'=>request()->ip(),'login_error'=>0))->setInc('login_number',1);
		cache('C_Code_'.$username,NULL);
		$data['code'] = 1;
		if($lang=='cn') $data['code_dec'] = '验证成功';
		elseif($lang=='en') $data['code_dec'] = 'Verification code succeeded!';
		elseif($lang=='id') $data['code_dec']	= 'Pengesahan berhasil';
			elseif($lang=='ft') $data['code_dec']	= '驗證成功';
			elseif($lang=='yd') $data['code_dec']	= 'वैधीकरण सफल';
			elseif($lang=='vi') $data['code_dec']	= 'Hợp lệ thành công';
			elseif($lang=='es') $data['code_dec']	= 'Verificación exitosa.';
			elseif($lang=='ja') $data['code_dec']	= '認証に成功しました';
			elseif($lang=='th') $data['code_dec']	= 'ตรวจสอบความสำเร็จ';
		return $data;		
	}
	
	
	// 找回密码————验证短信接口
	public function checkSmsResetPw(){
		$phone	= input('post.phone/d');	// 手机号
		$code	= input('post.code/d');		// 验证码
		$lang	= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		$cache_code = cache('C_Code_'.$phone);
		cache('C_CHKSMSLOG_'.$phone,NULL);	// 初始化
		
		if($code!=$cache_code){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec'] = '验证码错误';
			elseif($lang=='en') $data['code_dec'] = 'Verification code error!';
			elseif($lang=='id') $data['code_dec']	= 'Galat kode verifikasi';
			elseif($lang=='ft') $data['code_dec']	= '驗證碼錯誤';
			elseif($lang=='yd') $data['code_dec']	= 'सत्यापन कोड त्रुटि';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi mã kiểm tra';
		    elseif($lang=='es') $data['code_dec']	= 'Código de verificación';
		    elseif($lang=='ja') $data['code_dec']	= '認証コードエラー';
		    elseif($lang=='th') $data['code_dec']	= 'รหัสการตรวจสอบข้อผิดพลาด';
			return $data;
		}

		cache('C_CHKSMSLOG_'.$phone,$phone,600);
		
		$data['code'] = 1;
		if($lang=='cn') $data['code_dec'] = '验证成功';
		elseif($lang=='en') $data['code_dec'] = 'Verification code succeeded!';
		elseif($lang=='id') $data['code_dec']	= 'Pengesahan berhasil';
		elseif($lang=='ft') $data['code_dec']	= '驗證成功';
			elseif($lang=='yd') $data['code_dec']	= 'वैधीकरण सफल';
			elseif($lang=='vi') $data['code_dec']	= 'Hợp lệ thành công';
		elseif($lang=='es') $data['code_dec']	= 'Verificación exitosa.';
		elseif($lang=='ja') $data['code_dec']	= '認証に成功しました';
		elseif($lang=='th') $data['code_dec']	= 'ตรวจสอบความสำเร็จ';
		$data['phone'] = $phone;
		return $data;		
	}
	
	
	/*  找回密码————设置新密码  */
	public function resetPassword(){
		$phone			= input('post.phone/d');
		$password		= input('post.password/s');
		$re_password	= input('post.re_password/s');
		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		// 验证密码是否一致
		if(empty($password) || empty($re_password) || $password != $re_password){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec'] = '新密码输入错误';
			elseif($lang=='en') $data['code_dec'] = 'New password input error!';
			elseif($lang=='id') $data['code_dec']	= 'Galat input kata sandi baru';
			elseif($lang=='ft') $data['code_dec']	= '新密碼輸入錯誤';
			elseif($lang=='yd') $data['code_dec']	= 'नया पासवर्ड इनपुट त्रुटि';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi nhập mật khẩu mới';
			elseif($lang=='es') $data['code_dec']	= 'Error al introducir la nueva contraseña';
			elseif($lang=='ja') $data['code_dec']	= '新しいパスワードの入力エラー';
			elseif($lang=='th') $data['code_dec']	= 'ข้อผิดพลาดในการป้อนรหัสผ่านใหม่';
			return $data;
		}
		
		// 验证手机号
		$cache_phone	= cache('C_CHKSMSLOG_'.$phone);
		if(empty($phone) || empty($cache_phone) || $phone != $cache_phone){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec'] = '手机号码错误';
			elseif($lang=='en') $data['code_dec'] = 'Wrong mobile number!';
			elseif($lang=='id') $data['code_dec']	= 'Nomor ponsel salah';
			elseif($lang=='ft') $data['code_dec']	= '手機號碼錯誤';
			elseif($lang=='yd') $data['code_dec']	= 'गलत मोबाइल संख्या';
			elseif($lang=='vi') $data['code_dec']	= 'Sai số điện thoại';
			elseif($lang=='es') $data['code_dec']	= 'Número equivocado.';
			elseif($lang=='ja') $data['code_dec']	= '携帯番号が間違っています';
			elseif($lang=='th') $data['code_dec']	= 'หมายเลขโทรศัพท์ผิด';
			return $data;
		}
		
		// 验证用户
		$uid	= $this->where('phone',$phone)->value('id');
		
		if(empty($uid)){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec'] = '用户不存在';
			elseif($lang=='en') $data['code_dec'] = 'User does not exist!';
			elseif($lang=='id') $data['code_dec']	= 'pengguna tidak ada';
			elseif($lang=='ft') $data['code_dec']	= '用戶不存在';
			elseif($lang=='yd') $data['code_dec']	= 'उपयोक्ता मौजूद नहीं है';
			elseif($lang=='vi') $data['code_dec']	= 'người dùng không tồn tại';
			elseif($lang=='es') $data['code_dec']	= 'Usuario no existente';
			elseif($lang=='ja') $data['code_dec']	= 'ユーザが存在しません';
			elseif($lang=='th') $data['code_dec']	= 'ผู้ใช้ไม่มี';
			return $data;
		}
		
		// 更新重置密码
		$update_ok	= $this->where('id',$uid)->update(['password'=>auth_code($password,'ENCODE')]);
		
		if(!$update_ok){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec'] = '密码重置失败';
			elseif($lang=='en') $data['code_dec'] = 'Password reset failed!';
			elseif($lang=='id') $data['code_dec']	= 'Reset kata sandi gagal';
			elseif($lang=='ft') $data['code_dec']	= '密碼重置失敗';
			elseif($lang=='yd') $data['code_dec']	= 'पासवर्ड रिसेट असफल';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi đặt lại mật khẩu';
			elseif($lang=='es') $data['code_dec']	= 'Fallo de sustitución';
			elseif($lang=='ja') $data['code_dec']	= 'パスワードのリセットに失敗しました';
			elseif($lang=='th') $data['code_dec']	= 'ล้มเหลวในการตั้งค่ารหัสผ่าน';
			return $data;
		}
		
		$data['code'] = 1;
		if($lang=='cn') $data['code_dec'] = '密码重置成功';
		elseif($lang=='en') $data['code_dec'] = 'Password reset succeeded!';
			elseif($lang=='id') $data['code_dec']	= 'Reset kata sandi berhasil';
			elseif($lang=='ft') $data['code_dec']	= '密碼重置成功';
			elseif($lang=='yd') $data['code_dec']	= 'पासवर्ड पुनरावृत्ति';
			elseif($lang=='vi') $data['code_dec']	= 'Đặt mật khẩu thành công';
			elseif($lang=='es') $data['code_dec']	= 'Reinicio de contraseñas.';
			elseif($lang=='ja') $data['code_dec']	= 'パスワードのリセットに成功しました';
			elseif($lang=='th') $data['code_dec']	= 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว';
		return $data;
	}
	
	
	/*  修改密码  */
	public function changePassword(){
		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));
		$uid			= $userArr[0];
		$username     	= $userArr[1];
		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		$old_password		= input('post.password/s');
		$new_password		= input('post.n_password/s');
		$new_re_password	= input('post.r_password/s');
		
		// 验证密码是否一致
		if(empty($new_password) || empty($new_re_password) || $new_password != $new_re_password){
			$data['code'] = 0;
		    if($lang=='cn') $data['code_dec'] = '新密码输入错误';
		    elseif($lang=='en') $data['code_dec'] = 'New password input error!';
			elseif($lang=='id') $data['code_dec']	= 'Galat input kata sandi baru';
			elseif($lang=='ft') $data['code_dec']	= '新密碼輸入錯誤';
			elseif($lang=='yd') $data['code_dec']	= 'नया पासवर्ड इनपुट त्रुटि';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi nhập mật khẩu mới';
			elseif($lang=='es') $data['code_dec']	= 'Error al introducir la nueva contraseña';
			elseif($lang=='ja') $data['code_dec']	= '新しいパスワードの入力エラー';
			elseif($lang=='th') $data['code_dec']	= 'ข้อผิดพลาดในการป้อนรหัสผ่านใหม่';
			
			return $data;
		}
				
		// 验证用户，并获取原密码
		$o_password	= $this->where(['id'=>$uid,'username'=>$username])->value('password');
		
		if(empty($o_password)){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec'] = '用户不存在';
			elseif($lang=='en') $data['code_dec'] = 'User does not exist!';
			elseif($lang=='id') $data['code_dec']	= 'pengguna tidak ada';
			elseif($lang=='ft') $data['code_dec']	= '用戶不存在';
			elseif($lang=='yd') $data['code_dec']	= 'उपयोक्ता मौजूद नहीं है';
			elseif($lang=='vi') $data['code_dec']	= 'người dùng không tồn tại';
			elseif($lang=='es') $data['code_dec']	= 'Usuario no existente';
			elseif($lang=='ja') $data['code_dec']	= 'ユーザが存在しません';
			elseif($lang=='th') $data['code_dec']	= 'ผู้ใช้ไม่มี';
			return $data;
		}
		
		// 比较原密码是否一致
		$old_password	= auth_code($old_password,'ENCODE');
		
		if($old_password != $o_password){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec'] = '原密码错误';
			elseif($lang=='en') $data['code_dec'] = 'Original password error';
			elseif($lang=='id') $data['code_dec']	= 'Galat sandi asal';
			elseif($lang=='ft') $data['code_dec']	= '原密碼錯誤';
			elseif($lang=='yd') $data['code_dec']	= 'मौलिक पासवर्ड त्रुटि';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi mật khẩu gốc';
			elseif($lang=='es') $data['code_dec']	= 'Contraseña incorrecta';
			elseif($lang=='ja') $data['code_dec']	= '元のパスワードが間違っています';
			elseif($lang=='th') $data['code_dec']	= 'ข้อผิดพลาดรหัสผ่านเดิม';
			
			return $data;
		}
		
		// 更新密码
		$update_ok	= $this->where('id',$uid)->update(['password'=>auth_code($new_password,'ENCODE')]);
		
		if(!$update_ok){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec'] = '密码修改失败';
			elseif($lang=='en') $data['code_dec'] = 'Password modification failed';
			elseif($lang=='id') $data['code_dec']	= 'Modifikasi sandi gagal';
			elseif($lang=='ft') $data['code_dec']	= '密碼修改失敗';
			elseif($lang=='yd') $data['code_dec']	= 'पासवर्ड परिवर्तन असफल';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi sửa đổi mật khẩu';
			elseif($lang=='es') $data['code_dec']	= 'Modificación de contraseña fallida';
			elseif($lang=='ja') $data['code_dec']	= 'パスワードの変更に失敗しました';
			elseif($lang=='th') $data['code_dec']	= 'การเปลี่ยนแปลงรหัสผ่านล้มเหลว';
			
			return $data;
		}
		
		$data['code'] = 1;
		if($lang=='cn') $data['code_dec'] = '密码修改成功';
			elseif($lang=='en') $data['code_dec'] = 'Password changed successfully';
			elseif($lang=='id') $data['code_dec']	= 'Kata sandi berubah dengan sukses';
			elseif($lang=='ft') $data['code_dec']	= '密碼修改成功';
			elseif($lang=='yd') $data['code_dec']	= 'पासवर्ड सफलतापूर्वक परिवर्धित';
			elseif($lang=='vi') $data['code_dec']	= 'Mật khẩu đã thay đổi';
			elseif($lang=='es') $data['code_dec']	= 'Modificación de contraseña';
			elseif($lang=='ja') $data['code_dec']	= 'パスワードの変更に成功しました';
			elseif($lang=='th') $data['code_dec']	= 'การเปลี่ยนแปลงรหัสผ่านเรียบร้อยแล้ว';
		
		return $data;
	}
	
	
	//获取用户信息
	public function getuserinfo(){
		
		$param			= input('param.');
		$token			= $param['token'];
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid
		$username     	= $userArr[1];//username
		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		$userinfo = $this->field('ly_user_total.*,ly_users.*')->join('ly_user_total','ly_users.id=ly_user_total.uid')->where(array('ly_users.id'=>$uid))->findOrEmpty();
		if (!$userinfo) return ['code'=>0];
		//$settingdata                    = model('Setting')->where('id',1)->findOrEmpty();
		$data['code']                   = 1;
		$data['token']                  = $token;
		$data['info']['userid']         = $userinfo['id'];
		$data['info']['uid']            = $userinfo['uid'];
		$data['info']['username']       = $userinfo['username'];
		$data['info']['balance']  		= $userinfo['balance'];		//余额
		$data['info']['total_balance']  = $userinfo['total_balance']; //总资产
		$data['info']['realname']       = $userinfo['realname'];
		$data['info']['state']          = $userinfo['state'];
		$data['info']['dest']  			= $userinfo['dest'];//国家区号
		$data['info']['phone']  		= $userinfo['phone'];//手机号
		$is_realname	=	0;
		if($userinfo['realname'])		$is_realname	=	1;
		$data['info']['is_realname']  					= $is_realname;		//是否实名
		$data['info']['vip_level']  					= $userinfo['vip_level'];
		$UserGrade										= model('UserGrade')->where('grade', $userinfo['vip_level'])->find();
		$data['info']['pump']							=	floatval(number_format($UserGrade['pump']/100,3));//抽水
		$data['info']['number']							=	$UserGrade['number'];//每日任务
		 
		 	$data['info']['yesterday_earnings']  			= round($this->earnings(array('type'=>'yesterday_earnings','uid'=>$userinfo['id'])),3);//昨日收益
		$data['info']['today_earnings']  				= round($this->earnings(array('type'=>'today_earnings','uid'=>$userinfo['id'])),3);//今日收益
		$data['info']['week_earnings']  				= round($this->earnings(array('type'=>'week_earnings','uid'=>$userinfo['id'])),3);//本周收益
		$data['info']['month_earnings']  				= round($this->earnings(array('type'=>'month_earnings','uid'=>$userinfo['id'])),3);//本月收益
		$data['info']['last_month_earnings']  			= round($this->earnings(array('type'=>'last_month_earnings','uid'=>$userinfo['id'])),3);//上月收益
		$data['info']['today_o_num']  					= $this->earnings(array('type'=>'today_o_num','uid'=>$userinfo['id']));//今日完成
		$data['info']['today_s_o_num']  				= $UserGrade['number'] - $this->earnings(array('type'=>'today_j_num','uid'=>$userinfo['id']));//今日剩余任务(单)
		$data['info']['total_profit']  					= round($this->earnings(array('type'=>'total_profit','uid'=>$userinfo['id'])),3);//总收益
		 
		   
		    
		     
		$UserVip	=	model('UserVip')->where(array(['uid','=',$userinfo['id']],['state','=',1],['grade','=',$userinfo['vip_level']]))->find();
		 
		if($UserVip){
			if($lang=='en'){
				$data['info']['useridentity']	=	$UserVip['en_name'];
			}elseif($lang=='cn'){
				$data['info']['useridentity']	=	$UserVip['name'];
			}elseif($lang=='id'){
				$data['info']['useridentity']	=	$UserVip['ydn_name'];
			}elseif($lang=='ft'){
				$data['info']['useridentity']	=	$UserVip['ft_name'];
			}elseif($lang=='yd'){
				$data['info']['useridentity']	=	$UserVip['yd_name'];
			}elseif($lang=='vi'){
				$data['info']['useridentity']	=	$UserVip['yn_name'];
			}elseif($lang=='es'){
				$data['info']['useridentity']	=	$UserVip['xby_name'];
			}elseif($lang=='ja'){
				$data['info']['useridentity']	=	$UserVip['ry_name'];
			}elseif($lang=='th'){
				$data['info']['useridentity']	=	$UserVip['ty_name'];
			}

			$data['info']['stime']				=	date('Y-m-d',$UserVip['stime']);
			$data['info']['etime']				=	date('Y-m-d',$UserVip['etime']);
		}else{
			if($lang=='en'){
				$data['info']['useridentity']	=	$UserGrade['en_name'];
			}elseif($lang=='cn'){
				$data['info']['useridentity']	=	$UserGrade['name'];
			}elseif($lang=='id'){
				$data['info']['useridentity']	=	$UserGrade['ydn_name'];
			}elseif($lang=='ft'){
				$data['info']['useridentity']	=	$UserGrade['ft_name'];
			}elseif($lang=='yd'){
				$data['info']['useridentity']	=	$UserGrade['yd_name'];
			}elseif($lang=='vi'){
				$data['info']['useridentity']	=	$UserGrade['yn_name'];
			}elseif($lang=='es'){
				$data['info']['useridentity']	=	$UserGrade['xby_name'];
			}elseif($lang=='ja'){
				$data['info']['useridentity']	=	$UserGrade['ry_name'];
			}elseif($lang=='th'){
				$data['info']['useridentity']	=	$UserGrade['ty_name'];
			}
			$data['info']['stime']				=	'';
			$data['info']['etime']				=	'';
		}
		
		$data['info']['header'] 				= $userinfo['header'];//头像
		$data['info']['sid'] 					= $userinfo['sid'];//直属上级id
		$data['info']['credit'] 				= $userinfo['credit'];//信用
		$data['info']['idcode'] 				= $userinfo['idcode'];//邀请码
		$data['info']['qq'] 					= $userinfo['qq'];//邀请码
		$data['info']['weixin'] 				= $userinfo['weixin'];//邀请码
		$data['info']['douyin'] 				= $userinfo['douyin'];//邀请码
		$data['info']['kuaishou'] 				= $userinfo['kuaishou'];//邀请码
		$data['info']['alipay'] 				= $userinfo['alipay'];//邀请码
		$data['info']['alipay_name'] 			= $userinfo['alipay_name'];//邀请码
		$data['info']['alipay_names'] 			= $userinfo['alipay_names'];//邀请码
		$data['info']['is_housekeeper'] 		= $userinfo['is_housekeeper'];//云管家
		if($userinfo['fund_password']){
			$data['info']['is_fund_password']				=	1;
		}else{
			$data['info']['is_fund_password']				=	0;
		}
		
		$data['info']['susername']			=	'';
		if($userinfo['sid']){//获取上级的用户名 且开启自动加好友的
			$data['info']['susername']		=	$this->where(array(['id','=',$userinfo['sid']],['state','=',1],['is_auto_f','=',1]))->value('username');
		}
    
		return $data;
	}
	
	//激活
	public function activationUser(){
		
		$param			= input('param.');
		$token			= $param['token'];
		$userArr		= explode(',',auth_code($token,'DECODE'));
		$uid			= $userArr[0];
		$username     	= $userArr[1];
		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		$bankcount		=	model('UserBank')->where('uid',$uid)->count();//绑定银行卡数量
		
		if(!$bankcount){
			$data['code'] = 4;
			if($lang=='cn')	$data['code_dec']	= '未绑定银行卡';
			elseif($lang=='en') $data['code_dec'] 	= 'Unbound bank card';
			elseif($lang=='id') $data['code_dec']	= 'Kartu bank tidak terikat';
			elseif($lang=='ft') $data['code_dec']	= '未綁定銀行卡';
			elseif($lang=='yd') $data['code_dec']	= 'अबाइंड बैंक कार्ड';
			elseif($lang=='vi') $data['code_dec']	= 'Thẻ ngân hàng';
			elseif($lang=='es') $data['code_dec']	= 'Tarjeta bancaria no vinculada';
			elseif($lang=='ja') $data['code_dec']	= '銀行カードを紐付けていません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่ผูกบัตรธนาคาร';
			return $data;
		}

		//用户余额
		$userTotaldata	= 	model('UserTotal')->where('uid' , $uid)->find();
		$usersbalance 	=	$userTotaldata['balance'];
		
		//激活金额
		$activation_balance		= model('Setting')->where('id',1)->value('activation_balance');
		//如果是0 直接激活
		if($activation_balance==0){
			$this->where('id' , $uid)->update(array('state'=>1,'at_time'=>time()));
		}
		
		if($usersbalance < $activation_balance){
			$data['code'] = 7;
			if($lang=='cn')	$data['code_dec']	= '余额不足';
			elseif($lang=='en') $data['code_dec'] 	= 'Sorry, your credit is running low';
			elseif($lang=='id') $data['code_dec']	= 'Maaf, kreditmu kehabisan';
			elseif($lang=='ft') $data['code_dec']	= '餘額不足';
			elseif($lang=='yd') $data['code_dec']	= 'माफ़ करें, आपका क्रेडिट कम चल रहा है';
			elseif($lang=='vi') $data['code_dec']	= 'Xin lỗi, tín dụng của anh đang cạn dần';
			elseif($lang=='es') $data['code_dec']	= 'Saldo insuficiente';
			elseif($lang=='ja') $data['code_dec']	= '残高が足りない';
			elseif($lang=='th') $data['code_dec']	= 'ขาดสมดุล';
			return $data;
		}
			
		//扣钱激活
		
		$is_updata_user = model('UserTotal')->where('uid' , $uid)->Dec('balance', $activation_balance)->update();

		if (!$is_updata_user) {
			$data['code'] = 0;
			if($lang=='cn')	$data['code_dec']	= '激活失败';
			elseif($lang=='en') $data['code_dec'] 	= 'Activation failed';
			elseif($lang=='id') $data['code_dec']	= 'Aktifasi gagal';
			elseif($lang=='ft') $data['code_dec']	= '啟動失敗';
			elseif($lang=='yd') $data['code_dec']	= 'सक्रिय असफल';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi khởi động';
			elseif($lang=='es') $data['code_dec']	= 'Fallo de activación';
			elseif($lang=='ja') $data['code_dec']	= 'アクティブ化に失敗しました';
			elseif($lang=='th') $data['code_dec']	= 'เปิดใช้งานล้มเหลว';
			return $data;
		}
		
		//更新用户状态
		$is_u = $this->where('id' , $uid)->update(array('state'=>1,'at_time'=>time()));
		if(!$is_u){
			$data['code'] = 0;
			if($lang=='cn')	$data['code_dec']	= '激活失败';
			elseif($lang=='en') $data['code_dec'] 	= 'Activation failed';
			elseif($lang=='id') $data['code_dec']	= 'Aktifasi gagal';
			elseif($lang=='ft') $data['code_dec']	= '啟動失敗';
			elseif($lang=='yd') $data['code_dec']	= 'सक्रिय असफल';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi khởi động';
			elseif($lang=='es') $data['code_dec']	= 'Fallo de activación';
			elseif($lang=='ja') $data['code_dec']	= 'アクティブ化に失敗しました';
			elseif($lang=='th') $data['code_dec']	= 'เปิดใช้งานล้มเหลว';
			//把钱还回去
			model('UserTotal')->where('uid' , $uid)->Inc('balance', $activation_balance)->update();
			return $data;
		}
		
		$order_number = 'D'.trading_number();
		$trade_number = 'L'.trading_number();

		//流水
		$financial_data['uid'] 						= $uid;
		$financial_data['username'] 				= $username;
		$financial_data['order_number'] 			= $order_number;
		$financial_data['trade_number'] 			= $trade_number;
		$financial_data['trade_type'] 				= 2;
		$financial_data['account_frozen_balance']	= $userTotaldata['frozen_balance'];
		$financial_data['trade_before_balance']		= $usersbalance;
		$financial_data['trade_amount'] 			= $activation_balance;
		$financial_data['account_balance'] 			= $usersbalance - $activation_balance;
		$financial_data['remarks'] 					= '激活账号';
		$financial_data['types'] 					= 1;
		$is_f = model('TradeDetails')->tradeDetails($financial_data);
		if(!$is_f){
			$data['code'] = 0;
			if($lang=='cn')	$data['code_dec']	= '激活失败';
			elseif($lang=='en') $data['code_dec'] 	= 'Activation failed';
			elseif($lang=='id') $data['code_dec']	= 'Aktifasi gagal';
			elseif($lang=='ft') $data['code_dec']	= '啟動失敗';
			elseif($lang=='yd') $data['code_dec']	= 'सक्रिय असफल';
			elseif($lang=='vi') $data['code_dec']	= 'Lỗi khởi động';
			elseif($lang=='es') $data['code_dec']	= 'Fallo de activación';
			elseif($lang=='ja') $data['code_dec']	= 'アクティブ化に失敗しました';
			elseif($lang=='th') $data['code_dec']	= 'เปิดใช้งานล้มเหลว';
			$this->where('id' , $uid)->update(array('state'=>2));
			//把钱还回去
			model('UserTotal')->where('uid' , $uid)->Inc('balance', $activation_balance)->update();
			return $data;
		}
		
		$data['code']		= 1;
		if($lang=='cn')	$data['code_dec']	= '激活成功';
			elseif($lang=='en') $data['code_dec'] 	= 'Activation successful';
			elseif($lang=='id') $data['code_dec']	= 'Aktivasi berhasil';
			elseif($lang=='ft') $data['code_dec']	= '啟動成功';
			elseif($lang=='yd') $data['code_dec']	= 'सक्रिय सफल';
			elseif($lang=='vi') $data['code_dec']	= 'Khởi động thành công';
			elseif($lang=='es') $data['code_dec']	= 'Activar con éxito';
			elseif($lang=='ja') $data['code_dec']	= 'アクティブ化成功';
			elseif($lang=='th') $data['code_dec']	= 'เปิดใช้งานเรียบร้อยแล้ว';
		return $data;
	}
	
	
	//设置用户信息
	public function setuserinfo(){
		$param			= input('param.');
		$token			= $param['token'];
		$userArr		= explode(',',auth_code($token,'DECODE'));
		$uid			= $userArr[0];
		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		unset($param['token']);
		unset($param['lang']);
		if(!$param){
			if($lang=='cn') return ['code' => 0, 'code_dec' => '未提交数据'];
			elseif($lang=='en') return ['code' => 0, 'code_dec' => 'Data not submitted!'];
			elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Data tidak dikirim'];
			elseif($lang=='ft') return ['code' => 0, 'code_dec' => '未提交數據'];
			elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'डाटा भेजा नहीं गया'];
			elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Chưa đệ trình dữ liệu'];
			elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Datos no presentados'];
			elseif($lang=='ja') return ['code' => 0, 'code_dec' => '未送信データ'];
			elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ไม่ส่งข้อมูล'];
		}
		
		// 获取用户信息
		$chk_user	= $this->where('id',$uid)->count();
		if(!$chk_user)	
			if($lang=='cn') return ['code' => 0, 'code_dec' => '用户不存在'];
			elseif($lang=='en') return ['code' => 0, 'code_dec' => 'user does not exist'];
			elseif($lang=='id') return ['code' => 0, 'code_dec' => 'pengguna tidak ada'];
			elseif($lang=='ft') return ['code' => 0, 'code_dec' => '用戶不存在'];
			elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'उपयोक्ता मौजूद नहीं है'];
			elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'người dùng không tồn tại'];
			elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Usuario no existente'];
			elseif($lang=='ja') return ['code' => 0, 'code_dec' => 'ユーザが存在しません'];		
		    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ผู้ใช้ไม่มี'];	
		
		/* 更改支付密码——开始 */
		$o_payword		= input('post.o_payword/s');	//旧支付密码
		$n_payword		= input('post.n_payword/s');	//新支付密码
		$r_payword		= input('post.r_payword/s');	//确认密码
		
		// 设置支付密码
		if(!$o_payword && $n_payword && $r_payword){		// 如果o_payword不为空，则修改密码
			
			if(empty($n_payword) || empty($r_payword)) 
			if($lang=='cn') return ['code'=>0, 'code_dec'=>'新密码不能为空'];	// 检测新密码不能为空
			elseif($lang=='en') return ['code' => 0, 'code_dec' => 'New password cannot be empty'];
			elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Kata sandi baru tidak dapat kosong'];
			elseif($lang=='ft') return ['code' => 0, 'code_dec' => '新密碼不能為空'];
			elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'नया पासवर्ड खाली नहीं हो सकता'];
			elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Không thể bỏ mật khẩu mới'];
			elseif($lang=='es') return ['code' => 0, 'code_dec' => 'La nueva contraseña no está vacía.'];
			elseif($lang=='ja') return ['code' => 0, 'code_dec' => '新しいパスワードは空にできません。'];				
			elseif($lang=='th') return ['code' => 0, 'code_dec' => 'รหัสผ่านใหม่ไม่สามารถว่างเปล่า'];
			
			if($n_payword != $r_payword) 
			if($lang=='cn') return ['code'=>0, 'code_dec'=>'新支付密码不一致'];				// 检测新支付密码不一致
			elseif($lang=='en') return ['code' => 0, 'code_dec' => 'The new payment password is inconsistent'];
			elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Kata sandi pembayaran baru tidak konsisten'];
			elseif($lang=='ft') return ['code' => 0, 'code_dec' => '新支付密碼不一致'];
			elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'नया पैलेश पासवर्ड संस्थिर नहीं है'];
			elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Mật khẩu thanh toán mới không khớp'];
			elseif($lang=='es') return ['code' => 0, 'code_dec' => 'La nueva contraseña de pago es inconsistente.'];
			elseif($lang=='ja') return ['code' => 0, 'code_dec' => '新しく支払うパスワードが一致しません。'];
			elseif($lang=='th') return ['code' => 0, 'code_dec' => 'รหัสผ่านการชำระเงินใหม่ไม่ตรงกัน'];
			
			// 检测旧密码是否为空，为空则设置密码
			$db_pw	= $this->where('id', $uid)->value('fund_password');
			if(!$db_pw){
				$is_ok	= $this->where('id', $uid)->update(['fund_password'=>auth_code($n_payword, 'ENCODE')]);
				if($is_ok) 
			if($lang=='cn') return ['code'=>1, 'code_dec'=>'支付密码设置成功'];
			elseif($lang=='en') return ['code' => 1, 'code_dec' => 'Payment password set successfully'];
			elseif($lang=='id') return ['code' => 1, 'code_dec' => 'Sandi pembayaran ditetapkan dengan sukses'];
			elseif($lang=='ft') return ['code' => 1, 'code_dec' => '支付密碼設定成功'];
			elseif($lang=='yd') return ['code' => 1, 'code_dec' => 'पैमेंट पासवर्ड सफलतापूर्वक सेट'];
			elseif($lang=='vi') return ['code' => 1, 'code_dec' => 'Mã thanh toán thành công'];
			elseif($lang=='es') return ['code' => 1, 'code_dec' => 'Configuración de la contraseña de pago'];
			elseif($lang=='ja') return ['code' => 1, 'code_dec' => '支払いパスワードの設定に成功しました。'];				
			elseif($lang=='th') return ['code' => 1, 'code_dec' => 'จ่ายสำหรับการตั้งค่ารหัสผ่านเรียบร้อยแล้ว'];	
			}else{
				if($lang=='cn') return ['code'=>0, 'code_dec'=>'存在旧密码'];
				elseif($lang=='en') return ['code'=>0, 'code_dec'=>'Old password exists!'];
				elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Kata sandi lama ada'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '存在舊密碼'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'पुराना पासवर्ड मौजूद है'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Có mật khẩu cũ'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Código antiguo'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => '古いパスワードがあります'];
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'มีรหัสผ่านเก่า'];
			}
		}
		// 修改支付密码
		if($o_payword && $n_payword && $r_payword){		// 如果支付不为null，修改密码
			if(empty($n_payword) || empty($r_payword)) 
				if($lang=='cn') return ['code'=>0, 'code_dec'=>'新密码不能为空'];	// 检测新密码不能为空
				elseif($lang=='en') return ['code'=>0, 'code_dec'=>'New password cannot be empty'];
				elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Kata sandi baru tidak dapat kosong'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '新密碼不能為空'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'नया पासवर्ड खाली नहीं हो सकता'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Không thể bỏ mật khẩu mới'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'La nueva contraseña no está vacía.'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => '新しいパスワードは空にできません。'];			
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'รหัสผ่านใหม่ไม่สามารถว่างเปล่า'];
			
			
			if($n_payword != $r_payword) 
				if($lang=='cn') return ['code'=>0, 'code_dec'=>'新支付密码不一致'];				// 检测新支付密码不一致
				elseif($lang=='en') return ['code'=>0, 'code_dec'=>'The new payment password is inconsistent'];
				elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Kata sandi pembayaran baru tidak konsisten'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '新支付密碼不一致'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'नया पैलेश पासवर्ड संस्थिर नहीं है'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Mật khẩu thanh toán mới không khớp'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'La nueva contraseña de pago es inconsistente.'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => '新しく支払うパスワードが一致しません。'];			
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'รหัสผ่านการชำระเงินใหม่ไม่ตรงกัน'];
			
			// 检测旧密码是否输入正确
			$db_pw	= $this->where('id', $uid)->value('fund_password');
			$db_pw	= auth_code($db_pw, 'DECODE');
			if($o_payword != $db_pw){
				if($lang=='cn') return ['code'=>0, 'code_dec'=>'旧支付密码错误'];	// 检测旧密码是否与数据表里的一致，不一致，则返回错误代码
				elseif($lang=='en') return ['code'=>0, 'code_dec'=>'Old payment password error'];
				elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Galat kata sandi pembayaran lama'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '舊支付密碼錯誤'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'पुराना पैसा पासवर्ड त्रुटि'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Lỗi mật khẩu cũ'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Antiguo Código de pago'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => '古いパスワードの支払いエラー'];
				elseif($lang=='th') return ['code' => 0, 'code_dec' => 'รหัสผ่านเก่า'];
			}else{	// 旧密码正确，则更新为新密码
				$is_ok	= $this->where('id', $uid)->update(['fund_password'=>auth_code($n_payword, 'ENCODE')]);
				if($is_ok) 
				if($lang=='cn') return ['code'=>1, 'code_dec'=>'支付密码修改成功'];
				elseif($lang=='en') return ['code'=>1, 'code_dec'=>'Payment password modified successfully'];
				elseif($lang=='id') return ['code' => 1, 'code_dec' => 'Kata sandi pembayaran berhasil diubah'];
			    elseif($lang=='ft') return ['code' => 1, 'code_dec' => '支付密碼修改成功'];
			    elseif($lang=='yd') return ['code' => 1, 'code_dec' => 'पैमेंट पासवर्ड सफलतापूर्वक परिवर्धित'];
			    elseif($lang=='vi') return ['code' => 1, 'code_dec' => 'Thanh toán Comment'];
			    elseif($lang=='es') return ['code' => 1, 'code_dec' => 'Modificación de la contraseña de pago'];
			    elseif($lang=='ja') return ['code' => 1, 'code_dec' => 'パスワードを支払って変更しました。'];				
				elseif($lang=='th') return ['code' => 1, 'code_dec' => 'จ่ายสำหรับการเปลี่ยนแปลงรหัสผ่านเรียบร้อยแล้ว'];
			}
		}
		/* 更改支付密码——结束 */
		
		/* 认证实名——开始 */
		if(isset($param['realname']) && empty($param['realname'])){		// 实名认证不能未传数据
			if($lang=='cn') return ['code'=>0, 'code_dec'=>'实名认证失败'];
			elseif($lang=='en') return ['code'=>0, 'code_dec'=>'Real name authentication failed!'];
			elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Autentikasi nama asli gagal'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '實名認證失敗'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'वास्तविक नाम प्रमाणीकरण असफल'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Lỗi xác thực tên thật'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Fallo de autenticación'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => '実名認証に失敗しました'];
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ล้มเหลวในการตรวจสอบชื่อจริง'];
		}
		if(isset($param['realname']) && !empty($param['realname'])&& !isset($param['qq'])){		// 实名认证，不允许曾经已认证过
			$db_realname	= $this->where('id', $uid)->value('realname');
			if($db_realname){
				if($lang=='cn') return ['code'=>0, 'code_dec'=>'实名认证失败'];
				elseif($lang=='en') return ['code'=>0, 'code_dec'=>'Real name authentication failed!'];
			elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Autentikasi nama asli gagal'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '實名認證失敗'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'वास्तविक नाम प्रमाणीकरण असफल'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Lỗi xác thực tên thật'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Fallo de autenticación'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => '実名認証に失敗しました'];
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ล้มเหลวในการตรวจสอบชื่อจริง'];
			}
		}
		/*if(isset($param['realname'])){
			//正则身份证号
			//$rule	= "/^[1-9]\d{5}(19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/i";	//正则规则		
			//if(!preg_match($rule, $identity_id)) return ['code' => 0, 'code_dec' => '身份证号错误'];
			//$is_ok	= $this->where('id', $uid)->update(['realname'=>$realname, 'qq'=>$qq]);
			//if($is_ok) return ['code'=>1, 'code_dec'=>'实名认证成功'];
			//else return ['code'=>0, 'code_dec'=>'实名认证失败'];			
		}*/
		/* 认证实名——结束 */
		
		/* 更改登录密码——开始 */
		$o_password	= input('post.o_password/s');	//旧登录密码
		$n_password	= input('post.n_password/s');	//新登录密码
		$r_password	= input('post.r_password/s');	//确认密码
				
		if($o_password && $n_password && $r_password){
			if(empty($n_password) || empty($r_password)) 
			if($lang=='cn') return ['code'=>0, 'code_dec'=>'新密码不能为空'];	// 检测新密码不能为空
			elseif($lang=='en') return ['code' => 0, 'code_dec' => 'New password cannot be empty'];
			elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Kata sandi baru tidak dapat kosong'];
			elseif($lang=='ft') return ['code' => 0, 'code_dec' => '新密碼不能為空'];
			elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'नया पासवर्ड खाली नहीं हो सकता'];
			elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Không thể bỏ mật khẩu mới'];
			elseif($lang=='es') return ['code' => 0, 'code_dec' => 'La nueva contraseña no está vacía.'];
			elseif($lang=='ja') return ['code' => 0, 'code_dec' => '新しいパスワードは空にできません。'];
			elseif($lang=='th') return ['code' => 0, 'code_dec' => 'รหัสผ่านใหม่ไม่สามารถว่างเปล่า'];
			
			if($n_password != $r_password) 
			if($lang=='cn') return ['code'=>0, 'code_dec'=>'新密码不一致'];				// 检测新密码不一致
			elseif($lang=='en') return ['code' => 0, 'code_dec' => 'The new password is inconsistent'];
			elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Kata sandi baru tidak konsisten'];
			elseif($lang=='ft') return ['code' => 0, 'code_dec' => '新密碼不一致'];
			elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'नया पासवर्ड संस्थित नहीं है'];
			elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Mật khẩu mới không khớp'];
			elseif($lang=='es') return ['code' => 0, 'code_dec' => 'La nueva contraseña es inconsistente.'];
			elseif($lang=='ja') return ['code' => 0, 'code_dec' => '新しいパスワードが一致しません'];			
			elseif($lang=='th') return ['code' => 0, 'code_dec' => 'รหัสผ่านใหม่ไม่ตรงกัน'];
			
			// 检测旧密码是否输入正确
			$db_pw	= $this->where('id', $uid)->value('password');
			$db_pw	= auth_code($db_pw, 'DECODE');
			if($o_password != $db_pw){
			if($lang=='cn') return ['code'=>0, 'code_dec'=>'旧登录密码错误'];	// 检测旧密码是否与数据表里的一致，不一致，则返回错误代码
			elseif($lang=='en') return ['code' => 0, 'code_dec' => 'Old login password error'];
			elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Galat kata sandi log masuk lama'];
			elseif($lang=='ft') return ['code' => 0, 'code_dec' => '舊登入密碼錯誤'];
			elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'पुराना लॉगइन पासवर्ड त्रुटि'];
			elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Lỗi mật khẩu cũ'];
			elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Error de contraseña'];
			elseif($lang=='ja') return ['code' => 0, 'code_dec' => '旧ログインパスワードエラー'];			    
			elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ข้อผิดพลาดรหัสผ่านเข้าสู่ระบบเก่า'];	
			}else{	// 旧密码正确，则更新为新密码
				$is_ok = $this->where('id', $uid)->update(['password'=>auth_code($n_password, 'ENCODE')]);
				if($is_ok) 
			if($lang=='cn') return ['code' => 1, 'code_dec'	=> '密码修改成功'];
			elseif($lang=='en') return ['code' => 1, 'code_dec' => 'Password changed successfully'];
			elseif($lang=='id') return ['code' => 1, 'code_dec' => 'Kata sandi berubah dengan sukses'];
			elseif($lang=='ft') return ['code' => 1, 'code_dec' => '密碼修改成功'];
			elseif($lang=='yd') return ['code' => 1, 'code_dec' => 'पासवर्ड सफलतापूर्वक परिवर्धित'];
			elseif($lang=='vi') return ['code' => 1, 'code_dec' => 'Mật khẩu đã thay đổi'];
			elseif($lang=='es') return ['code' => 1, 'code_dec' => 'Modificación de contraseña'];
			elseif($lang=='ja') return ['code' => 1, 'code_dec' => 'パスワードの変更に成功しました'];				
			elseif($lang=='th') return ['code' => 1, 'code_dec' => 'การเปลี่ยนแปลงรหัสผ่านเรียบร้อยแล้ว'];	
				else 
			if($lang=='cn') return ['code'=>0, 'code_dec'=>'密码修改失败'];
			elseif($lang=='en') return ['code'=>0, 'code_dec'=>'Password modification failed'];
			elseif($lang=='id') return ['code'=>0, 'code_dec'=>'Modifikasi sandi gagal'];
			elseif($lang=='ft') return ['code'=>0, 'code_dec'=>'密碼修改失敗'];
			elseif($lang=='yd') return ['code'=>0, 'code_dec'=>'पासवर्ड परिवर्तन असफल'];
			elseif($lang=='vi') return ['code'=>0, 'code_dec'=>'Lỗi sửa đổi mật khẩu'];
			elseif($lang=='es') return ['code'=>0, 'code_dec'=>'Modificación de contraseña fallida'];
			elseif($lang=='ja') return ['code'=>0, 'code_dec'=>'パスワードの変更に失敗しました'];	
            elseif($lang=='th') return ['code'=>0, 'code_dec'=>'การเปลี่ยนแปลงรหัสผ่านล้มเหลว'];
			}
		}
		/* 更改登录密码——结束 */
		
		/* 绑定支付宝——开始 */
		$alipay		= input('post.alipay/s');
		$alipay_name	= input('post.alipay_name/s');
		$alipay_names	= input('post.alipay_names/s');
		if(isset($alipay) && isset($alipay_name) && isset($alipay_names)){	// 设置了支付宝的参数，并有值
			if(empty($alipay) || empty($alipay_name) || empty($alipay_names)){
				if($lang=='cn')	return ['code'=>0, 'code_dec'=>'支付宝参数不能为空'];
				else return ['code'=>0, 'code_dec'=>'Alipay parameter can not be empty!'];
			}
			/*$realname	= $this->where('id', $uid)->value('realname');
			if($realname != $alipay_name){
				if($lang=='cn') return ['code'=>0, 'code_dec'=>'未实名认证或认证姓名不一致'];
				else return ['code'=>0, 'code_dec'=>'Real name authentication is required!'];
			}*/
			//if($lang=='cn') return ['code'=>0, 'code_dec'=>'暂未开放，敬请期待!'];
			//else return ['code'=>0, 'code_dec'=>'NOT YET OPEN!'];
			$param	= [];
			$param['alipay']		= $alipay;
			$param['alipay_name']	= $alipay_name;
			$param['alipay_names']	= $alipay_names;
		}
		/* 绑定支付宝——结束 */
		//开通云管家
		$userinfo = $this->where('id', $uid)->find();
		if(isset($param['is_housekeeper']) && $param['is_housekeeper'] && $userinfo['is_housekeeper']==0){
			if($userinfo['vip_level']<5){
				if($lang=='cn'){
					return ['code' => 0, 'code_dec' => '最低必须是奥斯卡才可以开通此服务'];
				}else{
					return ['code' => 0, 'code_dec' => 'At least Oscar is required to open this service !'];
				}
			}
			$amount	=	99;
			// 检测用户的余额
			$userBalance	= model('UserTotal')->where('uid', $uid)->value('balance');	// 获取用户的余额
			if($amount > $userBalance){
				if($lang=='cn'){
					return ['code' => 2, 'code_dec' => '失败'];
				}else{
					return ['code' => 2, 'code_dec' => 'Fail'];
				}
			}
			$isDecBalance	= model('UserTotal')->where('uid', $uid)->setDec('balance', $amount);
			if(!$isDecBalance){
				if($lang=='cn'){
					return ['code' => 0, 'code_dec' => '失败'];
				}else{
					return ['code' => 0, 'code_dec' => 'Fail'];
				}
			}
			// 流水
			$order_number = 'B'.trading_number();
			$trade_number = 'L'.trading_number();
			$financial_data['uid'] 						= $userinfo['id'];
			$financial_data['username'] 				= $userinfo['username'];
			$financial_data['order_number'] 			= $order_number;
			$financial_data['trade_number'] 			= $trade_number;
			$financial_data['trade_type'] 				= 13;
			$financial_data['trade_before_balance']		= $userBalance;
			$financial_data['trade_amount'] 			= $amount;
			$financial_data['account_balance'] 			= $userBalance - $amount;
			$financial_data['remarks'] 					= '购买云管家';
			$financial_data['types'] 					= 1;	// 用户1，商户2
			model('TradeDetails')->tradeDetails($financial_data);
			$param['housekeeper_time']	=	strtotime(date("Y-m-d",time())) + 30 * 24 * 3600;
		}

		// 存储相应的数据到表中
		$aff = $this->allowField(true)->save($param, ['id' => $uid]);		
		if (!$aff){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
				return ['code' => 0, 'code_dec' => 'Fail'];
			}elseif($lang=='id'){
				return ['code' => 0, 'code_dec' => 'gagal'];
			}elseif($lang=='ft'){
				return ['code' => 0, 'code_dec' => '失敗'];
			}elseif($lang=='yd'){
				return ['code' => 0, 'code_dec' => 'असफल'];
			}elseif($lang=='vi'){
				return ['code' => 0, 'code_dec' => 'hỏng'];
			}elseif($lang=='es'){
				return ['code' => 0, 'code_dec' => 'Fracaso'];
			}elseif($lang=='ja'){
				return ['code' => 0, 'code_dec' => '失敗'];
			}elseif($lang=='th'){
				return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
			}
		}
			if($lang=='cn'){
				return ['code' => 1, 'code_dec' => '成功'];
			}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}		
	}
	
	
	//退出登陆
	public function logout(){
		$token		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

		cache('C_token_'.$uid,NULL);	//删除登录缓存
			if($lang=='cn') $data['code_dec']	= '退出成功';
			elseif($lang=='en') $data['code_dec']	= 'Exit succeeded';
			elseif($lang=='id') $data['code_dec']	= 'Keluar berhasil';
			elseif($lang=='ft') $data['code_dec']	= '退出成功';
			elseif($lang=='yd') $data['code_dec']	= 'बाहर होने का सफल';
			elseif($lang=='vi') $data['code_dec']	= 'Thoát thành công';
			elseif($lang=='es') $data['code_dec']	= 'Salida exitosa.';
			elseif($lang=='ja') $data['code_dec']	= 'ログアウト成功';
			elseif($lang=='th') $data['code_dec']	= 'ถอนตัวจากความสำเร็จ';
		$data['code'] 		= 1;
		return $data;
	}
	
	
	/**
	 * [userSid 获取所有上级]
	 * @param  [type] $id     [description]
	 * @param  string $select [description]
	 * @param  array  $array  [description]
	 * @return [type]         [description]
	 */
	public function userSid($id,$select='id,sid',$array=array()){
		$user_info =  $this->where('id',$id)->field($select)->find();	//查询上级

		$array[] = $user_info['id'];

		if($user_info['sid']){
			$array = $this->userSid($user_info['sid'],$select,$array);
		}

		return $array;
	}
	
	
	
	//领取佣金
	public function receiveCommission(){
		//佣金 ls=流水 fd=返点 rj=佣金
		$commissionData =  array(
			array('ls'=>2,'fd'=>5,'rj'=>10),
			array('ls'=>5,'fd'=>10,'rj'=>50),
			array('ls'=>10,'fd'=>15,'rj'=>150),
			array('ls'=>20,'fd'=>20,'rj'=>400),
			array('ls'=>40,'fd'=>25,'rj'=>1000),
			array('ls'=>80,'fd'=>30,'rj'=>2400),
			array('ls'=>140,'fd'=>40,'rj'=>5600),
			array('ls'=>230,'fd'=>50,'rj'=>11500),
			array('ls'=>350,'fd'=>60,'rj'=>21000),
			array('ls'=>550,'fd'=>70,'rj'=>38500),
			array('ls'=>800,'fd'=>80,'rj'=>64000),
		);
		$param			=	input('param.');
		$token			=	$param['token'];
		$userArr		=	explode(',',auth_code($token,'DECODE'));
		$uid			=	$userArr[0];
		$username     	=	$userArr[1];
		
		
		
		
	}		

	//我的团队
	public function userTeamTotal(){
		$param			=	input('param.');
		$token			=	$param['token'];
		$userArr		=	explode(',',auth_code($token,'DECODE'));
		$uid			=	$userArr[0];
		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		// 今日事件
		$today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$todayEnd = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
		// 今日团队业绩
		$dateTeamTotal = model('UserDaily')->join('user_team', 'ly_user_daily.uid = user_team.team')->where(array(['user_team.uid','=',$uid],['ly_user_daily.date','>=',$today],['ly_user_daily.date','<=',$todayEnd]))->sum('order');
		// 团队成员信息
		$teamUserInfo = $this->field('ly_users.reg_time,state,at_time')->join('user_team', 'ly_users.id = user_team.team')->where('user_team.uid', $uid)->select()->toArray();
		if(!count($teamUserInfo)){
				if($lang=='cn') return ['code'=>0, 'code_dec'=>'无团队成员'];
				elseif($lang=='en') return ['code'=>0, 'code_dec'=>'No team member'];
			    elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Tidak ada anggota tim'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '無團隊成員'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'कोई टीम सदस्य नहीं'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Không thành viên nhóm'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Ningún miembro del equipo'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => 'チームメンバーなし'];		    
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ไม่มีสมาชิกทีม'];
		}
		// 团队激活人数，单位：万
		$TeamActivationNumber     = 0;
		// 今日新增
		$DateTeamNumber           = 0;
		// 今日激活
		$DateTeamActivationNumber = 0;
		// 提取
		foreach ($teamUserInfo as $key => $value) {
			if ($value['state'] == 1) $TeamActivationNumber++;
			if ($value['reg_time'] >= $today && $value['reg_time'] <= $todayEnd) $DateTeamNumber++;
			if ($value['at_time'] >= $today && $value['at_time'] <= $todayEnd) $DateTeamActivationNumber++;
		}

		$data = array(
			'DateTeamTotal'					=>	$dateTeamTotal,
			'TeamNumber'					=>	count($teamUserInfo),
			'TeamActivationNumber'			=>	$TeamActivationNumber,
			'DateTeamNumber'				=>	$DateTeamNumber,
			'DateTeamActivationNumber'		=>	$DateTeamActivationNumber,
		);
		return $data;
	}
	
	
	//我的下级
	public function userTeamlist(){
		$param    =	input('param.');
		$userArr  =	explode(',',auth_code($param['token'],'DECODE'));
		$uid      =	$userArr[0];
		$lang	  = (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

		// 初始化查询条件
		$where[] = ['sid','=',$uid];
		// 搜索用户名
		if (isset($param['username']) && $param['username']) {
			$where[] = ['username','=',trim($param['username'])];
		}
		// 搜索状态
		if (isset($param['is_at']) && $param['is_at']) {
			$where[] = ['state','=',$param['is_at']];
		}
		// 总记录数
		$count       = $this->where($where)->count();
		if (!$count) 
				if($lang=='cn') return ['code' => 0, 'code_dec' => '暂无数据'];
				elseif($lang=='en') return ['code'=>0, 'code_dec'=>'No data available'];
			    elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Tidak ada data tersedia'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '暫無數據'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'कोई डाटा उपलब्ध नहीं'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Không có dữ liệu'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Datos no disponibles'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => 'データがありません'];		
		        elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ไม่มีข้อมูล'];
		// 每页显示记录
		$pageSize    = (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		// 当前的页,还应该处理非数字的情况
		$pageNo      = (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		// 总页数
		$pageTotal   = ceil($count / $pageSize);//当前页数大于最后页数，取最后	
		// 记录数
		$limitOffset = ($pageNo - 1) * $pageSize;
		// 获取直属下级用户数据
		$teamUserInfo = $this->field('id,uid,realname,at_time,alipay_fee,wechat_fee,bank_fee,state')->where($where)->order('at_time', 'desc')->limit($limitOffset, $pageSize)->select()->toArray();
		if (!count($teamUserInfo)) 
				if($lang=='cn') return ['code' => 0, 'code_dec' => '暂无数据'];
				elseif($lang=='en') return ['code'=>0, 'code_dec'=>'No data available'];
			    elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Tidak ada data tersedia'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '暫無數據'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'कोई डाटा उपलब्ध नहीं'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Không có dữ liệu'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Datos no disponibles'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => 'データがありません'];
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ไม่มีข้อมูล'];
		
		// 获取数据
		$data['data'] = model('UserDaily')->teamData($teamUserInfo);

		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;

		return $data;
	}

	//业绩报表
	public function userTeamrepost(){
		
		$param    =	input('param.');
		$userArr  =	explode(',',auth_code($param['token'],'DECODE'));
		$uid      =	$userArr[0];
		$lang	  = (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

		// 搜索用户名
		$date = (isset($param['date']) && $param['date']) ?  strtotime($param['date']) : mktime(0, 0, 0, date('m'), date('d'), date('Y'));		
		// 我的团队数据
		$myTeamData = model('UserDaily')->field(['SUM(`order`)'=>'order','SUM(`commission`)'=>'commission','SUM(`giveback`)'=>'giveback'])->join('user_team', 'ly_user_daily.uid = user_team.team')->where(array(['user_team.uid', '=', $uid],['user_team.team', '<>', $uid],['ly_user_daily.date', '=',$date]))->findOrEmpty();
		// 团队业绩
		$data['myself']['teamOrder']  = (isset($myTeamData['order'])) ? $myTeamData['order'] : 0;
		// 团队佣金
		$data['myself']['teamFee']    = (isset($myTeamData['commission'])) ? $myTeamData['commission'] : 0;
		// 团队收益
		$data['myself']['teamProfit'] = (isset($myTeamData['giveback'])) ? $myTeamData['giveback'] : 0;
		// 我的佣金
		$myFee = model('UserDaily')->field('commission,giveback')->where(array(['uid', '=', $uid],['date', '=',$date]))->findOrEmpty();
		// 我的佣金
		$data['myself']['myFee']           = (isset($myFee['commission'])) ? $myFee['commission'] : 0;
		// 我的收益
		$data['myself']['myProfit']        = (isset($myFee['giveback'])) ? $myFee['giveback'] : 0;
		// 所有下级佣金
		$data['myself']['teamFeeNotMe']    = $data['myself']['teamFee'] - $data['myself']['myFee'];
		// 所有下级收益
		$data['myself']['teamProfitNotMe'] = $data['myself']['teamProfit'] - $data['myself']['myProfit'];

		// 总记录数
		$count       = $this->where('sid', $uid)->count();
		if (!$count) 
				if($lang=='cn') return ['code' => 0, 'code_dec' => '暂无数据'];
				elseif($lang=='en') return ['code'=>0, 'code_dec'=>'No data available'];
			    elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Tidak ada data tersedia'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '暫無數據'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'कोई डाटा उपलब्ध नहीं'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Không có dữ liệu'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Datos no disponibles'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => 'データがありません'];
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ไม่มีข้อมูล'];
		// 每页显示记录
		$pageSize    = (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		// 当前的页,还应该处理非数字的情况
		$pageNo      = (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		// 总页数
		$pageTotal   = ceil($count / $pageSize);//当前页数大于最后页数，取最后	
		// 记录数
		$limitOffset = ($pageNo - 1) * $pageSize;
		// 获取直属下级用户数据
		$teamUserInfo = $this->field('id,uid,realname')->where('sid', $uid)->order('id', 'asc')->limit($limitOffset, $pageSize)->select()->toArray();
		if(!count($teamUserInfo)){
				if($lang=='cn') return ['code'=>0, 'code_dec'=>'无下级用户'];
				elseif($lang=='en') return ['code'=>0, 'code_dec'=>'No subordinate user'];
			    elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Tidak ada pengguna subordinat'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '無下級用戶'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'कोई उपनिर्धारित उपयोक्ता नहीं'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Không người dùng cấp'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Sin usuario'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => '下位ユーザーなし'];
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ไม่มีผู้ใช้ระดับล่าง'];
			
		}
		// 获取数据
		$data['data'] = model('UserDaily')->teamReport($date, $teamUserInfo);

		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;

		return $data;
	}
	
	
	//上级修改下级的费率
	public function setFee(){
		$param    =	input('param.');
		$userArr  =	explode(',',auth_code($param['token'],'DECODE'));
		$uid      =	$userArr[0];
		$lang	  = (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

		// 判断是否是下级
		$is_x = $this->where(['id'=>$param['xid'],'sid'=>$uid])->count();
		if (!$is_x) return ['code'=>0, 'code_dec'=>'未查找到该用户！'];
		
		// 获取用户自身费率
		$selfFee = $this->field('alipay_fee,wechat_fee,bank_fee')->where('id', $uid)->findOrEmpty();
		// 获取平台设置
		$setting = model('Setting')->field('u_alipay_fee_min,u_alipay_fee_max,u_wechat_fee_min,u_wechat_fee_max,u_bank_fee_min,u_bank_fee_max')->where('id','>',0)->findOrEmpty();

		$updateArray = array();
		/**
		 * 规则
		 */
		// 支付宝
		if (isset($param['alipay_fee']) && $param['alipay_fee']) {
			// 数据格式
			if (!is_numeric($param['alipay_fee'])) return ['code'=>0, 'code_dec'=>'请填写有效的费率！'];
			// 平台和上级
			if ($param['alipay_fee'] < $setting['u_alipay_fee_min'] || $param['alipay_fee'] > $selfFee['alipay_fee']) return ['code'=>0, 'code_dec'=>'支付宝费率应在'.$setting['u_alipay_fee_min'].' - '.$selfFee['alipay_fee'].'之间'];
			$updateArray['alipay_fee'] = $param['alipay_fee'];
		}
		// 微信
		if (isset($param['wechat_fee']) && $param['wechat_fee']) {
			// 数据格式
			if (!is_numeric($param['wechat_fee'])) return ['code'=>0, 'code_dec'=>'请填写有效的费率！'];
			// 平台和上级
			if ($param['wechat_fee'] < $setting['u_wechat_fee_min'] || $param['wechat_fee'] > $selfFee['wechat_fee']) return ['code'=>0, 'code_dec'=>'微信费率应在'.$setting['u_wechat_fee_min'].' - '.$selfFee['wechat_fee'].'之间'];
			$updateArray['wechat_fee'] = $param['wechat_fee'];
		}
		// 银行
		if (isset($param['bank_fee']) && $param['bank_fee']) {
			// 数据格式
			if (!is_numeric($param['bank_fee'])) return ['code'=>0, 'code_dec'=>'请填写有效的费率！'];
			// 平台和上级
			if ($param['bank_fee'] < $setting['u_bank_fee_min'] || $param['bank_fee'] > $selfFee['bank_fee']) return ['code'=>0, 'code_dec'=>'银行费率应在'.$setting['u_bank_fee_min'].' - '.$selfFee['bank_fee'].'之间'];
			$updateArray['bank_fee'] = $param['bank_fee'];
		}

		if (!$updateArray) return ['code'=>0, 'code_dec'=>'请填写有效的费率！'];

		$result = $this->where('id', $param['xid'])->update($updateArray);
		if (!$result) return ['code'=>0, 'code_dec'=>'设置失败！'];

		return ['code'=>1, 'code_dec'=>'设置成功！'];
	}
	
	
	//上级激活
	public function setactivationUser(){
		$param    =	input('param.');
		$userArr  =	explode(',',auth_code($param['token'],'DECODE'));
		$uid      =	$userArr[0];
		$xid	  = $param['xid'];//下级id
		$lang	  = (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		$xidinfo  =	$this->where('sid',$uid)->where('id',$xid)->count();//绑定银行卡数量
		if(!$xidinfo){
			$data['code'] = 0;
			$data['code_dec']	= '非直属下级,激活失败';
			return $data;
		}
		//激活
		$is = $this->where('sid',$uid)->where('id',$xid)->update(array('state'=>1,'at_time'=>time()));
		if(!$is){
			$data['code'] 		= 0;
			$data['code_dec']	= '激活失败';
			return $data;
		}
		$data['code'] 		= 1;
		$data['code_dec']	= '激活成功';
		return $data;
	}
	
	
	//私信 
	public function msginfo(){
		
		$param    =	input('param.');
		$userArr  =	explode(',',auth_code($param['token'],'DECODE'));
		$uid      =	$userArr[0];
		$msgid	  = $param['msgid'];//下级id
		$lang	  = (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		$msginfo  = model('Message')->where(array(['id','=',$msgid],['uid','=',$uid],['state','=',2]))->find();
		if(!$msginfo){
			$data['code'] 		= 0;
			$data['code_dec']	= '无效私信';
			return $data;
		}
		
		//解锁 1
		$updatadata3 = array(
			'state'	=>	1,
		);		
		model('Message')->where(array(['id','=',$msgid],['uid','=',$uid],['state','=',2]))->update($updatadata3);
		
		$data['code'] 		= 1;
		$data['content']	= $msginfo['content'];
		$data['add_time']	= date('Y-m-d H:i:s',$msginfo['add_time']);;
		$data['title']		= $msginfo['title'];
		return $data;
	}
	
	
	// 实名认证		(认证的实名必须与您绑定银行卡的开户名一致，否则将无法成功提现。)
	public function realname(){
		$param    	= input('param.');
		$userArr  	= explode(',',auth_code($param['token'],'DECODE'));
		$uid      	= $userArr[0];
		$realname		= input('post.realname/s');			// 真实姓名
		$identity_id	= input('post.identity_id/s');		// 身份证号
		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		
		if(!$realname || !$identity_id){
			if($lang == 'cn') return ['code' => 0, 'code_dec' => '不能实名认证'];	// 两个输入项一个都不能为空，必须都输入内容
			elseif($lang=='en') return ['code' => 0, 'code_dec' => 'Real name authentication is not allowed!'];
			    elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Otentikasi nama asli tidak diizinkan'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '不能實名認證'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'वास्तविक नाम प्रमाणीकरण अनुमति नहीं है'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Xác thực tên thật không được phép'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'No autenticado'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => '実名認証ができません'];
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ไม่สามารถตรวจสอบชื่อจริงได้'];
		}
		
		// 获取用户信息
		$userinfo	= $this->where('id',$uid)->find();
		if(!$userinfo){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec'] = '用户不存在';
			elseif($lang=='en') $data['code_dec'] = 'User does not exist!';
			elseif($lang=='id') $data['code_dec']	= 'pengguna tidak ada';
			elseif($lang=='ft') $data['code_dec']	= '用戶不存在';
			elseif($lang=='yd') $data['code_dec']	= 'उपयोक्ता मौजूद नहीं है';
			elseif($lang=='vi') $data['code_dec']	= 'người dùng không tồn tại';
			elseif($lang=='es') $data['code_dec']	= 'Usuario no existente';
			elseif($lang=='ja') $data['code_dec']	= 'ユーザが存在しません';
			elseif($lang=='th') $data['code_dec']	= 'ผู้ใช้ไม่มี';
			return $data;
		}
		
		if($userinfo['realname'] && $userinfo['identity_id']){
			if($lang == 'cn') return ['code' => 2, 'code_dec' => '已经实名认证'];	// 这两个字段有一个为空，则认为没有实名认证
			elseif($lang=='en') return ['code' => 2, 'code_dec' => 'Verified by real name!'];
			    elseif($lang=='id') return ['code' => 2, 'code_dec' => 'Autentikasi nama asli'];
			    elseif($lang=='ft') return ['code' => 2, 'code_dec' => '已經實名認證'];
			    elseif($lang=='yd') return ['code' => 2, 'code_dec' => 'वास्तविक नाम प्रमाणीकरण'];
			    elseif($lang=='vi') return ['code' => 2, 'code_dec' => 'Xác thực tên thật'];
			    elseif($lang=='es') return ['code' => 2, 'code_dec' => 'Autenticado.'];
			    elseif($lang=='ja') return ['code' => 2, 'code_dec' => '実名認証済み'];
			    elseif($lang=='th') return ['code' => 2, 'code_dec' => 'ชื่อจริงที่ได้รับการรับรอง'];
		}
		
		$bank_username	= model('UserBank')->where('uid',$uid)->value('name');		// 获取用户银行卡持卡人姓名
		if(!$bank_username){
			if($lang == 'cn') return ['code' => 0, 'code_dec' => '用户未绑定银行卡'];
			elseif($lang=='en') return ['code' => 0, 'code_dec' => 'User is not bound to bank card!'];
			    elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Pengguna tidak terikat ke kartu bank'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '用戶未綁定銀行卡'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'उपयोक्ता बैंक कार्ड में बान्ड नहीं है'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Người dùng không ràng buộc thẻ ngân hàng'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Tarjeta bancaria no vinculada'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => 'ユーザーは銀行カードをバインドしていません。'];
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ผู้ใช้ไม่ผูกบัตรธนาคาร'];
		}
		
		if($bank_username != $realname){
			if($lang == 'cn') return ['code' => 0, 'code_dec' => '用户真实姓名与绑定银行卡用户名不一致'];
			elseif($lang=='en') return ['code' => 0, 'code_dec' => 'The users real name is inconsistent with the user name of the bound bank card!'];
			    elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Nama asli pengguna tidak konsisten dengan nama pengguna kartu bank terikat'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '用戶真實姓名與綁定銀行卡用戶名不一致'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'उपयोक्ता का वास्तविक नाम बाइंड बैंक कार्ड के उपयोक्ता के नाम से अक्षम नहीं है'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Tên thật của người dùng không khớp với tên dùng của thẻ ngân hàng ràng buộc'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'El nombre real del usuario no coincide con el nombre de usuario de la tarjeta bancaria.'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => 'ユーザーの実名と結合カードのユーザー名が一致していません。'];
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ชื่อจริงของผู้ใช้ไม่ตรงกับชื่อผู้ใช้ที่ถูกผูกไว้กับธนาคารบัตร'];
		}
		
		//正则身份证号
		$rule	= "/^[1-9]\d{5}(19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/i";	//正则规则		
		if(!preg_match($rule, $identity_id)){
			if($lang=='cn') return ['code' => 0, 'code_dec' => '身份证号错误'];
			elseif($lang=='en') return ['code' => 0, 'code_dec' => 'Wrong ID number!'];
			    elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Nomor ID salah'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '身份證號錯誤'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'गलत आईडी संख्या'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Nhầm số nhận diện'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Número de identificación equivocado.'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => '身分証番号が間違っています'];
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'หมายเลขบัตรประชาชนผิดพลาด'];
		}
		
		$realname_data	= ['realname' => $realname, 'identity_id' => $identity_id];
		$this->where('id',$uid)->update($realname_data);
		if($lang=='cn') return ['code' => 1, 'code_dec' => '实名认证成功'];
		elseif($lang=='en')  return ['code' => 1, 'code_dec' => 'Real name authentication succeeded!'];
			    elseif($lang=='id') return ['code' => 0, 'code_dec' => 'Autentikasi nama asli berhasil'];
			    elseif($lang=='ft') return ['code' => 0, 'code_dec' => '實名認證成功'];
			    elseif($lang=='yd') return ['code' => 0, 'code_dec' => 'वास्तविक नाम प्रमाणीकरण सफल'];
			    elseif($lang=='vi') return ['code' => 0, 'code_dec' => 'Xác thực tên thật thành công'];
			    elseif($lang=='es') return ['code' => 0, 'code_dec' => 'Autenticado.'];
			    elseif($lang=='ja') return ['code' => 0, 'code_dec' => '実名認証に成功しました'];
			    elseif($lang=='th') return ['code' => 0, 'code_dec' => 'ชื่อจริงรับรองความสำเร็จ'];
	}
	
		
	/**
		签到
	**/
	public function signin(){
		
		$param    	=	input('param.');
		$userArr  	=	explode(',',auth_code($param['token'],'DECODE'));
		$uid      	=	$userArr[0];
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		$times		=	strtotime(date('Y-m-d',time()));
		$issignin	=	model('UserSignin')->where(array(['uid','=',$uid],['times','=',$times]))->count();
		if($issignin){
			if($lang == 'cn') return ['code' => 0, 'code_dec' => '已签到'];
			else return ['code' => 0, 'code_dec' => 'Signed in!'];
		}
		
		$array = array(
			'uid'	=>	$uid,
			'times'	=>	$times
		);
		
		$in = model('UserSignin')->insertGetId($array);
		if($in){
			if($lang == 'cn') return ['code' => 1, 'code_dec' => '签到成功'];
			else return ['code' => 0, 'code_dec' => 'Signed in failed!'];
		}
		if($lang == 'cn') return ['code' => 1, 'code_dec' => '签到失败'];
		else return ['code' => 0, 'code_dec' => 'Signed in succeeded!'];
	}
	
}
