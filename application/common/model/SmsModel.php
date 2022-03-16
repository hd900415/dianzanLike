<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\common\model;

use think\Model;
use think\Cache;

class SmsModel extends Model{
	
    /**
     * 密码加密
     * $userid：用户账号
     * $pwd：用户密码
     */
    public function encrypt_pwd($userid, $pwd)
    {
		$char = '00000000';//固定字符串
		$time = date('mdHis', time());//时间戳
		$pwd = md5($userid . $char . $pwd . $time);//拼接字符串进行加密
		return array('pwd' => $pwd, 'time' => $time);
    }
	
	
    /**
     * 短信内容加密
     * $content：短信内容
     */
    public function encrypt_content($content)
    {
       return urlencode(iconv('UTF-8', 'GBK', $content));//短信内容转化为GBK格式再进行urlencode格式加密
    }
	
	
    /**
     * 短连接请求方法
     * $url：请求地址
     * $post_data：请求数据
     */
    private function connection($url,$post_data){
		$attributes = array('Accept:text/plain;charset=utf-8', 'Content-Type:application/json', 'charset=utf-8', 'Expect:', 'Connection: Close');//请求属性
		$ch = curl_init();//初始化一个会话
		/* 设置验证方式 */
		curl_setopt($ch, CURLOPT_HTTPHEADER, $attributes);//设置访问
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//设置返回结果为流
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);//设置请求超时时间
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);//设置响应超时时间
		/* 设置通信方式 */
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);//使用urlencode格式请求

		$result = curl_exec($ch);//获取返回结果集
		$result=preg_replace('/\"msgid":(\d{1,})./', '"msgid":"\\1",', $result);//正则表达式匹配所有msgid转化为字符串
		$result = json_decode($result, true);//将返回结果集json格式解析转化为数组格式
		if (curl_errno($ch) !== 0) //网络问题请求失败
		{
			$result['result'] = '310099';
			curl_close($ch);//关闭请求会话
			return $result;
		} else {
			$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($statusCode != 200||!isset($result['result']))//域名问题请求失败或不存在返回结果
			{
				$result='';//清空result集合
				$result['result'] = '310099';
			}
			curl_close($ch);//关闭请求会话
			return $result;
		}
    }
	
	
    /*
    * 单条发送
     * $data:请求数据集合
    */
    public function singleSend($data){
		$data['userid']	= strtoupper($data['userid']);//用户名转化为大写
		$encrypt		= $this->encrypt_pwd($data['userid'],$data['pwd']);//密码进行MD5加密
		$data['pwd']	= $encrypt['pwd'];//获取MD5加密后的密码
		$data['timestamp']	= $encrypt['time'];//获取加密时间戳
		$data['content']	= $this->encrypt_content($data['content']);//短信内容进行urlencode加密
		$post_data		= json_encode($data);//将数组转化为JSON格式
		$result			= $this->connection($data['BaseUrl'].'single_send',$post_data);//根据请求类型进行请求
		return $result;//返回请求结果
    }
	
	
    /*
   * 相同内容群发
    * $data:请求数据集合
   */
    public function batchSend($data){
		$data['userid']	= strtoupper($data['userid']);//用户名转化为大写
		$encrypt		= $this->encrypt_pwd($data['userid'],$data['pwd']);//密码进行MD5加密
		$data['pwd']	= $encrypt['pwd'];//获取MD5加密后的密码
		$data['timestamp']	= $encrypt['time'];//获取加密时间戳
		$data['content']	= $this->encrypt_content($data['content']);//短信内容进行urlencode加密
		$post_data		= json_encode($data);//将数组转化为JSON格式
		$result			= $this->connection($data['BaseUrl'].'batch_send',$post_data);//根据请求类型进行请求
		return $result;
    }
	

    /*
    * 个性化内容群发
     * $data:请求数据集合
    */
    public function multiSend($data){
            $data['userid']	= strtoupper($data['userid']);	//用户名转化为大写
			$encrypt		= $this->encrypt_pwd($data['userid'],$data['pwd']);	//密码进行MD5加密
			$data['pwd']	= $encrypt['pwd'];		//获取MD5加密后的密码
			$data['timestamp']	= $encrypt['time'];	//获取加密时间戳
            foreach($data['multimt'] as $k=>$v){
                $data['multimt'][$k]['content']	= $this->encrypt_content($v['content']);//每一条个性化的短信内容进行urlencode加密
            }
            $post_data	= json_encode($data);//将数组转化为JSON格式
            $result		= $this->connection($data['BaseUrl'].'multi_send',$post_data);//根据请求类型进行请求
            return $result;
    }
	

    /*
   * 查询余额
    * $data:请求数据集合
   */
    public function getBalance($data){
		$data['userid']	= strtoupper($data['userid']);//用户名转化为大写
		$encrypt		= $this->encrypt_pwd($data['userid'],$data['pwd']);//密码进行MD5加密
		$data['pwd']	= $encrypt['pwd'];//获取MD5加密后的密码
		$data['timestamp']	= $encrypt['time'];//获取加密时间戳
		$post_data		= json_encode($data);//将数组转化为JSON格式
		$result			= $this->connection($data['BaseUrl'].'get_balance',$post_data);//根据请求类型进行请求
		return $result;
    }
	

    /*
     * 请求获取上行
     * $requestPath:请求地址
     * $data:请求数据集合
     * $isEncryptPwd:是否加密
    */
    public function getMo($data){
		$data['userid']	= strtoupper($data['userid']);//用户名转化为大写
		$encrypt		= $this->encrypt_pwd($data['userid'],$data['pwd']);//密码进行MD5加密
		$data['pwd']	= $encrypt['pwd'];//获取MD5加密后的密码
		$data['timestamp']	= $encrypt['time'];//获取加密时间戳
		$post_data		= json_encode($data);//将数组转化为JSON格式
		$result			= $this->connection($data['BaseUrl'].'get_mo',$post_data);//根据请求类型进行请求
		return $result;//返回请求结果
    }

	
    /*
	  * 请求获取状态报告
	  * $requestPath:请求地址
	  * $data:请求数据集合
	  * $isEncryptPwd:是否加密
	 */
    public function getRpt($data)
    {
		$data['userid']=strtoupper($data['userid']);//用户名转化为大写
		$encrypt=$this->encrypt_pwd($data['userid'],$data['pwd']);//密码进行MD5加密
		$data['pwd']=$encrypt['pwd'];//获取MD5加密后的密码
		$data['timestamp']=$encrypt['time'];//获取加密时间戳
		$post_data = json_encode($data);//将数组转化为JSON格式
		$result=$this->connection($data['BaseUrl'].'get_rpt',$post_data);//根据请求类型进行请求
		return $result;
    }

	
	public function sendSMSCode($phone=''){
	    
	    $lang		= (input('post.lang')) ? input('post.lang') : 'ft';	// 语言类型
		
		$phone = (input('post.phone')) ? input('post.phone') : $phone;// 手机号
		$dest  = (input('post.dest')) ? input('post.dest') : '86';// 手机号

		if (!$phone) return ['code'=>0,'code_dec'=>'请输入手机号'];
		
		$code_rand		= (input('post.code_rand')) ? input('post.code_rand') : '';// 随机码
		$code			= (input('post.code')) ? input('post.code') : '';// 验证码
	//	$recommend		= (input('post.recommend')) ? input('post.recommend') : '';// 邀请码
		
		$cache_code	=	cache('C_Code_'.$code_rand);

	//	if(!$cache_code || $cache_code != $code || !$code || !$recommend || $phone==$recommend){
		if(!$cache_code || $cache_code != $code || !$code){
			return ['code'=>0, 'code_dec'=>'验证码过期或未填写邀请码'];
		}

		//删除随机码缓存
		cache('C_Code_'.$code_rand, NULL);


		$ip = request()->ip();

		if(!$ip){
			return ['code'=>0, 'code_dec'=>'失败2'];
		}
		
		$code = rand(100000,999999);
		$sastime = date('Y-m-d H:i:s');
		file_put_contents('ip.txt', $ip.'-----'.$phone.'--------'.$sastime.PHP_EOL,FILE_APPEND);
		//时间段判定
		$TwoOclock = mktime(2,30,0,date('m'),date('d'),date('Y'));
		$FiveOclock = mktime(5,30,0,date('m'),date('d'),date('Y'));
		//if(time()>$TwoOclock && time()<$FiveOclock) return ['code'=>0,'code_dec'=>'发送频繁，稍后再试t'];

		$sendtime = cache('C_sendtime_'.$ip) ? cache('C_sendtime_'.$ip) : time()-60;
		if(time()-$sendtime < 60){
			//return ['code'=>0,'code_dec'=>'发送频繁，稍后再试i'];
		}
		cache('C_sendtime_'.$ip,time()+60);
		
		
		$is_sms	=	model('Setting')->where('id',1)->value('is_sms');

		if($is_sms==2){
			return ['code'=>0, 'code_dec'=>'失败'];
		}
		
		//邀请码是否存在
	//	if($recommend){
	//		$where = [
	//			['ly_users.idcode','=',$recommend],
	//			['ly_users.state','=',1],
	//		];
	//		$where2 = [
	//			['ly_users.username','=',$recommend],
	//			['ly_users.state','=',1],
	//		];

	//		$suserinfo = model('Users')->field('ly_users.id,ly_users.vip_level,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->whereOr([$where, $where2])->find();
	//		if(!$suserinfo){
	//			return ['code'=>0, 'code_dec'=>'邀请码不可用'];
	//		}
	//	}

		$data=array();
		//服务商url
		$data['BaseUrl']	=	'http://api01.monyun.cn:7901/sms/v2/std/';
		
		$data['userid']		=	'E10DGK';
		//设置密码（必填.填写明文密码,如:1234567890）
		$data['pwd']		=	'yKaLjK';
		
		// 手机号码

		$data['mobile']		=	'00'.$dest.$phone;
		//设置发送短信内容(必填)
		$data['content']	=	'您的验证码是'.$code.'，在30分钟内有效。如非本人操作请忽略本短信。';
		// 业务类型(可选)
		$data['svrtype']	=	'';
		// 设置扩展号(可选)
		$data['exno']		=	'';
		//用户自定义流水编号(可选)
		$data['custid']		=	'';
		// 自定义扩展数据(可选)
		$data['exdata']		=	'';

		/**
		 * 发送间隔时间
		 */
		if (!is_null(cache('interval'.$data['mobile'])) && time() - cache('interval'.$data['mobile']) < 5) return ['code'=>0, 'code_dec'=>'发送频率太快啦！'];
		cache('interval'.$data['mobile'], time());

		$result = $this->singleSend($data);

		if ($result['result'] === 0) {
			//生成验证码的缓存
			cache('C_Code_'.$phone, $code, 1800);
            if($lang=='cn'){
				return ['code' => 1, 'code_dec' => '验证码已发送，请注意查收！'];
			}elseif($lang=='en'){
				return ['code' => 1, 'code_dec' => 'Verification code has been sent, please check!'];
			}elseif($lang=='id'){
				return ['code' => 1, 'code_dec' => 'Kode pengesahan telah dikirim, tolong periksa!'];
			}elseif($lang=='ft'){
				return ['code' => 1, 'code_dec' => '驗證碼已發送，請注意查收！'];
			}elseif($lang=='vi'){
				return ['code' => 1, 'code_dec' => 'Mật khẩu đã được gửi, xin kiểm tra!'];
			}elseif($lang=='es'){
				return ['code' => 1, 'code_dec' => 'El Código de verificación ha sido enviado.'];
			}elseif($lang=='ja'){
				return ['code' => 1, 'code_dec' => '認証コードが送信されました。ご確認ください。'];
			}
		}else{
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '验证码已发送，请注意查收！'];
			}elseif($lang=='en'){
				return ['code' => 0, 'code_dec' => 'Verification code has been sent, please check!'];
			}elseif($lang=='id'){
				return ['code' => 0, 'code_dec' => 'Kode pengesahan telah dikirim, tolong periksa!'];
			}elseif($lang=='ft'){
				return ['code' => 0, 'code_dec' => '驗證碼已發送，請注意查收！'];
			}elseif($lang=='vi'){
				return ['code' => 0, 'code_dec' => 'Mật khẩu đã được gửi, xin kiểm tra!'];
			}elseif($lang=='es'){
				return ['code' => 0, 'code_dec' => 'El Código de verificación ha sido enviado.'];
			}elseif($lang=='ja'){
				return ['code' => 0, 'code_dec' => '認証コードが送信されました。ご確認ください。'];
			}
			//return ['code'=>0, 'code_dec'=>'验证码发送失败，请重新尝试！'];
		}
	}
}
