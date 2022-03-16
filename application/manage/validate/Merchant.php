<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\manage\validate;

use think\Validate;

class Merchant extends Validate
{
    protected $rule =   [
        'username'           =>  'require|alphaNum|regex:/^[a-zA-Z][a-zA-Z0-9]{5,14}$/|unique:merchant|unique:users',
        'rebate'             =>  'require|float|chenkRebate:1',
        'password'           =>  'require|alphaNum|regex:/^[a-zA-Z][a-zA-Z0-9]{5,17}$/',
        'repassword'         =>  'require|alphaNum|confirm:password',
        'min_rebate'         =>  'require|float',
        'max_rebate'         =>  'require|float',
        'max_user'           =>  'require|float',
        'bUsername'          =>  'require',
        'artificialUsername' =>  'require|regex:/^[a-zA-Z][a-zA-Z0-9]{5,14}$/',
        'artificialPrice'    =>  'require|regex:/^[-]?\d+\.?\d{0,3}$/',
        'artificialType'     =>  'require|number',
        'artificialSafeCode' =>  'require|chenkSafeCode:1',
        
        'alipay_fee'         =>  'require|float|chenkAlipayFee:1',
        'wechat_fee'         =>  'require|float|chenkWechatFee:1',
        'bank_fee'           =>  'require|float|chenkBankFee:1',
    ];

    protected $message =   [
        'username.require'              =>  '商户名必须填写',
        'username.alphaNum'             =>  '商户名必须数字或者字母',
        'username.unique'               =>  '商户名已存在',
        'username.regex'                =>  '商户名的长度为6-15个字符，以字母开头',

        'rebate.require'                =>  '返点值必须填写',
        'rebate.float'                  =>  '返点值必须为数字',

        'password.require'              =>  '密码必须填写',
        'password.alphaNum'             =>  '密码必须数字或者字母',
        'password.regex'                =>  '密码的长度为6-18个字符，以字母开头',

        'repassword.require'            =>  '密码必须填写',
        'repassword.alphaNum'           =>  '密码必须数字或者字母',
        'repassword.confirm'            =>  '两次密码不一致',

        'min_rebate.require'            =>  '最小返点必须填写',
        'min_rebate.float'              =>  '最小返点必须是数字',
        'max_rebate.require'            =>  '最大返点必须填写',
        'max_rebate.float'              =>  '最小返点必须是数字',
        'max_user.require'              =>  '最大注册数必须填写',
        'max_user.integer'              =>  '最小返点必须是数字',

        'bUsername.require'             =>  '请填写被迁移代理账号',
        'artificialUsername.require'    =>  '请填写会员账号',
        'artificialUsername.regex'      =>  '会员账号不符合规则',
        'artificialPrice.require'       =>  '请填写金额',
        'artificialType.require'        =>  '请选择类型',
        'artificialSafeCode.require'    =>  '请输入安全码',
    ];

    protected $scene = [
        'add'               =>  ['username','alipay_fee','wechat_fee','bank_fee','password','repassword'],
        'ruleadd'           =>  ['min_rebate','max_rebate','max_user'],
        'capital'           =>  ['artificialPrice','artificialType','artificialSafeCode'],
        'artificial'        =>  ['artificialUsername','artificialPrice','artificialType','artificialSafeCode'],
        'artificialBatch'   =>  ['artificialType','artificialSafeCode'],
        'teamMove'          =>  ['bUsername','artificialUsername','artificialSafeCode'],
    ];


    /**
     * 验证支付宝费率
     */
    protected function chenkAlipayFee($value){
        $data = model('Setting')->getFieldsById('m_alipay_fee_min,m_alipay_fee_max');

        if($value < $data['m_alipay_fee_min']) return '支付宝费率不能低于'.$data['m_alipay_fee_min'];
        if($value > $data['m_alipay_fee_max']) return '支付宝费率不能高于'.$data['m_alipay_fee_max'];

        return true;
    }
    /**
     * 验证微信费率
     */
    protected function chenkWechatFee($value){
        $data = model('Setting')->getFieldsById('m_wechat_fee_min,m_wechat_fee_max');

        if($value < $data['m_wechat_fee_min']) return '微信费率不能低于'.$data['m_wechat_fee_min'];
        if($value > $data['m_wechat_fee_max']) return '微信费率不能高于'.$data['m_wechat_fee_max'];

        return true;
    }
    /**
     * 验证银行费率
     */
    protected function chenkBankFee($value){
        $data = model('Setting')->getFieldsById('m_bank_fee_min,m_bank_fee_max');

        if($value < $data['m_bank_fee_min']) return '银行费率不能低于'.$data['m_bank_fee_min'];
        if($value > $data['m_bank_fee_max']) return '银行费率不能高于'.$data['m_bank_fee_max'];

        return true;
    }

    /**
     * 验证用户返点
     */
    protected function chenkRebate($value){
        $data = model('Setting')->getFieldsById('min_rebate,max_rebate');

        if($value < $data['min_rebate']) return '返点值不能低于'.$data['min_rebate'];
        if($value > $data['max_rebate']) return '返点值不能高于'.$data['max_rebate'];

        return true;
    }

    /**
     * 安全码验证
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function chenkSafeCode($value){
        $safeCode = model('Manage')->where('id',session('manage_userid'))->value('safe_code');
        if ($value != auth_code($safeCode,'DECODE')) return '安全码不正确';

        return true;
    }
}