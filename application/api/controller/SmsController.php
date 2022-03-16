<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\controller;

use think\Controller;
use think\Cache;
//use app\common\model\SmsModel as Sms;

class SmsController extends Controller{
	//初始化方法
	protected function initialize(){		
	 	parent::initialize();		
		header('Access-Control-Allow-Origin:*');
		header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, authKey, sessionId");
    }

	//国家区号
	public function smsCode(){
	    $lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

	    if($lang=='en'){
		$data	=	array(
		    array('id'=>'1','name'=>'USA(美国)'),
			array('id'=>'86','name'=>'CHINA(中国)'),
			array('id'=>'62','name'=>'INDOESIA(印度尼西亚)'),
			array('id'=>'84','name'=>'VIETNAM(越南)'),
			array('id'=>'34','name'=>'España(西班牙)'),
			array('id'=>'81','name'=>'JAPAN(日本)'),
			array('id'=>'66','name'=>'THAILAND(泰国)'),
		);
		return json($data);
	    }
	    if($lang=='yd'){
		$data	=	array(
		    array('id'=>'1','name'=>'USA(美国)'),
			array('id'=>'86','name'=>'CHINA(中国)'),
			array('id'=>'62','name'=>'INDOESIA(印度尼西亚)'),
			array('id'=>'84','name'=>'VIETNAM(越南)'),
			array('id'=>'34','name'=>'España(西班牙)'),
			array('id'=>'81','name'=>'JAPAN(日本)'),
			array('id'=>'66','name'=>'THAILAND(泰国)'),
		);
		return json($data);
	    }
	    if($lang=='ft'){
		$data	=	array(
		    array('id'=>'86','name'=>'CHINA(中国)'),
			array('id'=>'1','name'=>'USA(美国)'),
			array('id'=>'62','name'=>'INDOESIA(印度尼西亚)'),
			array('id'=>'84','name'=>'VIETNAM(越南)'),
			array('id'=>'34','name'=>'España(西班牙)'),
			array('id'=>'81','name'=>'JAPAN(日本)'),
			array('id'=>'66','name'=>'THAILAND(泰国)'),
		);
		return json($data);
	    }
	    if($lang=='cn'){
		$data	=	array(
		    array('id'=>'86','name'=>'CHINA(中国)'),
			array('id'=>'1','name'=>'USA(美国)'),
			array('id'=>'62','name'=>'INDOESIA(印度尼西亚)'),
			array('id'=>'84','name'=>'VIETNAM(越南)'),
			array('id'=>'34','name'=>'España(西班牙)'),
			array('id'=>'81','name'=>'JAPAN(日本)'),
			array('id'=>'66','name'=>'THAILAND(泰国)'),
		);
		return json($data);
	    }
	    if($lang=='id'){
		$data	=	array(
		    array('id'=>'62','name'=>'INDOESIA(印度尼西亚)'),
		    array('id'=>'86','name'=>'CHINA(中国)'),
			array('id'=>'1','name'=>'USA(美国)'),
			array('id'=>'84','name'=>'VIETNAM(越南)'),
			array('id'=>'34','name'=>'España(西班牙)'),
			array('id'=>'81','name'=>'JAPAN(日本)'),
			array('id'=>'66','name'=>'THAILAND(泰国)'),
		);
		return json($data);
	    }
	    if($lang=='vi'){
		$data	=	array(
		    array('id'=>'84','name'=>'VIETNAM(越南)'),
		    array('id'=>'62','name'=>'INDOESIA(印度尼西亚)'),
		    array('id'=>'86','name'=>'CHINA(中国)'),
			array('id'=>'1','name'=>'USA(美国)'),
			array('id'=>'34','name'=>'España(西班牙)'),
			array('id'=>'81','name'=>'JAPAN(日本)'),
			array('id'=>'66','name'=>'THAILAND(泰国)'),
		);
		return json($data);
	    }
	    if($lang=='ja'){
		$data	=	array(
		    array('id'=>'81','name'=>'JAPAN(日本)'),
		    array('id'=>'84','name'=>'VIETNAM(越南)'),
		    array('id'=>'62','name'=>'INDOESIA(印度尼西亚)'),
		    array('id'=>'86','name'=>'CHINA(中国)'),
			array('id'=>'1','name'=>'USA(美国)'),
			array('id'=>'34','name'=>'España(西班牙)'),
			array('id'=>'66','name'=>'THAILAND(泰国)'),
		);
		return json($data);
	    }
	    if($lang=='es'){
		$data	=	array(
		    array('id'=>'34','name'=>'España(西班牙)'),
		    array('id'=>'81','name'=>'JAPAN(日本)'),
		    array('id'=>'84','name'=>'VIETNAM(越南)'),
		    array('id'=>'62','name'=>'INDOESIA(印度尼西亚)'),
		    array('id'=>'86','name'=>'CHINA(中国)'),
			array('id'=>'1','name'=>'USA(美国)'),
			array('id'=>'66','name'=>'THAILAND(泰国)'),
		);
		return json($data);
	    }
	    if($lang=='th'){
		$data	=	array(
		    array('id'=>'66','name'=>'THAILAND(泰国)'),
		    array('id'=>'34','name'=>'España(西班牙)'),
		    array('id'=>'81','name'=>'JAPAN(日本)'),
		    array('id'=>'84','name'=>'VIETNAM(越南)'),
		    array('id'=>'62','name'=>'INDOESIA(印度尼西亚)'),
		    array('id'=>'86','name'=>'CHINA(中国)'),
			array('id'=>'1','name'=>'USA(美国)'),
		);
		return json($data);
	    }
		
		
	}
	
    /**
     * 发送短信验证码（POST形式）
     * @return [type] [description]
    */
    public function sendSMSCode(){
		$data = model('Sms')->sendSMSCode();
    	return json($data);
    }

	
}
