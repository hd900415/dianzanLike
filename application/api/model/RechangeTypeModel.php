<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;

class RechangeTypeModel extends Model{
	//表名
	protected $table = 'ly_rechange_type';

	/**
	 * 获取充值渠道
	 */
	public function getRechargetype($where=array()){
		//获取参数
		$token 		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];//uid
		$username 	= $userArr[1];//username
        $lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		$param 		= input('post.');

		//获取用户等级
		$userInfo = model('Users')->field('grade,user_type,vip_level')->where('id',$uid)->find();
		if ($userInfo['user_type'] == 3) {
			$data['code'] = 0;
			if($lang=='cn'){
			$data['code_dec']	= '没有可用的充值通道';
			}elseif($lang=='en'){
			$data['code_dec']	= 'No recharge channels available';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Tidak ada saluran muat ulang yang tersedia';
			}elseif($lang=='ft'){
			$data['code_dec']	= '沒有可用的充值通道';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'कोई फिर चैनल उपलब्ध नहीं';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Không có kênh phục hồi';
			}elseif($lang=='es'){
			$data['code_dec']	= 'No hay Canal de carga disponible.';
			}elseif($lang=='ja'){
			$data['code_dec']	= 'チャージできるチャンネルがありません。';
			}elseif($lang=='th'){
			$data['code_dec']	= 'ไม่มีช่องชาร์จที่มีอยู่';
			}

			return $data;
		}
		
		$userGrade = $userInfo['vip_level'];
		// if ($userGrade > 9) $userGrade = 9;
		//获取用户充值总额，限制充值渠道
		// $usercharge = model('UserTotal')->where('uid',$uid)->value('total_recharge');
		// if(abs($usercharge) < 2000) $userGrade = 0;
		//定义查询条件
		$where['state'] = 1;
		//客户端
		$where['type'] = (isset($param['type']) && strtolower($param['type']) == 'app') ? 'app' : 'pc';
		// return $this->fetchSql(true)->field('id,name,submitUrl,minPrice,maxPrice,mode,fee,fixed')->where($where)->order('sort','asc')->select();die;
		$rechargeType = $this->field('id,name,submitUrl,minPrice,maxPrice,mode,fee,fixed')->where($where)->order('sort','asc')->select()->toArray();
		if(!$rechargeType){
			$data['code'] = 0;
			if($lang=='cn'){
			$data['code_dec']	= '没有可用的充值通道';
			}elseif($lang=='en'){
			$data['code_dec']	= 'No recharge channels available';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Tidak ada saluran muat ulang yang tersedia';
			}elseif($lang=='ft'){
			$data['code_dec']	= '沒有可用的充值通道';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'कोई फिर चैनल उपलब्ध नहीं';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Không có kênh phục hồi';
			}elseif($lang=='es'){
			$data['code_dec']	= 'No hay Canal de carga disponible.';
			}elseif($lang=='ja'){
			$data['code_dec']	= 'チャージできるチャンネルがありません。';
			}elseif($lang=='th'){
			$data['code_dec']	= 'ไม่มีช่องชาร์จที่มีอยู่';
			}
			return $data;
		}

		foreach ($rechargeType as $key => &$value) {
			switch ($value['mode']) {
				case 'alipay_scan':
				case 'wechat_scan':
				case 'qpay_scan':
					//获取收款账号
					$recaivablesList = model('Recaivables')->field('id,name,qrcode,open_level')->where(['type'=>$value['id'],'state'=>1])->select()->toArray();
					$serverName = model('Setting')->where('id', '1')->value('q_server_name');
					foreach ($recaivablesList as $key2 => &$value2) {
						//判断该用户是否可用
						$openLevel = ($value2['open_level']) ? json_decode($value2['open_level']) : array() ;
						if (!$openLevel || !in_array($userGrade, $openLevel)) {
							array_splice($recaivablesList, $key2, 1);
							// unset($recaivablesList[$key2]);
							continue;
						}
						unset($value2['open_level']);
						$value2['qrcode'] = $serverName.$value2['qrcode'];
					}
					$rechargeType[$key]['qrcodeList'] = array_values($recaivablesList);
					break;

				default:
					$rechargeType[$key]['bankList'] = model('Bank')->field('id,bank_name,bank_code,c_start_time,c_end_time')->where(['pay_type'=>$value['id'],'c_state'=>1])->select()->toArray();
					break;
			}
		}

		$data['code'] = 1;
		$data['info'] = $rechargeType;

		return $data;
	}
}