<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\manage\validate;

use think\Validate;

class Yuebao extends Validate
{
    protected $rule =   [
        //活动添加
        'title'             =>  'require',
        'lilv'              =>  'require',
        'time'              =>  'require',
    ];

    protected $message =   [
        'title.require'             =>  '标题必须填写',
        'lilv.require'              =>  '利率必须填写',
        'time.require'              =>  '时间必须填写',
    ];

//    protected $scene = [
//        'activityadd'           =>  ['title','date_range','sort','state','explain'],
//        'everyday'              =>  ['date'],
//        'between'               =>  ['startdate','enddate'],
//    ];
}