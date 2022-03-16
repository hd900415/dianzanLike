<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


if ( ! function_exists('match_msectime')){
	//返回当前的毫秒时间戳
	function match_msectime(){
		list($msec, $sec) = explode(' ', microtime());
		return $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);

	}
}

if ( ! function_exists('match_msecdate')){
/* 毫秒时间戳转换成日期 */
	function match_msecdate($time){
		$tag='Y-m-d H:i:s';
		$a = substr($time,0,10);
		$b = substr($time,10);
		$date = date($tag,$a);
		return $date;
	}
}

if ( ! function_exists('match_cn')){
	//	匹配中文
	function match_cn($str){
		return preg_match('/^[\x80-\xff]{2,}$/', $str);
	}
}

if ( ! function_exists('match_text')){
	//	匹配字母, 数字, 中文, 下划线
	function match_text($str){
		return preg_match('/^(?!_)(?!.*?_$)[\w\x80-\xff]{4,16}$/', $str);
	}
}

if ( ! function_exists('match_txt')){
	//	匹配手机号和邮箱
	function match_txt($str){
		if (preg_match('/^1[34578]{1}\d{9}$/', $str)) {
			return 'mobile';
		}
		else if (preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $str)) {
			return 'email';
		}

		return false;
	}
}

if ( ! function_exists('match_username')){
	//	匹配字母, 标点符号, 下划线
	function match_username($str){
		return preg_match('/^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]{6,16}$/', $str);
	}
}

if ( ! function_exists('match_password')){
	//	匹配字母, 标点符号, 下划线
	function match_password($str){
		return preg_match('/^[\w[:punct:]]{6,16}$/', $str);
	}
}

if ( ! function_exists('half_replace')){
	//	替换的字符数为字符串长度的一半，并向下取整。替换的位置为字符串的中级位置开始向左右浮动
	function half_replace($str){
		$c = strlen($str)/2;
		return preg_replace('/(?>=.{'.(ceil($c/2)).'})(.{'.floor($c).'}).*?/', str_pad('', floor($c), '*'), $str, 1);
	}

}

if (!function_exists('multi_array_sort')) {
	/**
	 * 二维数组排序
	 * @param  array $multi_array 待排序数组
	 * @param  string $sort_key    排序字段
	 * @param  string $sort        排序类型
	 * @return array              排序后数组
	 */
	function multi_array_sort($multi_array,$sort_key,$sort=SORT_DESC){ 
		if(is_array($multi_array) && $multi_array){
			foreach ($multi_array as $row_array){ 
				if(is_array($row_array)){ 
					$key_array[] = $row_array[$sort_key]; 
				}else{ 
					return false; 
				} 
			} 
		}else{ 
			return false; 
		} 
		array_multisort($key_array,$sort,$multi_array); 
		return $multi_array; 
	}
}

if ( ! function_exists('auth_code')){
	/**
	 * 加密解密
	 * @param	string	$string		要加密的字符串或已加密的密文
	 * @param	string	$operation	DECODE表示解密, ENCODE其他为加密
	 * @param	string	$key		密匙
	 * @param	integer	$expiry		加密后有效期
	 * @return	string				加密解密后的字符串
	 */
	function auth_code($string, $operation = 'DECODE', $key = '', $expiry = 0){
		$ckey_length = 4;						//	动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
		$key = md5($key ? $key : 'AC_KEY');		//	密匙
		$keya = md5(substr($key, 0, 16));		//	密匙a会参与加解密
		$keyb = md5(substr($key, 16, 16));		//	密匙b会用来做数据完整性验证

		//	密匙c用于变化生成的密文
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		//	参与运算的密匙
		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		/*
			明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
			如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
		 */
		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		//	产生密匙簿
		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		//	用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		//	核心加解密部分
		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));	//	从密匙簿得出密匙进行异或，再转成字符
		}

		if($operation == 'DECODE') {
			/*
				substr($result, 0, 10) == 0 验证数据有效性
				substr($result, 0, 10) - time() > 0 验证数据有效性
				substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
				验证数据有效性，请看未加密明文的格式
			 */
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			/*
				把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
				因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
			 */
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}
}

if ( ! function_exists('ajax_return')){
	/**
	 * Ajax方式返回数据到客户端
	 * @param	mixed	$data			要返回的数据
	 * @param	string	$type			AJAX返回数据格式
	 * @param	integer	$json_option	传递给json_encode的option参数
	 */
	function ajax_return($data, $type = '', $json_option = 0){
		if(empty($type)) $type = 'JSON';
		switch (strtoupper($type))
		{
			case 'JSON':
				//	返回JSON数据格式到客户端 包含状态信息
				header('Content-Type:application/json; charset=utf-8');
				exit(json_encode($data, $json_option));
			case 'XML':
				// 返回xml格式数据
				header('Content-Type:text/xml; charset=utf-8');
				exit(xml_encode($data));
			case 'JSONP':
				// 返回JSON数据格式到客户端 包含状态信息
				header('Content-Type:application/json; charset=utf-8');
				$get		= $this->input->get(NULL, TRUE);
				$handler	= isset($get['callback']) ? $get['callback'] : 'jsonpReturn';
				exit($handler.'('.json_encode($data, $json_option).');');
			case 'EVAL' :
				// 返回可执行的js脚本
				header('Content-Type:text/html; charset=utf-8');
				exit($data);
			case 'STR' :
				// 返回可执行的js脚本
				exit($data);
		}
	}
}

if ( ! function_exists('trading_number')){
	/**
	 * 交易号生成
	 */
	function trading_number(){
		$msec = substr(microtime(), 2, 2);		//	毫秒
		$subtle = substr(uniqid('', true), -8);	//	微妙
		return date('YmdHis').$msec.$subtle;	// 当前日期 + 当前时间 + 当前时间毫秒 + 当前时间微妙
	}
}

if ( ! function_exists('geturldata')){
	/**
	 * curl 模拟浏览器抓取数据  http
	 * @param	string	$starttime	开始时间的时间戳
	 * @param	string	$endtime	结束时间的时间戳
	 * @return	array				相差的天 时 分 秒 数组
	 */
	function geturldata($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
		$content = curl_exec($ch);
		return $content;
	}
}

if ( ! function_exists('geturldatahttps')){
	/**
	 * curl 模拟浏览器抓取数据 https
	 * @param	string	$starttime	开始时间的时间戳
	 * @param	string	$endtime	结束时间的时间戳
	 * @return	array				相差的天 时 分 秒 数组
	 */
	function geturldatahttps($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点
		$content = curl_exec($ch);
		return $content;
	}
}

if ( ! function_exists('object_array')){
	//	对象转换成数组
	function object_array($array) {
		if(is_object($array)) {
			$array = (array)$array;
		} if(is_array($array)) {
			foreach($array as $key=>$value) {
			$array[$key] = object_array($value);
		}
	}
		return $array;
	}
}



//拆分字符串
if ( ! function_exists('explodekong')){
	function explodekong($s,$str){
		$newarr = array();
		if($s==""){
			for($i=0;$i<strlen($str);$i++){
				$newarr[]=$str[$i];
			}
		}else{
			$newarr = explode($s,$str);
		}
		return $newarr;
	}
}


if(!function_exists('get_client_ip')){
	//获取登录IP
	function get_client_ip(){
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$arr=explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos=array_search('unknown',$arr);
			if(false!==$pos)
		     unset($arr[$pos]);
			$ip=trim($arr[0]);
		}elseif(isset($_SERVER['HTTP_X_REAL_IP']))
		{
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		}else
		{
			$ip=$_SERVER['REMOTE_ADDR'];
		}

        // IP地址合法验证
		$long = sprintf("%u",ip2long($ip));
		$ip = $long ? array($ip,$long):array('0.0.0.0',0);
		return $ip[0];
	}
}

// if(!function_exists('mkdirs')){
// 	function mkdirs($dir, $mode = 0777){
// 		if(is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
// 		if(!mkdirs(dirname($dir), $mode)) return FALSE;
// 		@mkdir($dir, $mode);
// 	}
// }
	//获取客户端浏览器版本---------------------------------------------------------------------------------------------------
	function get_broswer(){
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
	//获取客户端系统版本
	function get_os(){
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

	//IP地址查询-------------------------------------------------------------------------------------------
	function GetIpLookup($ip=''){
		if(empty($ip)){
			$ip = get_client_ip();
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

	//客户端查询
	function getBrowserType(){
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

	//模拟POST提交
	function curl_post($url, $post){

		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_POST           => true,
			CURLOPT_TIMEOUT        => 5,
			CURLOPT_POSTFIELDS     => $post,
			// CURLOPT_FAILONERROR    => true,
		);

		$ch = curl_init($url);

		curl_setopt_array($ch, $options);

		$result = curl_exec($ch);

		curl_close($ch);

		return $result;
	}

