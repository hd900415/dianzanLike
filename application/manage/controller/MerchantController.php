<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use app\manage\controller\Common;

class MerchantController extends CommonController{
	/**
	 * 空操作处理
	 */
	public function _empty(){
		return $this->list();
	}
	/**
	 * 用户列表
	 */
	public function list(){

		$data = model('Merchant')->userList();

		return view('', [
			'where'     =>	$data['where'],
			'userList'  =>	$data['userList'],
			'page'      =>	$data['page'],
			'userState' =>	$data['userState'],
			'power'     =>	$data['power'],
		]);
	}

	/**
	 * 资金操作
	 * @return [type] [description]
	 */
	public function merchantCapital(){
		if(request()->isAjax()){
			return model('MerchantTotal')->capital();
		}
		$data = model('MerchantTotal')->capitalView();

		$this->assign('id',$data['id']);
		$this->assign('balance',$data['balance']);
		//交易类型
		$this->assign('transactionType',config('custom.transactionType'));

		return $this->fetch();
	}

	/**
	 * 商户银行
	 * @return [type] [description]
	 */
	public function bank(){
		$data = model('MerchantBank')->merchantBank();

		return view('', [
			'where'     =>	$data['where'],
			'userBank'  =>	$data['userBank'],
			'page'      =>	$data['page'],
			'power'     =>	$data['power'],
		]);
	}

	/**
	 * 添加账号
	 */
	public function add(){
		if(request()->isAjax()){
			return model('Merchant')->add();
		}

		$setting = model('Setting')->field('m_alipay_fee_min,m_alipay_fee_max,m_wechat_fee_min,m_wechat_fee_max,m_bank_fee_min,m_bank_fee_max')->where('id','>',0)->findOrEmpty();
		
		return view('', [
			'setting' => $setting
		]);
	}
	/**
	 * 用户编辑
	 */
	public function edit(){
		if(request()->isAjax()){
			return model('Merchant')->edit();
		}

		$data = model('Merchant')->editView();

		$this->assign('userInfo',$data['userInfo']);
		//权限
		$this->assign('power',$data['power']);
		//账号状态
		$this->assign('userState',$data['userState']);
		
		return $this->fetch();
	}

	/**
	 * 删除操作
	 */
	public function del(){
		return model('Merchant')->del();
	}

	/**
	 * 资质认证
	 * @return [type] [description]
	 */
	public function verifyMer(){
		if (request()->isAjax()) return model('Merchant')->verifyMer();

		$id = input('get.id');
		$data = model('Merchant')->field('id,status,verify')->where('id', $id)->find();

		return view('', [
			'info'	=>	$data
		]);
	}

	/**
	 * 商户提现
	 * @return [type] [description]
	 */
	public function withdrawal(){
		$param = input('get.');
		$data = model('MerchantWithdrawals')->withdrawalsList();

		$this->assign('data',$data['data']);
		$this->assign('pageTotal',$data['pageTotal']);
		$this->assign('page',$data['page']);
		$this->assign('where',$data['where']);
		$this->assign('withdrawalsState',$data['withdrawalsState']);
		$this->assign('power',$data['power']);

		return $this->fetch();
	}

	/**
	 * 风控审核
	 */
	public function controlAudit(){
		if(request()->isAjax()){
			return model('MerchantWithdrawals')->controlAudit();
		}
		$data = model('MerchantWithdrawals')->controlAuditView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 财务审核
	 */
	public function financialAudit(){
		if(request()->isAjax()){
			return model('MerchantWithdrawals')->financialAudit();
		}
		$data = model('MerchantWithdrawals')->controlAuditView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 提现详情
	 */
	public function withdrawalsDetails(){
		$data = model('MerchantWithdrawals')->controlAuditView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 商户每日
	 * @return [type] [description]
	 */
	public function merDaily(){
		$data = model('MerchantDaily')->merDaily();

		return view('', [
			'data' => $data
		]);
	}

	public function setLock(){
		return model('Merchant')->setFieldValue();
	}
}