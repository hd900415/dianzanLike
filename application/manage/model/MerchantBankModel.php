<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


/**
 * 编写：祝踏岚
 * 对用户银行列表的相关操作
 */

namespace app\manage\model;

use think\Model;

class MerchantBankModel extends Model{
	//表名
	protected $table = 'ly_merchant_bank';

	/**
	 * 用户银行列表
	 */
	public function merchantBank(){
		$param = input('get.');//获取参数
		//查询条件组装
		$where = array();
		//分页参数组装
		$pageParam = array();
		//商户名搜索
		if(isset($param['username']) && $param['username']){
			$where[] = array('merchant.username','=',trim($param['username']));
			$pageParam['username'] = $param['username'];
		}
		//账户名搜索
		if(isset($param['name']) && $param['name']){
			$where[] = array('card_name','=',trim($param['name']));
			$pageParam['name'] = $param['name'];
		}
		//账号搜索
		if(isset($param['card_no']) && $param['card_no']){
			$where[] = array('card_number','=',trim($param['card_no']));
			$pageParam['card_no'] = $param['card_no'];
		}
		//绑定时间搜索
		if(isset($param['datetime']) && $param['datetime']){
			$dateTime = explode(' - ', $param['datetime']);
			$where[] = array('ly_merchant_bank.bind_time','>=',strtotime($dateTime[0]));
			$where[] = array('ly_merchant_bank.bind_time','<=',strtotime($dateTime[1]));
			$pageParam['datetime'] = $param['datetime'];
		}
		//查询符合条件的数据
		$resultData = $this->where($where)->field('ly_merchant_bank.*,merchant.username')->join('merchant','ly_merchant_bank.mid = merchant.id','left')->order('merchant.id','desc')->paginate(16,false,['query'=>$pageParam]);
		//数据集转数组
		$userBank = $resultData->toArray()['data'];

		//权限查询
		$powerWhere = [
			['uid','=',session('manage_userid')],
			['cid','=',255],
		];
		$power = model('ManageUserRole')->getUserPower($powerWhere);

		return array(
			'where'		=>	$pageParam,
			'userBank'	=>	$userBank,//数据
			'page'		=>	$resultData->render(),//分页
			'power'		=>	$power
		);
	}
}