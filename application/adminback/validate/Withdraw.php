<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\admin\validate;

use think\Validate;

class Withdraw extends Validate{
    protected $rule =   [
        'price'   =>  'require|number|checkBalance:1',
        'card'    =>  'require|number|integer',
        'pay_pwd' =>  'require|checkPayPwd:1',
    ];

    protected $message =   [
        'price.require'   =>  '请填写金额',
        'price.number'    =>  '请填写正确的金额',
        'price.between'   =>  '请填写正确的金额',
        'card.require'    =>  '请选择银行卡',
        'card.number'     =>  '请选择银行卡',
        'card.integer'    =>  '请选择银行卡',
        'pay_pwd.require' =>  '请输入交易密码',
    ];

    protected $scene = [
        'withdrawSub' =>  ['price','card','pay_pwd'],
    ];

    /**
     * 验证密码
     */
    protected function checkBalance($value){
        $setting = model('Setting')->field('m_withdraw_min,m_withdraw_max')->where('id','>',0)->findOrEmpty();
        if ($value < $setting['m_withdraw_min'] || $value > $setting['m_withdraw_max']) return '提现金额范围：'.$setting['m_withdraw_min'].' - '.$setting['m_withdraw_max'];

        $merchantInfo = model('Merchant')
                        ->field('ly_merchant.cash_fee,merchant_total.balance,merchant_total.frozen_balance')
                        ->join('merchant_total','ly_merchant.id=merchant_total.uid')
                        ->where('ly_merchant.id', session('admin_userid'))->find();
        if ($value > $merchantInfo['balance'] + $merchantInfo['cash_fee']) return '可用余额不足';
        
        return true;
    }

    /**
     * 验证交易密码
     */
    protected function checkPayPwd($value){
        $payPwd = model('Merchant')->where('id', session('admin_userid'))->value('pay_pwd');
        if (is_null($payPwd)) return '请先设置交易密码';
        if (auth_code($payPwd, 'DECODE') != $value) return '交易密码错误';
        return true;
    }
}