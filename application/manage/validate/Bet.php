<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\manage\validate;

use think\Validate;

class Bet extends Validate
{
    protected $rule =   [
        'lottery'         =>  'require|alphaNum',
        'no'              =>  'require|number',
    ];

    protected $message =   [
        'lottery.require'           =>  '彩种不能为空',
        'lottery.alphaNum'          =>  '彩种格式不正确',
        'no.require'                =>  '期号不能为空',
        'no.number'                 =>  '期号格式不正确',
    ];

    protected $scene = [
        'cancelMany'           =>  ['lottery','no'],
    ];
}