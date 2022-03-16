<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;
use think\Cache;
//use think\facade\Request;
use think\Request;

class QrcodeModel extends Model{
	
	protected $table = 'ly_qrcode';
	
	/*  添加二维码账户  */
	public function addQrcode(){
		$token			=	input('post.token/s');
		$userArr		=	explode(',',auth_code($token,'DECODE'));
		$uid			=	$userArr[0];
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		// 检测用户是否存在		
		$is_users = model('Users')->where('id',$uid)->count();
		if(!$is_users){
			$data['code'] = 0;
			if($lang=='cn'){
			$data['code_dec']	= '用户不存在';
			}elseif($lang=='en'){
			$data['code_dec']	= 'user does not exist';
			}elseif($lang=='id'){
			$data['code_dec']	= 'pengguna tidak ada';
			}elseif($lang=='ft'){
			$data['code_dec']	= '用戶不存在';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'उपयोक्ता मौजूद नहीं है';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'người dùng không tồn tại';
			}elseif($lang=='es'){
			$data['code_dec']	= 'Usuario no existente';
			}elseif($lang=='ja'){
			$data['code_dec']	= 'ユーザが存在しません';
			}elseif($lang=='th'){
			$data['code_dec']	= 'ผู้ใช้ไม่มี';
			}

			return $data;
		}
		
		$codename	= input('post.codename/s');
		$codenumder	= input('post.codenumder/s');
		$payway		= input('post.payway/s');
		$remark		= input('post.remark/s');
		$price		= 0;//金额
		
		// 检测账户
		if(empty($codenumder)){
			$data['code'] = 0;
			if($lang=='cn'){
			$data['code_dec']	= '请输入账户名称';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Please enter account name';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Silakan masukkan nama rekening';
			}elseif($lang=='ft'){
			$data['code_dec']	= '請輸入帳戶名稱';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'कृपया खाता नाम भरें';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Hãy nhập tên tài khoản';
			}elseif($lang=='es'){
			$data['code_dec']	= 'Introduzca el nombre de la cuenta';
			}elseif($lang=='ja'){
			$data['code_dec']	= 'アカウント名を入力してください。';
			}elseif($lang=='th'){
			$data['code_dec']	= 'กรุณาใส่ชื่อบัญชี';
			}

			return $data;
		}
		
		
		if(in_array($payway,array('WechatPayFixed','AliPayFixed'))){
			$fixed = model('Setting')->where('id',1)->value('api_fixed');
			$price		= input('post.price');//金额
			$fixedArr = explode(',',$fixed);
			if(!in_array($price,$fixedArr)){
				$data['code'] = 0;
			if($lang=='cn'){
			$data['code_dec']	= '固定金额错误';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Fixed amount error';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Galat jumlah tetap';
			}elseif($lang=='ft'){
			$data['code_dec']	= '固定金額錯誤';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'स्थिर मात्रा त्रुटि';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Lỗi mức cố định';
			}elseif($lang=='es'){
			$data['code_dec']	= 'Error de suma fija';
			}elseif($lang=='ja'){
			$data['code_dec']	= '固定金額エラー';
			}elseif($lang=='th'){
			$data['code_dec']	= 'ข้อผิดพลาดคงที่';
			}

				return $data;
			}

			
		}else{

			/*防止账户重复 */ 
			$account_codenumder	= $this->where(array(['uid','=',$uid],['codenumder','=',$codenumder],['payway','=',$payway],['status','<>',0]))->count();
			
			if($account_codenumder){
				$data['code']			= 0;
			if($lang=='cn'){
			$data['code_dec']	= '账户已存在';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Account already exists';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Akaun sudah ada';
			}elseif($lang=='ft'){
			$data['code_dec']	= '帳戶已存在';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'खाता पहिले से मौजूद है';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Tài khoản đã có';
			}elseif($lang=='es'){
			$data['code_dec']	= 'Cuenta existente';
			}elseif($lang=='ja'){
			$data['code_dec']	= 'アカウントは既に存在します';
			}elseif($lang=='th'){
			$data['code_dec']	= 'บัญชีอยู่แล้ว';
			}

				return $data;
			}
		}
		
		// 上传的二维码图片存储到框架应用根目录/public/uploads/code/ 目录下
		$codeimg	= request()->file('codeimg');
		
		//dump($codeimg);
		//die;
	    if($codeimg){			
			//$rootPath		= __DIR__ . '\..\..\..';
			//$uploadPath		= $rootPath . '\public\upload\code';
			$uploadPath		= './upload/qrcode';
			$uploadInfo	= $codeimg->move($uploadPath);
					
			if(!$uploadInfo){
				$data['code']		= 0;
			if($lang=='cn'){
			$data['code_dec']	= '上传二维码图片失败';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Failed to upload QR code image';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Gagal mengunggah gambar kode QR';
			}elseif($lang=='ft'){
			$data['code_dec']	= '上傳二維碼圖片失敗';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'QR कोड छवि अपलोड करने में विफल';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Lỗi tải ảnh mã QR';
			}elseif($lang=='es'){
			$data['code_dec']	= 'La carga de la imagen no funcionó.';
			}elseif($lang=='ja'){
			$data['code_dec']	= '二次元コード画像のアップロードに失敗しました。';
			}elseif($lang=='th'){
			$data['code_dec']	= 'ล้มเหลวในการอัปโหลดภาพ 2D รหัส';
			}

				return $data;
			}
	    }else{
			$data['code']		= 0;
			if($lang=='cn'){
			$data['code_dec']	= '上传二维码图片失败';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Failed to upload QR code image';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Gagal mengunggah gambar kode QR';
			}elseif($lang=='ft'){
			$data['code_dec']	= '上傳二維碼圖片失敗';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'QR कोड छवि अपलोड करने में विफल';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Lỗi tải ảnh mã QR';
			}elseif($lang=='es'){
			$data['code_dec']	= 'La carga de la imagen no funcionó.';
			}elseif($lang=='ja'){
			$data['code_dec']	= '二次元コード画像のアップロードに失敗しました。';
			}elseif($lang=='th'){
			$data['code_dec']	= 'ล้มเหลวในการอัปโหลดภาพ 2D รหัส';
			}
			return $data;
		}
		
		$codeImgPath	= $uploadInfo->getSaveName();	// 将图片的地址定义为$codeImg_path存进数据库
		$codeImgPath	= str_replace('\\','/',$codeImgPath);
		$imgPath		= $uploadPath.'/'.$codeImgPath;
		
		$paywayurl = controller('common/common')->qrReader($imgPath);
		// return $paywayurl;
		if (!$paywayurl) return ['code'=>0, 'code_dec'=>'二维码识别失败'];

		// 检验二维码
		$qrMate = model('Setting')->where('id','>',0)->value('qr_mate');
		$checkQrcode = explode(',', $qrMate);
		// print_r($checkQrcode);die;
		$checkResult = false;
		foreach ($checkQrcode as $key => $value) {
			$checkQr = 'qr_'.strtolower($paywayurl);
			if (strpos($checkQr, $value)) {
				$checkResult = true;
				break;
			}
		}
		if (!$checkResult) return ['code'=>0, 'code_dec'=>'请上传真实收款码'];
		
		$date	= [
			'uid'        => $uid,				// 用户id
			'codename'   => $codename,			// 昵称
			'codenumder' => $codenumder,		// 账号
			'payway'     => $payway,			// 支付方式
			'qrcodeurl'  => $imgPath,			// 二维码图片路径	
			'reg_time'   => time(),				// 注册时间
			'remarks'    => $remark,			// 备注
			'paywayurl'  => $paywayurl,
			'enable'     => 1,					// 启用账户
			'price'		 => $price,
		];
	    $is_ok	= $this->insertGetId($date);
		
		if(!$is_ok){
			$data['code']		= 0;
			$data['code_dec']	= '二维码账户添加失败';
			return $data;
		}
				
		$data['code']	= 1;
		$data['code_dec']	= '二维码账户添加成功';	
	    return $data;
	}
	
	
	/*  获取二维码账户列表  */
	public function getQrcodeList(){
		$token			=	input('post.token/s');
		$userArr		=	explode(',',auth_code($token,'DECODE'));
		$uid			=	$userArr[0];
		
		// 检测用户是否存在
		$is_users = model('Users')->where('id',$uid)->count();
		if(!$is_users){
			$data['code'] = 0;
			$data['code_dec'] = '用户不存在';
			return $data;
		}

		// 获取支付宝账户列表
		$qrcodeList	= $this->where('uid',$uid)->where('status','<>',0)->select()->toArray();
		
		if(!$qrcodeList){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '没有二维码账户'
			];
			return $data;
		}
		
		// 统计支付类型
		$countWechatpay			= $this->where(array(['uid','=',$uid],['payway','=','WechatPay'],['status','<>',0]))->count();
		$countWechatpayEnable	= $this->where(array(['uid','=',$uid],['payway','=','WechatPay'],['enable','=',1],['status','<>',0]))->count();	// 统计微信开启的账户数量
		$countAlipay			= $this->where(array(['uid','=',$uid],['payway','=','AliPay'],['status','<>',0]))->count();
		$countAlipayEnable		= $this->where(array(['uid','=',$uid],['payway','=','AliPay'],['enable','=',1],['status','<>',0]))->count();		// 统计支付宝开启的账户数量


		
		// 统计支付类型
		$countWechatPayfixed			= $this->where(array(['uid','=',$uid],['payway','=','WechatPayFixed'],['status','<>',0]))->count();
		$countWechatPayfixedEnable	= $this->where(array(['uid','=',$uid],['payway','=','WechatPayFixed'],['enable','=',1],['status','<>',0]))->count();	// 统计微信开启的账户数量
		$countAliPayFixed			= $this->where(array(['uid','=',$uid],['payway','=','AliPayFixed'],['status','<>',0]))->count();
		$countAliPayFixedEnable		= $this->where(array(['uid','=',$uid],['payway','=','AliPayFixed'],['enable','=',1],['status','<>',0]))->count();		// 统计支付宝开启的账户数量

		// 返回数组
		$data['code'] = 1;
		$data['info']['wechatpay']				= $countWechatpay;
		$data['info']['wechatpayEnable']		= $countWechatpayEnable;
		$data['info']['alipay'] 				= $countAlipay;
		$data['info']['alipayEnable']			= $countAlipayEnable;
		
		$data['info']['wechatpayFixed']			= $countWechatPayfixed;
		$data['info']['wechatpayFixedEnable']	= $countWechatPayfixedEnable;
		$data['info']['aliPayFixed'] 			= $countAliPayFixed;
		$data['info']['aliPayFixedEnable']		= $countAliPayFixedEnable;
		
		$i=0;
		$j=0;
		$k=0;
		$h=0;
		foreach($qrcodeList as $key =>$value){
			if($value['payway'] == 'AliPay'){
				$data['data']['alipay'][$i]['id']         = $value['id'];
				$data['data']['alipay'][$i]['codenumder'] = $value['codenumder'];
				$data['data']['alipay'][$i]['codename']   = $value['codename'];
				$data['data']['alipay'][$i]['payway']     = $value['payway'];
				$data['data']['alipay'][$i]['remark']     = $value['remarks'];
				$data['data']['alipay'][$i]['enable']     = $value['enable'];
				$data['data']['alipay'][$i]['status']     = $value['status'];
				$data['data']['alipay'][$i]['qrcodeurl']  = $value['qrcodeurl'];
				$data['data']['alipay'][$i]['paywayurl']  = $value['paywayurl'];
				$data['data']['alipay'][$i]['callnumber'] = $value['callnumber'];
				$data['data']['alipay'][$i]['call_today'] = $value['call_today'];
				$data['data']['alipay'][$j]['price']	  = '';
				++$i;
			}
			
			if($value['payway'] == 'WechatPay'){
				$data['data']['wechatpay'][$j]['id']         = $value['id'];
				$data['data']['wechatpay'][$j]['codenumder'] = $value['codenumder'];
				$data['data']['wechatpay'][$j]['codename']   = $value['codename'];
				$data['data']['wechatpay'][$j]['payway']     = $value['payway'];
				$data['data']['wechatpay'][$j]['remark']     = $value['remarks'];
				$data['data']['wechatpay'][$j]['enable']     = $value['enable'];
				$data['data']['wechatpay'][$j]['status']     = $value['status'];
				$data['data']['wechatpay'][$j]['qrcodeurl']  = $value['qrcodeurl'];
				$data['data']['wechatpay'][$j]['paywayurl']  = $value['paywayurl'];
				$data['data']['wechatpay'][$j]['callnumber'] = $value['callnumber'];
				$data['data']['wechatpay'][$j]['call_today'] = $value['call_today'];
				$data['data']['wechatpay'][$j]['price']      = '';
				++$j;
			}
			
			if($value['payway'] == 'AliPayFixed'){
				$data['data']['AliPayFixed'][$k]['id']         = $value['id'];
				$data['data']['AliPayFixed'][$k]['codenumder'] = $value['codenumder'];
				$data['data']['AliPayFixed'][$k]['codename']   = $value['codename'];
				$data['data']['AliPayFixed'][$k]['payway']     = $value['payway'];
				$data['data']['AliPayFixed'][$k]['remark']     = $value['remarks'];
				$data['data']['AliPayFixed'][$k]['enable']     = $value['enable'];
				$data['data']['AliPayFixed'][$k]['status']     = $value['status'];
				$data['data']['AliPayFixed'][$k]['qrcodeurl']  = $value['qrcodeurl'];
				$data['data']['AliPayFixed'][$k]['paywayurl']  = $value['paywayurl'];
				$data['data']['AliPayFixed'][$k]['callnumber'] = $value['callnumber'];
				$data['data']['AliPayFixed'][$k]['call_today'] = $value['call_today'];
				$data['data']['AliPayFixed'][$k]['price']      = $value['price'];
				++$k;
			}
			
			if($value['payway'] == 'WechatPayFixed'){
				$data['data']['WechatPayFixed'][$h]['id']         = $value['id'];
				$data['data']['WechatPayFixed'][$h]['codenumder'] = $value['codenumder'];
				$data['data']['WechatPayFixed'][$h]['codename']   = $value['codename'];
				$data['data']['WechatPayFixed'][$h]['payway']     = $value['payway'];
				$data['data']['WechatPayFixed'][$h]['remark']     = $value['remarks'];
				$data['data']['WechatPayFixed'][$h]['enable']     = $value['enable'];
				$data['data']['WechatPayFixed'][$h]['status']     = $value['status'];
				$data['data']['WechatPayFixed'][$h]['qrcodeurl']  = $value['qrcodeurl'];
				$data['data']['WechatPayFixed'][$h]['paywayurl']  = $value['paywayurl'];
				$data['data']['WechatPayFixed'][$h]['callnumber'] = $value['callnumber'];
				$data['data']['WechatPayFixed'][$h]['call_today'] = $value['call_today'];
				$data['data']['WechatPayFixed'][$h]['price']	  = $value['price'];
				++$h;
			}
		}		
		return $data;		
	}
	
	
	/*  获取二维码账户详细信息  */
	public function getQrcodeInfo(){
		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));
		$uid			= $userArr[0];
		$qrcode_id		= input('post.qrcode_id/d');
		
		// 检测用户是否存在
		$is_users = model('Users')->where('id',$uid)->count();
		if(!$is_users){
			$data['code'] = 0;
			$data['code_dec'] = '用户不存在';
			return $data;
		}
		
		// 获取二维码账户列表
		$qrcodeInfo	= $this->where(['id'=>$qrcode_id,'uid'=>$uid])->find();
		
		if(!$qrcodeInfo){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '获取二维码账户失败'
			];
			
			return $data;
		}
				
		// 返回数组
		$data['code'] = 1;
		
		$data['data']['id']			= $qrcodeInfo['id'];
		$data['data']['codenumder']	= $qrcodeInfo['codenumder'];
		$data['data']['codename']	= $qrcodeInfo['codename'];
		$data['data']['payway']		= $qrcodeInfo['payway'];
		$data['data']['qrcodeurl']	= $qrcodeInfo['qrcodeurl'];
		$data['data']['calltimes']	= $qrcodeInfo['calltimes'];
		$data['data']['reg_time']	= $qrcodeInfo['reg_time'];
		$data['data']['status']		= $qrcodeInfo['status'];
		$data['data']['remark']		= $qrcodeInfo['remarks'];
		$data['data']['enable']		= $qrcodeInfo['enable'];
		$data['data']['paywayurl']	= $qrcodeInfo['paywayurl'];	
		return $data;		
	}
	
	
	/*  修改二维码账户信息  */
	public function changeQrcodeInfo(){
		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));
		$uid			= $userArr[0];
		$qrcode_id		= input('post.id/d');
		$act			= input('post.action/d');	// 二维码账户修改操作
		
		// 检测用户是否存在
		$is_users = model('Users')->where('id',$uid)->count();
		if(!$is_users){
			$data['code'] = 0;
			$data['code_dec'] = '用户不存在';
			return $data;
		}
		
		// 获取支付宝账户列表
		$qrcodeInfo	= $this->where(['id'=>$qrcode_id,'uid'=>$uid])->find();
		//dump($qrcodeInfo);die;
		if(empty($qrcodeInfo)){
			$data	= [
				'code'		=> 0,
				'code_dec'	=> '获取二维码账户失败'
			];
			
			return $data;
		}
		
		$act	= (empty($act)) ? 1 : $act;
		switch($act){
			case '1':	// 二维码账户的删除
				$isDel_qrcodeInfo	= $this->where('id',$qrcode_id)->update(['status'=>0,'enable'=>0]);

				if(!$isDel_qrcodeInfo){
					$data['code']		= 0;
					$data['code_dec']	= '删除失败';
					return $data;
				}

				$data['code']		= 1;
				$data['code_dec']	= '删除成功';
				return $data;
			break;
			case '2':	// 修改其它
				// 存储相应的数据到表中
				$aff = $this->allowField(true)->save($post, ['id'=>$qrcode_id]);
				if (!$aff) {
					$data['code'] = 0;
					$data['code_dec']	= '修改失败';
					return $data;
				}
				$data['code'] = 1;
				$data['code_dec']	= '修改成功';
				return $data;
			break;
			case '3':	// 启用账户
				$is_enable = $this->where('id',$qrcode_id)->update(['enable'=>1]);
				if (!$is_enable) {
					$data['code'] = 0;
					$data['code_dec']	= '启用账户失败';
					return $data;
				}
				$data['code'] = 1;
				$data['code_dec']	= '启用账户成功';
				return $data;
			break;
			case '4':	// 禁用账户
				$is_disable = $this->where('id',$qrcode_id)->update(['enable'=>0]);
				if (!$is_disable) {
					$data['code'] = 0;
					$data['code_dec']	= '禁用账户失败';
					return $data;
				}
				$data['code'] = 1;
				$data['code_dec']	= '禁用账户';
				return $data;
			break;
		}
				
		
	}

	

}