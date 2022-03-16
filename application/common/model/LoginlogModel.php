<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\common\model;

use think\Model;

class LoginlogModel extends Model{
	//表名
	protected $table = 'ly_loginlog';

	/**
	 * 获取客户端IP
	 * @return string 客户端IP
	 */
	public function getClientIp(){
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$arr=explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos=array_search('unknown',$arr);
			if(false!==$pos)
		     unset($arr[$pos]);
			$ip=trim($arr[0]);
		}elseif(isset($_SERVER['HTTP_X_REAL_IP'])){
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		}else{
			$ip=$_SERVER['REMOTE_ADDR'];
		}

		// IP地址合法验证
		$long = sprintf("%u",ip2long($ip));
		$ip = $long ? array($ip,$long):array('0.0.0.0',0);
		return $ip[0];
	}

	/**
	 * 获取客户端浏览器版本
	 * @return [type] [description]
	 */
	public function get_broswer(){
		$sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
	    if (stripos($sys, "Firefox/") > 0) {
	        preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
	        $exp[0] = "Firefox";
	        //$exp[1] = $b[1];  //获取火狐浏览器的版本号
	    } elseif (stripos($sys, "Maxthon") > 0) {
	        preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
	        $exp[0] = "傲游";
	        //$exp[1] = $aoyou[1];
	    } elseif (stripos($sys, "MSIE") > 0) {
	        preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
	        $exp[0] = "IE";
	        //$exp[1] = $ie[1];  //获取IE的版本号
	    } elseif (stripos($sys, "OPR") > 0) {
			preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
	        $exp[0] = "Opera";
	        //$exp[1] = $opera[1];
	    } elseif(stripos($sys, "Edge") > 0) {
	        //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
	        preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
	        $exp[0] = "Edge";
	        //$exp[1] = $Edge[1];
	    } elseif (stripos($sys, "Chrome") > 0) {
			preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
	        $exp[0] = "Chrome";
	        //$exp[1] = $google[1];  //获取google chrome的版本号
	    } elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
	        preg_match("/rv:([\d\.]+)/", $sys, $IE);
			$exp[0] = "IE";
	        //$exp[1] = $IE[1];
	    }else {
			$exp[0] = "未知浏览器";
	        //$exp[1] = "";
		}
	    return $exp[0];//.'('.$exp[1].')';
	}

	/**
	 * 获取客户端系统版本
	 * @return [type] [description]
	 */
	public function get_os(){
		$agent = $_SERVER['HTTP_USER_AGENT'];
	    $os = false;

	    if (preg_match('/win/i', $agent) && strpos($agent, '95'))
	    {
	      $os = 'Windows 95';
	    }
	    else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90'))
	    {
	      $os = 'Windows ME';
	    }
	    else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent))
	    {
	      $os = 'Windows 98';
	    }
	    else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent))
	    {
	      $os = 'Windows Vista';
	    }
	    else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent))
	    {
	      $os = 'Windows 7';
	    }
		  else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent))
	    {
	      $os = 'Windows 8';
	    }else if(preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent))
	    {
	      $os = 'Windows 10';#添加win10判断
	    }else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent))
	    {
	      $os = 'Windows XP';
	    }
	    else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent))
	    {
	      $os = 'Windows 2000';
	    }
	    else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent))
	    {
	      $os = 'Windows NT';
	    }
	    else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent))
	    {
	      $os = 'Windows 32';
	    }
	    else if (preg_match('/linux/i', $agent))
	    {
	      $os = 'Linux';
	    }
	    else if (preg_match('/unix/i', $agent))
	    {
	      $os = 'Unix';
	    }
	    else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent))
	    {
	      $os = 'SunOS';
	    }
	    else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent))
	    {
	      $os = 'IBM OS/2';
	    }
	    else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent))
	    {
	      $os = 'Macintosh';
	    }
	    else if (preg_match('/PowerPC/i', $agent))
	    {
	      $os = 'PowerPC';
	    }
	    else if (preg_match('/AIX/i', $agent))
	    {
	      $os = 'AIX';
	    }
	    else if (preg_match('/HPUX/i', $agent))
	    {
	      $os = 'HPUX';
	    }
	    else if (preg_match('/NetBSD/i', $agent))
	    {
	      $os = 'NetBSD';
	    }
	    else if (preg_match('/BSD/i', $agent))
	    {
	      $os = 'BSD';
	    }
	    else if (preg_match('/OSF1/i', $agent))
	    {
	      $os = 'OSF1';
	    }
	    else if (preg_match('/IRIX/i', $agent))
	    {
	      $os = 'IRIX';
	    }
	    else if (preg_match('/FreeBSD/i', $agent))
	    {
	      $os = 'FreeBSD';
	    }
	    else if (preg_match('/teleport/i', $agent))
	    {
	      $os = 'teleport';
	    }
	    else if (preg_match('/flashget/i', $agent))
	    {
	      $os = 'flashget';
	    }
	    else if (preg_match('/webzip/i', $agent))
	    {
	      $os = 'webzip';
	    }
	    else if (preg_match('/offline/i', $agent))
	    {
	      $os = 'offline';
	    }
	    else
	    {
	      $os = '未知操作系统';
	    }
	    return $os;
	}

	/**
	 * IP地址查询
	 * @param string $ip [description]
	 */
	public function GetIpLookup($ip=''){
		return '';
		if(empty($ip)){
			//$ip = $this->input->ip_address();
			$ip = $this->getClientIp();
		}
		if($ip=="127.0.0.1") return "本机地址";
		$api = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
		$json = @file_get_contents($api);//调用新浪IP地址库
		$arr = json_decode($json,true);//解析json
		$country = $arr['data']['country']; //取得国家
		$province = $arr['data']['region'];//获取省份
		$city = $arr['data']['city']; //取得城市
		$isp = $arr['data']['isp']; //取得运营商
		if((string)$country == "中国"){
			if((string)($province) != (string)$city){
				$_location = $province.$city.$isp;
			}else{
				$_location = $country.$city.$isp;
			}
		}else{
			$_location = $country;
		}

		return $_location;
	}

	/**
	 * 客户端查询
	 * @return [type] [description]
	 */
	public function getBrowserType(){
		$is_mobile = 1;
		$mobile_os_list = array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
		$mobile_token_list = array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');

		foreach ($mobile_os_list as $key => $value) {
			if (stripos($_SERVER['HTTP_USER_AGENT'],$value)) {
				$is_mobile = 2;
			}
		}

		foreach ($mobile_token_list as $key => $value) {
			if (stripos($_SERVER['HTTP_USER_AGENT'],$value)) {
				$is_mobile = 2;
			}
		}

		return $is_mobile;
	}
}