<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;
use think\Db;
use app\api\validate\User as UserValidate;
use think\facade\Request;

class UserLinkModel extends Model{
	//表名
	protected $table = 'ly_userlink';

	// 新建注册链接并获取
	/** 接收参数：
		token		登录标记
		flevel_fd	返点等级
		user_type	用户类型
	 **/
	/*
	public function addUserLink(){
		//获取参数
		$post 				= input('post.');
		$token				= input('post.token/s');
		$userArr			= explode(',',auth_code($token,'DECODE'));	//从token中分离出$userArr[0]=uid和$userArr[1]=username
		$post['user_id']	= $userArr[0];//uid
		
		// 检测用户是否存在
		$userInfo = model('users')->field('vip_level,user_type,idcode')->where('id',$post['user_id'])->find();
		if(!$userInfo){
			$data['code']		=	0;
			$data['code_dec']	=	'注册失败';
			return $data;
		}		
		
		// 检测用户是否已经生成了邀请链接
		$idcode = $this->where('id',$post['user_id'])->value('idcode');
		
		if($idcode){
			$data['code']		=	0;
			$data['code_dec']	=	'邀请链接已生成';
			return $data;
		}
		
		$url = Request::domain()."/#/register?".'regcode='.$userLinkInfo['idcode'];	// 注册链接
		
		model('Users')->where('id',$post['user_id'])->update([]);
		
		$code_data	= [
			'qrcode' 	=> $url,					// 注册链接
			'imgName'	=> $userLinkInfo['idcode'],	// 邀请码
		];
		$code_url	= model('Users')->createQrcode($code_data);
				
		$data_arr = array(
			'sid'			 => $post['user_id'],
			'url'			 => $url,
			'idcode'		 => $idcode,
			'user_type'		 => ($userInfo['user_type'] == 3) ? 3 : $post['user_type'],
			'qq'			 => (isset($post['qq']) && $post['qq']) ? $post['qq'] : '',
			'wx'			 => (isset($post['wx']) && $post['wx']) ? $post['wx'] : '',
			'skype'			 => (isset($post['skype']) && $post['skype']) ? $post['skype'] : '',
			'mail'			 => (isset($post['mail']) && $post['mail']) ? $post['mail'] : '',
		);
				
		$is_id = $this->insert($data_arr);
				
		if (!$is_id) {
			$data['code'] = 0;
		}
		$data['code'] = 1;
		$data['code_dec']	=	'注册链接和邀请码已成功生成';
		$data['url'] = $url;
		$data['idcode'] = $idcode;

		return $data;
	}	
	*/
	
	
	// 获取注册链接
	/*
	 * 接收参数param	token		string
	 *					user_type	int
	 *					token		string
	 * 返回参数			data		array
	 */
	public function getRegLink(){
		//获取参数
		$post 		= input('post.');		
		$token		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));	//从token中分离出$userArr[0]=uid和$userArr[1]=username
		$user_id	= $userArr[0];//uid
		
		if(!$user_id){
			$data['code'] = 0;
			return $data;
		}
							
		$regLink = $this->where('uid',$user_id)->find();
		if (!$regLink) {
			$data['code'] = 0;
			return $data;
		}
		
		$wx_url = config('custom.weixinregedist')['url'].'/weixin/wechat_getcode.php?state=';
		$data['code'] = 1;
		
		$code = base64_encode(json_encode(array('platform'=>config('custom.weixinregedist')['name'],'idcode'=>$regLink['idcode'])));
				
		$data['info']['url'] 		= $regLink['url'];
		//$data['info']['qrcodeurl'] 	= $this->create_qrcode($wx_url.$code,$regLink['idcode']);
		$data['info']['wxlink'] 	= $wx_url.$code;
		$data['info']['reg_num'] 	= $regLink['reg_num'];
		$data['info']['user_type'] 	= $regLink['user_type'];
		$data['info']['idcode'] 	= $regLink['idcode'];
		$data['info']['qq'] 		= $regLink['qq'];
		$data['info']['wx'] 		= $regLink['wx'];
		$data['info']['skype'] 		= $regLink['skype'];
		$data['info']['mail'] 		= $regLink['mail'];
		$data['info']['qrcodeurl'] 	= $this->create_qrcode($regLink['url'],$regLink['idcode']);
		
		return $data;
	}

	
	// 获取注册链接列表
	/*
	 * 接收参数param	page_no		int
	 *					page_size	int
	 *					user_type	int
	 *					token		string
	 *					sign		string
	 * 返回参数			data		array
	 */
	/*public function userLinkList1(){
		//获取参数
		$post 		= input('post.');
		$token		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));	//从token中分离出$userArr[0]=uid和$userArr[1]=username
		$user_id	= $userArr[0];//uid

		if(!$user_id){
			$data['code'] = 0;
			$data['code_dec']	= '用户不存在';
			return $data;
		}

		$userInfo = Model('users')->where('id',$user_id)->find();

		if(!$userInfo){
			$data['code'] 		= 0;
			$data['code_dec']	= '用户不存在1';
			return $data;
		}

		if($userInfo['user_type'] != 1 ){
			$data['code'] 		= 0;
			$data['code_dec']	= '测试号或会员账号没权限分享二维码';
			return $data;
		}
		$list = $this->where('sid', $user_id)->order(['exp_date'=>'desc','id'=>'desc'])->limit(1,0)->find();
	    if(!$list){

			list($msec, $sec) = explode(' ', microtime());
			$msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
			$idcode = substr($msectime,-5);

			$url = Request::domain()."/#/register?".'regcode='.$idcode;
			$post['flevel_fd'] = $userInfo['rebate'];
			$data_arr = array(
				'sid'			 => $post['user_id'],
				'rebate'		 => $post['flevel_fd'],
				'banker_rebate'	 => isset($post['flevel_banker_fd']) ? $post['flevel_banker_fd'] : 0.0,
				'exp_date'		 => strtotime('+100 week'),
				'url'			 => $url,
				'idcode'		 => $idcode,
				'user_type'		 => 1,//($userInfo['user_type'] == 3) ? 3 : $post['user_type'],
				'qq'			 => (isset($post['qq']) && $post['qq']) ? $post['qq'] : '',
				'wx'			 => (isset($post['wx']) && $post['wx']) ? $post['wx'] : '',
				'skype'			 => (isset($post['skype']) && $post['skype']) ? $post['skype'] : '',
				'mail'			 => (isset($post['mail']) && $post['mail']) ? $post['mail'] : '',
			);

			$is_id = $this->insert($data_arr);
			if (!$is_id) {
				$data['code'] = 0;
				$data['code_dec']	= '获取失败,请重新获取';
				return $data;
			}

			$list['idcode']    = $idcode;
			$list['rebate']    = $post['flevel_fd'];
			$list['user_type'] = $data_arr['user_type'];
        }
        //$type = 0;
		//if(isset($post['type']) && $post['type'] == 1) $type = 1;
		$wx_url = config('custom.weixinregedist')['url'].'/weixin/wechat_getcode.php?state=';
		$data['code'] = 1;
		$code = base64_encode(json_encode(array('platform'=>config('custom.weixinregedist')['name'],'idcode'=>$list['idcode'])));

		$data['rebate'] 		= $list['rebate'];
		//$data['exp_date'] 		= $list['exp_date'];
		$data['url'] 			= $wx_url.$code;
		//$data['isreg'] 			= $list['isreg'];
		$data['user_type'] 		= $list['user_type'];
		$data['regcode'] 		= $list['idcode'];
		$data['qrcodeurl'] 		= $this->create_qrcode($data['url'],$list['idcode']);//'/manage/wechatqrcode/pay.php?idcode='.$list['idcode'].'&data='.base64_encode($data['url']);
		return $data;
	}*/


	public function create_qrcode($data,$idcode)
	{
	    //vendor("phpqrcode.phpqrcode");
	    //$data ='http://www.baidu.com';
	    require_once('manage/wechatqrcode/phpqrcode.php');
		if(!file_exists('./manage/wechatqrcode/img')) mkdir('./manage/wechatqrcode/img');
		if(file_exists('./manage/wechatqrcode/img/qrcode.png')){
			unlink('./manage/wechatqrcode/img/qrcode.png');
		}
		$pic = 'manage/wechatqrcode/img/'.$idcode.'.png';
		if(file_exists($pic)){
			 return $pic;
		   //exit;
		}
		//$qrcode = 'img/qrcode.png';
		$errorCorrectionLevel = 'L';
		$matrixPointSize = 10;
		$qrcode = new \QRcode();
		$qrcode->png ($data, $pic, $errorCorrectionLevel, $matrixPointSize, 2 );
		//imagepng($QR, $pic);
		//echo "<img src=$pic>";
	    return $pic;

	}

	// 删除注册链接
	/*public function delUserLink(){
		//获取参数
		//$user_id   = input('post.user_id/d');
		$idcode    = input('post.idcode');
		$token		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));	//从token中分离出$userArr[0]=uid和$userArr[1]=username
		$user_id	= $userArr[0];//uid
		
		if (!$idcode) {
			$data['code'] = 2;
			$data['code_dec'] = '用户识别码不能为空';
			return $data;
		}

		$isdel = $this->where([
							['sid' ,'=', $user_id],
							['idcode' ,'=', $idcode]
						])
						->delete();

		if (!$isdel) {
			$data['code'] = 0;
			$data['code_dec'] = '失败';
			return $data;
		}

		$data['code'] = 1;
		$data['code_dec'] = '成功';
		return $data;
	}*/

	/**
	 * [getUserLink 获取注册链接]
	 * @param  [type] $idcode [识别码]
	 * @return [type]         [注册链接]
	 */
	public function getUserLink($idcode){
		$res = $this->where('idcode' , $idcode)->find();
		return $res;
	}
}