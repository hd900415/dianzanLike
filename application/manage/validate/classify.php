<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\manage\validate;

use think\Validate;

class classify extends Validate
{
	protected $rule =   [
        'class'  => 'require',
		'class'  => 'alphaNum',
		'class_name'   => 'require',
    ];
    
    protected $message  =   [
        'class.require'	    => '分类必须',
        'class.alphaNum'     => '分类不正确',
		'role_url.require'	=> '分类必须',
    ];
}
