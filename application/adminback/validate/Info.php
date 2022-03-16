<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\admin\validate;

use think\Validate;

class Info extends Validate{
    protected $rule =   [
        'phone'          => 'require|mobile',
        'mail'           => 'require|email',
        'password'       => 'require|checkPwd:1',
        
        'old_password'   => 'require|checkPwd:1',
        'new_password'   => 'require|alphaDash|length:6,18',
        'check_password' => 'require|confirm:new_password',
        
        'pay_pwd'        => 'require|alphaDash|length:6,18',
        'check_pay_pwd'  => 'require|confirm:pay_pwd',
        
        'card_name'      => 'require|chsAlpha',
        'bank_name'      => 'require|chs',
        'card_number'    => 'require|number',
        
        'platform_name'  => 'chsDash',
        'platform_type'  => 'chsDash',
        'remarks'        => 'chsDash',
        'ip_white'       => ['regex' => '^((http(s)?:\/\/)?(www\.)?[a-zA-Z0-9][-a-zA-Z0-9]*(\.[a-zA-Z0-9][-a-zA-Z0-9]*)+(\.[a-zA-Z0-9][-a-zA-Z0-9]*)?(\.[a-zA-Z0-9][-a-zA-Z0-9]*)?(:\d+)*(\/\w+\.\w+)*\/?,?)+$'],
    ];

    protected $message =   [
        'phone.require'          => '请填写联系电话',
        'phone.mobile'           => '联系电话格式有误，请检查后再提交',
        'mail.require'           => '请填写邮箱',
        'mail.email'             => '邮箱格式有误，请检查后再提交',
        'password.require'       => '请填写交易密码',
        
        'old_password.require'   => '请输入旧密码',
        'new_password.require'   => '请填写新密码',
        'new_password.alphaDash' => '新密码格式有误',
        'new_password.length'    => '新密码长度请控制在6-18位',
        'check_password.require' => '请再次确认新密码',
        'check_password.confirm' => '两次新密码不一致',
        
        'pay_pwd.require'        => '请填写交易密码',
        'pay_pwd.alphaDash'      => '交易密码格式有误',
        'pay_pwd.length'         => '新密码长度请控制在6-18位',
        'check_pay_pwd.require'  => '请再次确认交易密码',
        'check_pay_pwd.confirm'  => '两次交易密码不一致',
        
        'card_name.require'      => '请填写持卡人姓名',
        'card_name.chsAlpha'     => '持卡人姓名仅支持汉字和字母',
        'bank_name.require'      => '请填写开户银行',
        'bank_name.chs'          => '开户银行仅支持汉字',
        'card_number.require'    => '请填写卡号',
        'card_number.number'     => '卡号仅支持纯数字',
        
        'platform_name.chsDash'  => '平台名称仅限汉字、字母、数字和下划线_及破折号-',
        'platform_type.chsDash'  => '平台名称仅限汉字、字母、数字和下划线_及破折号-',
        'remarks.chsDash'        => '平台名称仅限汉字、字母、数字和下划线_及破折号-',
        'ip_white.regex'         => '请填写有效的域名',
    ];

    protected $scene = [
        'bindPhone'    => ['phone','password'],
        'bindMail'     => ['mail','password'],
        'editPwd'      => ['old_password','new_password','check_password'],
        'bindMail'     => ['mail','password'],
        'bindBankcard' => ['card_name','bank_name','card_number'],
        'lookMe'       => ['platform_name','platform_type','remarks','ip_white'],
        'setPayPwd'    => ['pay_pwd','check_pay_pwd'],
    ];

    /**
     * 验证密码
     */
    protected function checkPwd($value){
        $password = model('Merchant')->where('id', session('admin_userid'))->value('password');
        if (auth_code($password, 'DECODE') != $value) return '密码不正确';
        return true;
    }
}