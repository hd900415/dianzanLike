<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\manage\validate;

use think\Validate;

class admins extends Validate
{
	protected $rule =   [
        'username'  => 'require|length:5,25',
		'username'  => 'alphaNum',
        'password'  => 'require|length:5,25',
		'password'  => 'alphaNum',
        'safe_code'  => 'require|length:5,25',
		'safe_code'  => 'alphaNum',
    ];
    
    protected $message  =   [
        'username.require'	    	=> '管理员名必须填写',
		'username.length'	   		=> '管理员名6-25字符',
		'username.alphaNum'	   		=> '管理员必须数字或者字母',
        'password.require'	    	=> '密码必须填写',
		'password.length'	   		=> '密码6-25字符',
		'password.alphaNum'	   		=> '密码必须数字或者字母',
        'safe_code.require'	    	=> '安全码必须填写',
		'safe_code.length'	   		=> '安全码6-25字符',
		'safe_code.alphaNum'	   	=> '安全码必须数字或者字母',

    ];
}
