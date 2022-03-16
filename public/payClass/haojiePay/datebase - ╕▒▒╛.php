<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

class Datebase{
	/**
	 * 构造函数
	 */
	public function __construct(){
		date_default_timezone_set('PRC');
		ini_set('display_errors','off');
	}

	public $localhost = '192.168.0.28';//'192.168.190.12';
	public $username = 'root';
	public $password = 'sAskeji[zzkj]2020*03';
	public $datebase = 'le10';
	public $port = '3306';
	/*public $localhost = '127.0.0.1';//'192.168.190.12';
	public $username = 'root';
	public $password = 'root';
	public $datebase = 'yf_le10';
	public $port = '3306';*/

	/**
	 * 数据库连接
	 * @return object
	 */
	public function db_connect(){
	    $mysqli=mysqli_connect($this->localhost,$this->username,$this->password,$this->datebase,$this->port);
	    if(mysqli_connect_errno()){
	        // echo mysqli_connect_errno().' : '.mysqli_connect_error();
	        $mysqli=null;
	    }
	    return $mysqli;
	}

	/**
	 * 获取数据
	 * @return bool
	 */
	public function param(){
		$data = $_REQUEST;

		if(!$data) $data = file_get_contents("php://input");

		if(is_string($data)){
			$data = json_decode($data, true);
		}
		if (is_object($data)) {
			$data = simplexml_load_string($data);
			$data = $this->objectToArray($data);
		}

		$sqlCruxArr = array('create','insert','delete','update','drop','alter');

		foreach ($data as $key => $value) {
			// if (!preg_match('^\w+$', $key) || !preg_match('^\w+$', $value)) {
			// 	return false;
			// }
			if (in_array($value,$sqlCruxArr)) {
				return false;
			}
		}

		return $data;
	}

	/**
	 * 条件组装
	 * @param  integer $where 默认条件成立
	 * @return string         SQL条件字符串
	 */
	public function getWhere($where=1){
		if(is_array($where) && $where){
			//where条件
			$whereStr = '';
			foreach ($where as $key => $value) {
				if(preg_match('/\s*(<>|<=|>=|<|>)\s*$/i', $key)){
					$key = $key;
				}else{
					$key = "`$key`".'=';
				}
				$whereStr .= $key."'".$value."'".' AND ';
			}
			$whereStr = rtrim($whereStr, ' AND ');
		}else{
			$whereStr = $where;
		}
		return $whereStr;
	}

	/**
	 * 数据插入操作
	 * @param  object $mysqli 数据库连接资源
	 * @param  string $table  数据表
	 * @param  array  $array  数组
	 * @return integer        插入的ID
	 */
	public function db_insert($mysqli, $table, $array=array()){
		if(is_array($array)){
			$str1='';
			$str2='';
			foreach ($array as $key => $value) {
				$str1.="`$key`,";
				$str2.="'$value',";
			}
			$str1=rtrim($str1, ',');
			$str2=rtrim($str2, ',');
			mysqli_query($mysqli, $sql="insert into `$table`($str1) values($str2)");
			return $mysqli->insert_id;
		}
	}

	/**
	 * 数据删除
	 * @param  object  $mysqli 数据库连接资源
	 * @param  string  $table  数据表
	 * @param  integer $where  条件
	 * @return integer         影响的行数
	 */
	public function db_delete($mysqli, $table, $where=1){
		$whereStr = $this->getWhere($where);
		mysqli_query($mysqli, $sql="delete from `$table` where $whereStr");
		return $mysqli->affected_rows;
	}

	/**
	 * 数据更新
	 * @param  object  $mysqli 数据库连接资源
	 * @param  string  $table  数据表
	 * @param  array   $array  更新的数据
	 * @param  integer $where  条件
	 * @return integer         影响的行数
	 */
	public function db_update($mysqli, $table, $array=array(), $where=1){
		if(is_array($array)) {
			$str='';
			foreach ($array as $key => $value) {
				$str.="`$key`='$value',";
			}
			$str=rtrim($str, ',');
			$whereStr = $this->getWhere($where);
			mysqli_query($mysqli, $sql="update `$table` set $str where $whereStr");
			return $mysqli->affected_rows;
		}
	}

	/**
	 * 传入语句更新
	 * @param  object $mysqli 数据库连接资源
	 * @param  string $sql    语句
	 * @return integer        影响的行数
	 */
	public function db_updateSQL($mysqli,$sql){
	    $result = mysqli_query($mysqli,$sql);
	    return $mysqli->affected_rows;
	}

	/**
	 * 数据查询
	 * @param  object  $mysqli 数据库连接资源
	 * @param  string  $table  数据表
	 * @param  integer $where  条件
	 * @param  string  $field  字段
	 * @return array           查询结果数组
	 */
	public function db_select($mysqli, $table, $where=1, $field='*', $isall=false){
		$whereStr = $this->getWhere($where);
		$result = mysqli_query($mysqli, $sql="select $field from `$table` where $whereStr");
		if ($isall) {
			return mysqli_fetch_all($result, MYSQLI_ASSOC);
		} else {
			return mysqli_fetch_assoc($result);
		}
	}

		//获取结果集
	public function db_selectSQL_result($mysqli,$sql){
	    $result = mysqli_query($mysqli,$sql);
	    //return mysqli_fetch_all($result);
	    return mysqli_fetch_all($result,MYSQLI_ASSOC);
	}

	/**
	 * 传入语句查询
	 * @param  object $mysqli 数据库连接资源
	 * @param  string $sql    语句
	 * @return array          查询结果数组
	 */
	public function db_selectSQL($mysqli,$sql, $isall=false){
	    $result = mysqli_query($mysqli,$sql);
		if ($isall) {
			return mysqli_fetch_all($result, MYSQLI_ASSOC);
		} else {
			return mysqli_fetch_assoc($result);
		}
	}
	//订单号生成
	/**
	 * 根据时间订单号生成
	 * @return string 一串数字
	 */
	public function trading_number(){
	    $msec = substr(microtime(), 2, 2);      //  毫秒
	    $subtle = substr(uniqid('', true), -3); //  微妙
	    return date('YmdHis').$msec.$subtle;    // 当前日期 + 当前时间 + 当前时间毫秒 + 当前时间微妙
	}
	/**
	 * Ajax方式返回数据到客户端
	 * @param	mixed	$data			要返回的数据
	 * @param	string	$type			AJAX返回数据格式
	 * @param	integer	$json_option	传递给json_encode的option参数
	 */
	public function ajaxReturn($data, $type = '', $json_option = 0){
		if(empty($type)) $type = 'JSON';
		switch (strtoupper($type)){
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
				$get		= $_GET;
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

	/**
	 * 获取客户端IP
	 * @return string IP
	 */
	public function get_client_ip(){
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
	 * POST提交
	 * @param  string $url  提交地址
	 * @param  array  $post POST数据
	 * @return [type]       [description]
	 */

	public function file_get_contents_post($url, $post){
		$options = array(
			'http'=> array(
				'method'=>'POST',
				'content'=> http_build_query($post),
			),
		);
		$result = file_get_contents($url,false, stream_context_create($options));
		return $result;
	}

	public function curl_post($url,$data){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response=curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * 对象转数组
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	public function objectToArray($array) {
		if (is_object($array)) {
			$array = (array)$array;
		}
		if (is_array($array)) {
			foreach($array as $key => $value) {
				$array[$key] = $this->objectToArray($value);
			}
		}
		return $array;
	}

    //通过订单号查询用户银行卡省份、城市
    public function get_city($array=''){
		$mysqli = $this->db_connect();
		if(is_null($mysqli)){
		    return 2;
		}
    	$sql = "select ly_user_bank.* from `ly_user_bank`,`ly_user_withdrawals` where order_number='" . $array . "'and ly_user_withdrawals.uid = ly_user_bank.uid ";
    	$result = $this->db_selectSQL($mysqli,$sql);

    	if(!$result) return false;
    	$province = $this->db_select($mysqli,'ly_city',array('id'=>$result['pid']),'name');
    	$city = $this->db_select($mysqli,'ly_city',array('id'=>$result['cid']),'name');
		mysqli_close($mysqli);

    	$user_info = array();
        $user_info['province'] =$province['name'];
        $user_info['city']     =$city['name'];
        return $user_info;
    }

	/**
	 * 网银快捷数据检测
	 * @param  array  $array 待检测数组
	 * @return string        检测结果
	 */
	public function verifyQuick($array=array()){
		//检测提交的各项
		if(!is_numeric($array['price']) || $array['price']<=0 || !$array['typeid'] || !$array['uid']){
			$rdata['code'] = 1;
			$rdata['code_dec'] = '参数不全';
		    return $rdata;
		}
		$mysqli = $this->db_connect();
		if(is_null($mysqli)){
			$rdata['code'] = 2;
			$rdata['code_dec'] = '连接数据库失败';
		    return $rdata;
		}
		//检测充值渠道
		$countRechargeType = $this->db_select($mysqli, 'ly_rechange_type', array('id'=>$array['typeid'],'state'=>1), '`minPrice`,`maxPrice`');
		if(!$countRechargeType || $array['price']<$countRechargeType['minPrice'] || $array['price']>$countRechargeType['maxPrice']){
			$rdata['code'] = 3;
			$rdata['code_dec'] = '充值渠道出错';
		    return $rdata;
		}
		//检测用户
		$countUser = $this->db_select($mysqli, 'ly_users', array('id'=>$array['uid'],'recharge_state'=>1), 'count(*)');
		if(!$countUser['count(*)']){
		    mysqli_close($mysqli);
		    $rdata['code'] = 4;
			$rdata['code_dec'] = '获取用户信息失败';
		    return $rdata;
		}
/*		//检测银行信息
		$bankInfo = $this->db_select($mysqli, 'ly_bank', array('id'=>$array['bid'],'c_state'=>1), 'bank_code');
		if(!$bankInfo || $bankInfo['bank_code']!=$array['bank_code']){
		    mysqli_close($mysqli);
			$rdata['code'] = 5;
			$rdata['code_dec'] = '获取银行信息失败';
		    return $rdata;
		}*/
		//入库
		$insertArray = array(
		    'uid'                   =>  $array['uid'],
		    'order_number'          =>  $array['orderNumber'],
		    'type'                  =>  $array['typeid'],
		    'money'                 =>  $array['price'],
		    'account_receivable'    =>  $array['paytype'],
		    'add_time'              =>  time(),
		    'bid'              		=>  $array['bid'],
		    'bank_code'             =>  'Quick',
		);
		$res = $this->db_insert($mysqli, 'ly_user_recharge', $insertArray);
		mysqli_close($mysqli);
		//检测入库结果
		if(!$res) {
			$rdata['code'] = 6;
			$rdata['code_dec'] = '提交失败';
		    return $rdata;
		}

		return 'Y';
	}


	/**
	 * 在线支付数据检测
	 * @param  array  $array 待检测数组
	 * @return string        检测结果
	 */
	public function verifyOnline($array=array()){
		//检测提交的各项
		if(!is_numeric($array['price']) || $array['price']<=0 || !$array['typeid'] || !$array['uid']){
			$rdata['code'] = 1;
			$rdata['code_dec'] = '参数不全';
		    return $rdata;
		}
		$mysqli = $this->db_connect();
		if(is_null($mysqli)){
			$rdata['code'] = 2;
			$rdata['code_dec'] = '连接数据库失败';
		    return $rdata;
		}
		//检测充值渠道
		$countRechargeType = $this->db_select($mysqli, 'ly_rechange_type', array('id'=>$array['typeid'],'state'=>1), '`minPrice`,`maxPrice`');
		if(!$countRechargeType || $array['price']<$countRechargeType['minPrice'] || $array['price']>$countRechargeType['maxPrice']){
			$rdata['code'] = 3;
			$rdata['code_dec'] = '充值渠道出错';
		    return $rdata;
		}
		//检测用户
		$countUser = $this->db_select($mysqli, 'ly_users', array('id'=>$array['uid'],'recharge_state'=>1), 'count(*)');
		if(!$countUser['count(*)']){
		    mysqli_close($mysqli);
		    $rdata['code'] = 4;
			$rdata['code_dec'] = '获取用户信息失败';
		    return $rdata;
		}
		//检测银行信息
		$bankInfo = $this->db_select($mysqli, 'ly_bank', array('id'=>$array['bid'],'c_state'=>1), 'bank_code');
		if(!$bankInfo || $bankInfo['bank_code']!=$array['bank_code']){
		    mysqli_close($mysqli);
			$rdata['code'] = 5;
			$rdata['code_dec'] = '获取银行信息失败';
		    return $rdata;
		}
		//入库
		$insertArray = array(
		    'uid'                   =>  $array['uid'],
		    'order_number'          =>  $array['orderNumber'],
		    'type'                  =>  $array['typeid'],
		    'money'                 =>  $array['price'],
		    'account_receivable'    =>  $array['paytype'],
		    'add_time'              =>  time(),
		    'bid'              		=>  $array['bid'],
		    'bank_code'             =>  $array['bank_code'],
		);
		$res = $this->db_insert($mysqli, 'ly_user_recharge', $insertArray);
		mysqli_close($mysqli);
		//检测入库结果
		if(!$res) {
			$rdata['code'] = 6;
			$rdata['code_dec'] = '提交失败';
		    return $rdata;
		}

		return 'Y';
	}

	/**
	 * 扫码支付数据检测
	 * @param  array  $array 待检测数组
	 * @return string        检测结果
	 */
	public function verifyScan($array=array()){
		//检测提交的各项
		if(!is_numeric($array['price']) || !$array['price'] || !$array['typeid'] || !$array['uid']){
			$rdata['code'] = 1;
			$rdata['code_dec'] = '参数不全';
		    return $rdata;
		}
		$mysqli = $this->db_connect();
		if(is_null($mysqli)){
			$rdata['code'] = 2;
			$rdata['code_dec'] = '连接数据库失败';
		    return $rdata;
		}
		//检测充值渠道
		$countRechargeType = $this->db_select($mysqli, 'ly_rechange_type', array('id'=>$array['typeid'],'state'=>1), '`minPrice`,`maxPrice`');
		if(!$countRechargeType || $array['price']<$countRechargeType['minPrice'] || $array['price']>$countRechargeType['maxPrice']){
		    mysqli_close($mysqli);
		    $rdata['code'] = 3;
			$rdata['code_dec'] = '充值渠道出错';
		    return $rdata;
		}
		//检测用户
		$countUser = $this->db_select($mysqli, 'ly_users', array('id'=>$array['uid'],'recharge_state'=>1), 'count(*)');
		if(!$countUser['count(*)']){
		    mysqli_close($mysqli);
		    $rdata['code'] = 4;
			$rdata['code_dec'] = '获取用户信息失败';
		    return $rdata;
		}
		//检测银行信息
		//$bankInfo = $this->db_select($mysqli, 'ly_bank', array('id'=>$array['bid'],'c_state'=>1), 'bank_code');
		/*if(!$bankInfo || $bankInfo['bank_code']!=$array['scanType']){
		    mysqli_close($mysqli);
			$rdata['code'] = 5;
			$rdata['code_dec'] = '获取银行信息失败';
		    return $rdata;
		}*/
		//入库
		$insertArray = array(
		    'uid'                   =>  $array['uid'],
		    'order_number'          =>  $array['orderNumber'],
		    'type'                  =>  $array['typeid'],
		    'money'                 =>  $array['price'],
		    'account_receivable'    =>  $array['paytype'],
		    'add_time'              =>  time(),
		    'bid'              		=>  '',//$array['bid'],
		    'bank_code'             =>  $array['scanType'],
		);
		$res = $this->db_insert($mysqli, 'ly_user_recharge', $insertArray);
		mysqli_close($mysqli);
		//检测入库结果
		if(!$res) {
			$rdata['code'] = 6;
			$rdata['code_dec'] = '提交失败';
		    return $rdata;
		}

		return 'Y';
	}

	/**
	 * 充值成功时的处理方法
	 * @param  array   $array  必须的数据
	 * @param  boolean $isScan 是否收取手续费
	 * @return boolean         处理结果
	 */
	public function runUpdate($array=array(),$isScan=false){
		if(!$array) return false;

		$mysqli = $this->db_connect();
		if(is_null($mysqli)){
			return false;
		}
		$updateUserRechargeArray = array(
			'state'	=>	1,
			'dispose_time'	=>	time(),
		);
		if($isScan){
			$updateUserRechargeArray['fee'] = $fee = $array['money'] * 0.01;
		}
		$username = $this->db_selectSQL($mysqli, "select * from `ly_users` where `id`='".$array['uid']."'");
		//更新用户充值信息
		$updateUserRecharge = $this->db_update($mysqli, 'ly_user_recharge', $updateUserRechargeArray, array('order_number'=>$array['order_number'],'money'=>$array['money'],'state'=>3));
		if(!$updateUserRecharge){
			mysqli_close($mysqli);
			return false;
		}

		//查询用户余额
		$userBalance = $this->db_selectSQL($mysqli, "select `balance` from `ly_user_total` where `uid`='".$array['uid']."'");

		//更新余额
		$updateBalance = $this->db_updateSQL($mysqli, "update `ly_user_total` set balance = balance + '".$array['money']."', total_recharge = total_recharge + '".$array['money']."' where `uid`='".$array['uid']."'");
		if(!$updateBalance){
			$this->db_update($mysqli, 'ly_user_recharge', array('state'=>3,'dispose_time'=>''), array('order_number'=>$array['order_number'],'money'=>$array['money'],'state'=>1));
			mysqli_close($mysqli);
			return false;
		}
		//添加流水
		$insertTradeDetailsArray = array(
			'uid'						=>	$array['uid'],
			'sid'						=>	$array['uid'],
			'order_number'				=>	$array['order_number'],
			'username'                  =>  $username['username'],
			'user_type'					=>	$username['user_type'],
			'trade_number'				=>	'L'.$this->trading_number(),
			'trade_time'				=>	time(),
			'trade_type'				=>	1,
			'trade_amount'				=>	$array['money'],
			'trade_before_balance'		=>	$userBalance['balance'],
			'account_balance'			=>	$userBalance['balance']+$array['money'],
		);
		$insertTradeDetails = $this->db_insert($mysqli, 'ly_trade_details', $insertTradeDetailsArray);
		if(!$insertTradeDetails){
			$this->db_update($mysqli, 'ly_user_recharge', array('state'=>3,'dispose_time'=>''), array('order_number'=>$array['order_number'],'money'=>$array['money'],'state'=>1));
			$this->db_updateSQL($mysqli, "update `ly_user_total` set balance = balance - '".$array['money']."', total_recharge = total_recharge - '".$array['money']."' where `uid`='".$array['uid']."'");
			mysqli_close($mysqli);
			return false;
		}

		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));//当天时间戳
		$countReport = $this->db_select($mysqli, 'ly_user_daily', array('uid'=>$array['uid'],'date'=>$today), 'count(*)');

		if(!$countReport['count(*)']){
	        $updateDaily = $this->db_insert($mysqli, 'ly_user_daily', array('uid'=>$array['uid'],'username'=>$username['username'],'user_type'=>$username['user_type'],'date'=>$today,'recharge'=>$array['money']));
	    }else{
	        $updateDaily = $this->db_updateSQL($mysqli, "update `ly_user_daily` set recharge = recharge + '".$array['money']."' where `uid`='".$array['uid']."' and `date`='".$today."'");
	    }
	    if(!$updateDaily){
	    	$this->db_update($mysqli, 'ly_user_recharge', array('state'=>3,'dispose_time'=>''), array('order_number'=>$array['order_number'],'money'=>$array['money'],'state'=>1));
			$this->db_updateSQL($mysqli, "update `ly_user_total` set balance = balance - '".$array['money']."', total_recharge = total_recharge - '".$array['money']."' where `uid`='".$array['uid']."'");
			$this->db_delete($mysqli, 'ly_trade_details', array('id'=>$insertTradeDetails));
			mysqli_close($mysqli);
			return false;
	    }

		//新增用户积分累计
		$gradeSetting = $this->db_selectSQL_result($mysqli, "select * from `ly_user_grade` where 1");

		$grade =$username['grade'];
		foreach (array_reverse($gradeSetting) as $key => $value) {
			if($username['experience'] + $array['money'] >= $value['experience']) {
				$grade = $value['id'];
				break;
			}
		}

		$gradeDifference = $grade - $username['grade'];

		$rewardPrice = $username['grade_reward_price'];
		if($gradeDifference >1) {
			//跳级
			for ($i=$grade; $i > $username['grade']; $i--) {
				$rewardPrice += $gradeSetting[$i-1]['promotion'];
			}
		} elseif ($gradeDifference == 1) {
		//非跳级
			$rewardPrice += $gradeSetting[$username['grade']]['promotion'];
		} elseif ($gradeDifference == 0) {
			//未升级
			$username['grade'] = $username['last_grade'];
		}

		//增加用户积分
		$this->db_updateSQL($mysqli, "update `ly_users` set experience = experience + '".$array['money']."', grade = ".$grade.", last_grade = ".$username['grade'].", grade_reward_price = '".$rewardPrice."' where `id`=".$array['uid']);

	    if($isScan){
	    	$this->db_updateSQL($mysqli, "update `ly_user_total` set balance = balance - '".$fee."', total_recharge = total_recharge - '".$fee."' where `uid`='".$array['uid']."'");
	    	//添加流水
			$feeTradeDetailsArray = array(
				'uid'						=>	$array['uid'],
				'username'                  =>  $username['username'],
				'order_number'				=>	$array['order_number'],
				'user_type'					=>	$username['user_type'],
				'trade_number'				=>	'L'.$this->trading_number(),
				'trade_time'				=>	time(),
				'trade_type'				=>	15,
				'trade_amount'				=>	$fee,
				'trade_before_balance'		=>	$userBalance['balance']+$array['money'],
				'account_balance'			=>	$userBalance['balance']+$array['money']-$fee,
			);
			$feeTradeDetails = $this->db_insert($mysqli, 'ly_trade_details', $feeTradeDetailsArray);
	    }

		mysqli_close($mysqli);

		return true;
	}

	/**
	 * 出款订单检测
	 * @param  array  $array [description]
	 * @return [type]        [description]
	 */
	public function verifyDraw($array=array()){
		if(!$array) return '参数缺失';
		//if (!isset($array['uid']) || !$array['uid']) return '参数缺失';
		if (!isset($array['amount']) || !$array['amount']) return '参数缺失';
		if (!isset($array['order']) || !$array['order']) return '参数缺失';
		//if (!isset($array['bank_Code']) || !$array['bank_Code']) return '参数缺失';
		if (!isset($array['account_Name']) || !$array['account_Name']) return '参数缺失';
		if (!isset($array['account_Number']) || !$array['account_Number']) return '参数缺失';
		// 连接数据库
		$mysqli = $this->db_connect();
		if (is_null($mysqli)) return '数据库连接失败';
		//检测订单
		$countDraw = $this->db_select($mysqli, 'ly_user_withdrawals', array('order_number'=>$array['order'],'price'=>$array['amount'],'state'=>3), 'count(*)');
		if(!$countDraw['count(*)']){
		    mysqli_close($mysqli);
		    return '订单不存在';
		}

		mysqli_close($mysqli);

		return 1;
	}

	/**
	 * 代付成功时处理
	 * @return [type] [description]
	 */
	public function withDrawRun($array=array()){
		if(!$array) return false;
		// 连接数据库
		$mysqli = $this->db_connect();
		if (is_null($mysqli)) return false;
		// 更新用户提现信息
		$updateWithDraw = $this->db_update($mysqli, 'ly_user_withdrawals', ['state'=>1], ['uid'=>$array['uid'],'order_number'=>$array['order_number'],'price'=>$array['price'],'state'=>4]);
		if(!$updateWithDraw){
			mysqli_close($mysqli);
			return false;
		}
		// 更新流水
		$updateWithDraw = $this->db_update($mysqli, 'ly_trade_details', ['state'=>1], ['uid'=>$array['uid'],'order_number'=>$array['order_number'],'state'=>3]);
		if(!$updateWithDraw){
			$this->db_update($mysqli, 'ly_user_withdrawals', ['state'=>4], ['uid'=>$array['uid'],'order_number'=>$array['order_number'],'price'=>$array['price'],'state'=>1]);
			mysqli_close($mysqli);
			return false;
		}
		// 更新报表
		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));//当天时间戳
		$countReport = $this->db_select($mysqli, 'ly_user_daily', array('uid'=>$array['uid'],'date'=>$today), 'count(*)');

		if(!$countReport['count(*)']){
			$userInfo = $this->db_select($mysqli, 'ly_users', array('id'=>$array['uid'],'recharge_state'=>1), '`username`');
	        $updateDaily = $this->db_insert($mysqli, 'ly_user_daily', array('uid'=>$array['uid'],'username'=>$userInfo['username'],'user_type'=>$username['user_type'],'date'=>$today,'withdrawals'=>$array['price']));
	    }else{
	        $updateDaily = $this->db_updateSQL($mysqli, "update `ly_user_daily` set withdrawals = withdrawals + '".$array['price']."' where `uid`='".$array['uid']."' and `date`='".$today."'");
	    }
	    if(!$updateDaily){
	    	$this->db_update($mysqli, 'ly_user_withdrawals', ['state'=>4], ['uid'=>$array['uid'],'order_number'=>$array['order_number'],'price'=>$array['price'],'state'=>1]);
			$this->db_update($mysqli, 'ly_trade_details', ['state'=>3], ['uid'=>$array['uid'],'order_number'=>$array['order_number'],'state'=>1]);
			mysqli_close($mysqli);
			return false;
	    }


		return true;
	}
}