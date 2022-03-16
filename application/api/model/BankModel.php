<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\model;

class BankModel extends model{

	protected $table = 'ly_bank';

	/**
	 * [getBank 获取银行]
	 * @param  [int]    $bid [银行ID]
	 * @return [string]      [银行]
	 */
	public function getBank($bid){
		if(!$bid){
			$data['code'] = 0;
			return $data;
		}
		//获取数据
		$res = $this->where('id',$bid)->value('bank_name');

		return $res;
	}

	/**
	 * [getPayBanksList 获取可充值银行列表]
	 * @return [type] [description]
	 */
	public function getPayBanksList(){
		//获取数据
		$payBanks = $this->where(array(['c_state','=',1],['pay_type','=',4]))->group('bank_name')->select();
		
		if (!$payBanks) {
			$data['code'] = 0;
			return $data;
		}
		//数组重组
		$data['code'] = 1;
		foreach($payBanks as $key =>$value){
			$data['info'][$key]['bank_id'] = $value['id'];
			$data['info'][$key]['bank'] = $value['bank_name'];
			$data['info'][$key]['types'] =  $value['pay_type'];
		}

		return $data;
	}

	/**
	 * [getDrawBanksList 获取可提现银行列表]
	 * @return [type] [description]
	 */
	public function getDrawBanksList(){
		//获取数据
		$payBanks = $this->where(array(['q_state','=',1],['pay_type','=',4]))->group('bank_name')->select();
		
		if (!$payBanks) {
			$data['code'] = 0;
			return $data;
		}
		//数组重组
		$data['code'] = 1;
		foreach($payBanks as $key =>$value){
			$data['info'][$key]['bank_id'] = $value['id'];
			$data['info'][$key]['bank'] = $value['bank_name'];
			$data['info'][$key]['types'] =  $value['pay_type'];
		}
		
		return $data;
	}	
}
