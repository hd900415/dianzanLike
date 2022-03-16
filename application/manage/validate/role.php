<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\manage\validate;

use think\Validate;

class role extends Validate
{
	protected $rule =   [
        'role_name'  => 'require',
		'role_name'  => 'chsAlphaNum',
		'role_url'   => 'require',
        'role_url'   => 'regex:^[a-zA-Z\/_]+$',
    ];
    
    protected $message  =   [
        'role_name.require'	    => '权限名必须',
        'role_name.chsAlphaNum'     => '权限名必须中文',
		'role_url.require'	=> '权限必须',
		'role_url.regex'	=> '权限不正确',
    ];
}
