<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\admin\validate;

use think\Validate;

class Common extends Validate{
    protected $rule =   [
        'code'   =>  'require|number|integer',
    ];

    protected $message =   [
        'code.require' =>  '请输入验证码',
        'code.number'  =>  '验证码格式不正确',
        'code.integer' =>  '验证码格式不正确',
    ];

    protected $scene = [
        'checkSMSCode' =>  ['code'],
    ];
}