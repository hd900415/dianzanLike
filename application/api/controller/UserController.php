<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\controller;

use app\api\controller\BaseController;

class UserController extends BaseController{
	// 手机验证码+邀请码注册
	public function register(){
		$data = model('Users')->register();
		return json($data);
	}

	// 登录系统
	public function login()
	{
		$data = model('Users')->login();
		return json($data);
	}

	// 验证短信接口
	public function checkSlogin(){
		$data = model('Users')->checkSlogin();
		return json($data);
	}


	// 找回密码————验证短信接口
	public function checkSmsResetPw(){
		$data = model('Users')->checkSmsResetPw();
		return json($data);
	}


	// 找回密码————设置新密码
	public function resetPassword(){
		$data = model('Users')->resetPassword();
		return json($data);
	}


	// 修改密码
	public function changePassword(){
		$data = model('Users')->changePassword();
		return json($data);
	}


	//获取用户信息
	public function getuserinfo(){
		$data = model('Users')->getuserinfo();
		return json($data);
	}

	//获取信息
	public function initialData(){
		$data = model('Users')->initialData();
		return json($data);
	}


	//激活账号
	public function activationUser(){
		$data = model('Users')->activationUser();
		return json($data);
	}
	//修改下级的费率
	public function setFee(){
		$data = model('Users')->setFee();
		return json($data);
	}

	//上级激活
	public function setactivationUser(){
		$data = model('Users')->setactivationUser();
		return json($data);
	}

	//设置用户信息
	public function setuserinfo(){
		$data = model('Users')->setuserinfo();
		return json($data);
	}

	//我的团队
	public function userTeamTotal(){
		$data = model('Users')->userTeamTotal();
		return json($data);
	}

	//我的下级
	public function userTeamlist(){
		$data = model('Users')->userTeamlist();
		return json($data);
	}

	//业绩报表
	public function userTeamrepost(){
		$data = model('Users')->userTeamrepost();
		return json($data);
	}

	//领取佣金
	public function receiveCommission(){
		$data = model('Users')->receiveCommission();
		return json($data);
	}

	//退出登陆
	public function logout(){
		$data = model('Users')->logout();
		return json($data);
	}

	/**
	 * 获取佣金发放历史
	 * @return [type] [description]
	 */
	public function getUserFeeHistory(){
		$data = model('UserCommission')->getUserFeeHistory();
		return json($data);
	}

	//私信
	public function msginfo(){
		$data = model('Users')->msginfo();
		return json($data);
	}



	/**
	 * 获取账号每日盈利
	 * @return [type] [description]
	 */
	public function getUserDailyProfit(){
		$data = model('UserDaily')->getUserDailyProfit();
		return json($data);
	}

	/**
		签到
	**/
	public function signin(){
		$data = model('Users')->signin();
		return json($data);
	}
	
	//个人报表
	public function getStatisticsInfo(){
		$data = model('Users')->getStatisticsInfo();
		return json($data);
	}

	/**
		实名认证
	**/
	public function realname(){
		$data = model('Users')->realname();
		return json($data);
	}


	/**
		用户购买VIP等级
	**/
	public function userBuyVip(){
		$data = model('UserVip')->userBuyVip();
		return json($data);
	}


	/**
		获取用户购买VIP等级列表
	**/
	public function getUserBuyVipList(){
		$data = model('UserVip')->getUserBuyVipList();
		return json($data);
	}
	/**
		获取用户积分信息
	**/
	public function getUserCreditList(){
		$data = model('UserCredit')->getUserCreditList();
		return json($data);
	}
	/**
	 * 图片上传
	 * @return [type] [description]
	 */
	public function uploadImg(){
		$token      = input('post.token/s');
		$userArr	= explode(',', auth_code($token, 'DECODE'));
		$uid		= $userArr[0];
        $lang		= (input('post.lang')) ? input('post.lang') : 'ft';	// 语言类型
		$param = input('post.');
		// 获取文件
		$file = request()->file('image');
		if (!$param || !$file) {
			$_return['code']     = 0;
			if($lang=='cn'){
			$_return['code_dec'] = '参数缺省或图片不存在';
			}elseif($lang=='en'){
			$_return['code_dec'] = 'Parameter default or image does not exist';    
			}elseif($lang=='id'){
			$_return['code_dec'] = 'Parameter default atau gambar tidak ada';    
			}elseif($lang=='ft'){
			$_return['code_dec'] = '參數預設或圖片不存在';    
			}elseif($lang=='yd'){
			$_return['code_dec'] = 'पैरामीटर डिफाल्ट या छवि मौजूद नहीं है';    
			}elseif($lang=='vi'){
			$_return['code_dec'] = 'Mặc định Tham số hay ảnh không tồn tại';    
			}elseif($lang=='es'){
			$_return['code_dec'] = 'Parámetros o imágenes predeterminados no existen';    
			}elseif($lang=='ja'){
			$_return['code_dec'] = 'パラメータがデフォルトまたは画像が存在しません。';    
			}elseif($lang=='th'){
			$_return['code_dec'] = 'พารามิเตอร์เริ่มต้นหรือภาพที่ไม่มีอยู่จริง';    
			}
			return json($_return);
		}
		// 上传路径
		$uploadPath = './upload/image';
		if(!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
		// 上传
		$info = $file->validate(['size'=>1024*1024*15,'ext'=>'jpg,png,gif,jpeg'])->rule('date')->move($uploadPath);
		if($info){
			// 成功上传后 获取上传信息
			$savePath = $uploadPath.'/'.$info->getSaveName();	// 相对路径
			$absPath = 'http://'.$_SERVER['HTTP_HOST'].ltrim($savePath, '.');	// 服务器路径
			// 依类型处理
			switch ($param['type']) {
				// 用户头像
				case '1':
					$res = model('Users')->where('id', $param['uid'])->setField('header', $savePath);
					if ($res) {
						$_return['code']    = 1;
						$_return['imghtml'] = $absPath;
					} else {
						unlink($uploadPath);
						$_return['code']     = 0;
            if($lang=='cn'){
			$_return['code_dec'] = '图片上载失败';
			}elseif($lang=='en'){
			$_return['code_dec'] = 'Image upload failed';    
			}elseif($lang=='id'){
			$_return['code_dec'] = 'Mengupload gambar gagal';   
			}elseif($lang=='ft'){
			$_return['code_dec'] = '圖片上載失敗';    
			}elseif($lang=='yd'){
			$_return['code_dec'] = 'छवि अपलोड असफल';    
			}elseif($lang=='vi'){
			$_return['code_dec'] = 'Lỗi tải ảnh';    
			}elseif($lang=='es'){
			$_return['code_dec'] = 'Error al cargar la imagen';    
			}elseif($lang=='ja'){
			$_return['code_dec'] = '画像のアップロードに失敗しました';    
			}elseif($lang=='th'){
			$_return['code_dec'] = 'ล้มเหลวในการอัพโหลดรูปภาพ';    
			}						
					}
					break;
				// 群头像
				case '2':
					$res = model('Groups')->where('id', $param['uid'])->setField('header', $savePath);
					if ($res) {
						$_return['code']    = 1;
						$_return['imghtml'] = $absPath;
					} else {
						unlink($uploadPath);
						$_return['code']     = 0;
            if($lang=='cn'){
			$_return['code_dec'] = '图片上载失败';
			}elseif($lang=='en'){
			$_return['code_dec'] = 'Image upload failed';    
			}elseif($lang=='id'){
			$_return['code_dec'] = 'Mengupload gambar gagal';   
			}elseif($lang=='ft'){
			$_return['code_dec'] = '圖片上載失敗';    
			}elseif($lang=='yd'){
			$_return['code_dec'] = 'छवि अपलोड असफल';    
			}elseif($lang=='vi'){
			$_return['code_dec'] = 'Lỗi tải ảnh';    
			}elseif($lang=='es'){
			$_return['code_dec'] = 'Error al cargar la imagen';    
			}elseif($lang=='ja'){
			$_return['code_dec'] = '画像のアップロードに失敗しました';    
			}elseif($lang=='th'){
			$_return['code_dec'] = 'ล้มเหลวในการอัพโหลดรูปภาพ';    
			}
					}
					break;
				// 聊天图片
				default:
					$_return['code']    = 1;
					$_return['url'] 	= ltrim($savePath, '.');
					break;
			}
		}else{
			// 上传失败获取错误信息
			// return json(['success'=>$file->getError()]);
			$_return['code']     = 0;
            if($lang=='cn'){
			$_return['code_dec'] = '图片上载失败';
			}elseif($lang=='en'){
			$_return['code_dec'] = 'Image upload failed';    
			}elseif($lang=='id'){
			$_return['code_dec'] = 'Mengupload gambar gagal';   
			}elseif($lang=='ft'){
			$_return['code_dec'] = '圖片上載失敗';    
			}elseif($lang=='yd'){
			$_return['code_dec'] = 'छवि अपलोड असफल';    
			}elseif($lang=='vi'){
			$_return['code_dec'] = 'Lỗi tải ảnh';    
			}elseif($lang=='es'){
			$_return['code_dec'] = 'Error al cargar la imagen';    
			}elseif($lang=='ja'){
			$_return['code_dec'] = '画像のアップロードに失敗しました';    
			}elseif($lang=='th'){
			$_return['code_dec'] = 'ล้มเหลวในการอัพโหลดรูปภาพ';    
			}
		}

		return json($_return);
	}

	/**
	 * 日结报表
	 * @return [type] [description]
	 */
	public function dailyReport(){
		$token   = input('post.token/s');
		$userArr = explode(',', auth_code($token, 'DECODE'));
		$uid     = $userArr[0];

		// 今日时间
		$todayStart = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$todayEnd   = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
		/**
		 * 当日数据
		 */
		// 我的
		$myTaskProfit = model('UserDaily')->field(['SUM(`commission`)'=>'commission','SUM(`rebate`)'=>'rebate'])
						->where('uid', $uid)
						->whereTime('date', 'between', [$todayStart, $todayEnd])
						->find();
		// 任务收益
		$data['myTaskProfit']  = ($myTaskProfit['commission']) ? round($myTaskProfit['commission'], 2) : 0;
		// 完成的任务数
		$data['myTaskFinish']  = model('UserTask')->where([['uid','=',$uid],['status','=',3]])->whereTime('add_time', 'between', [$todayStart, $todayEnd])->count();
		// 总收益
		$data['myTotalProfit'] = round($myTaskProfit['commission'] + $myTaskProfit['rebate'],3);
		// 下级数据
		$branchProfit = model('UserDaily')->field(['SUM(`commission`)'=>'commission','SUM(`rebate`)'=>'rebate'])
						->join('user_team','ly_user_daily.uid=user_team.team')
						->where([
							['user_team.uid','=',$uid],
							['user_team.team','<>',$uid]
						])
						->whereTime('date', 'between', [$todayStart, $todayEnd])
						->find();
		// 任务收益
		$data['branchTaskProfit'] = ($branchProfit['commission']) ? round($branchProfit['commission'], 2) : 0;
		// 完成的任务数
		$data['branchTaskFinish'] = model('UserTask')->alias('utk')
									->join('user_team','utk.uid=user_team.team')
									->where([
										['user_team.uid','=',$uid],
										['status','=',3]
									])
									->whereTime('add_time', 'between', [$todayStart, $todayEnd])
									->count();
		/**
		 * 日结报表（统计30天）
		 */
		for ($i=0; $i < 30; $i++) {
			$data['daily'][$i]['date']    = date('m-d', $todayStart);
			// 消费
			$data['daily'][$i]['consume'] = model('Task')->where('uid',$uid)->whereTime('add_time', 'between', [$todayStart, $todayStart+86399])->sum('total_price');
			// 数量
			$data['daily'][$i]['count']   = model('UserTask')->where([['uid','=',$uid],['status','=',3]])->whereTime('add_time', 'between', [$todayStart, $todayStart+86399])->count();
			// 每日数据
			$taskProfit                   = model('UserDaily')->field(['SUM(`commission`)'=>'commission','SUM(`rebate`)'=>'rebate'])->where('uid', $uid)->whereTime('date', 'between', [$todayStart, $todayStart+86399])->find();
			// 下级
			$data['daily'][$i]['branch']  = ($taskProfit['rebate']) ? $taskProfit['rebate'] : 0;
			// 任务收益
			$data['daily'][$i]['task']    = ($taskProfit['commission']) ? $taskProfit['commission'] : 0;

			$todayStart -= 86400;
		}

		return json(['code'=>1,'data'=>$data]);
	}

	public function teamReport(){
		$data = model('UserTeam')->teamReport();
		return json($data);

	}
	
	/*
		第三方红包扣钱
	*/
	public function ko(){
		$data = model('UserTotal')->ko();
	return json($data);
	}

}
