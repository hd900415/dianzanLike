<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\admin\validate;

use think\Validate;

class Agentcentre extends Validate{
    protected $rule =   [
        'username'        =>  'require|alphaDash|length:6,18|unique:merchant',
        'types'           =>  'require|number|integer',
        'alipay_fee'      =>  'require|float|checkAlipayFee:1',
        'wechat_fee'      =>  'require|float|checkWechatyFee:1',
        'bank_fee'        =>  'require|float|checkBankFee:1',
        'password'        =>  'require|alphaDash|length:6,18',
        'check_password'  =>  'require|confirm:password',
        
        'alipay_fee_edit' =>  'regex:^\-?\d+\.?\d*$',
        'wechat_fee_edit' =>  'regex:^\-?\d+\.?\d*$',
        'bank_fee_edit'   =>  'regex:^\-?\d+\.?\d*$',
    ];

    protected $message =   [
        'username.require'       =>  '请填写商户名',
        'username.alphaDash'     =>  '商户名仅限字母、数字、_、-',
        'username.length'        =>  '商户名应限制在6-18位',
        'username.unique'        =>  '商户名已存在',
        'types.require'          =>  '请选择商户类型',
        'types.number'           =>  '请选择有效的商户类型',
        'types.integer'          =>  '请选择有效的商户类型',
        'alipay_fee.require'     =>  '请填写费率',
        'alipay_fee.float'       =>  '请输入有效的商户费率',
        'wechat_fee.require'     =>  '请填写费率',
        'wechat_fee.float'       =>  '请输入有效的商户费率',
        'bank_fee.require'       =>  '请填写费率',
        'bank_fee.float'         =>  '请输入有效的商户费率',
        'password.require'       =>  '请填写密码',
        'password.integer'       =>  '密码仅限字母、数字、_、-',
        'password.length'        =>  '密码应限制在6-18位',
        'check_password.require' =>  '请再次输入密码',
        'check_password.confirm' =>  '两次密码不一致',
        
        'alipay_fee_edit.float'  => '请填写有效的费率',
        'wechat_fee_edit.float'  => '请填写有效的费率',
        'bank_fee_edit.float'    => '请填写有效的费率',
    ];

    protected $scene = [
        'merAdd'  =>  ['username','types','alipay_fee','wechat_fee','bank_fee','password','check_password'],
        'merEdit' =>  ['alipay_fee_edit','wechat_fee_edit','bank_fee_edit'],
    ];

    /**
     * 验证费率
     */
    protected function checkAlipayFee($value){
        $merFee = model('Merchant')->where('id', session('admin_userid'))->value('alipay_fee');
        $setting = model('Setting')->where('id', '>', 0)->value('m_alipay_fee_max');
        if ($value < $merFee || $value > $setting) return '请填写有效的支付宝费率（'.$merFee.' - '.$setting.'）';
        return true;
    }

    /**
     * 验证费率
     */
    protected function checkWechatyFee($value){
         $merFee = model('Merchant')->where('id', session('admin_userid'))->value('wechat_fee');
        $setting = model('Setting')->where('id', '>', 0)->value('m_wechat_fee_max');
        if ($value < $merFee || $value > $setting) return '请填写有效的微信费率（'.$merFee.' - '.$setting.'）';
        return true;
    }

    /**
     * 验证费率
     */
    protected function checkBankFee($value){
         $merFee = model('Merchant')->where('id', session('admin_userid'))->value('bank_fee');
        $setting = model('Setting')->where('id', '>', 0)->value('m_bank_fee_max');
        if ($value < $merFee || $value > $setting) return '请填写有效的银行费率（'.$merFee.' - '.$setting.'）';
        return true;
    }
}