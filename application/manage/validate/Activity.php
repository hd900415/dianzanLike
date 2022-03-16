<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\manage\validate;

use think\Validate;

class Activity extends Validate
{
    protected $rule =   [
        //活动添加
        'title'             =>  'require',
        'date_range'        =>  'dateFormat:Y-m-d - Y-m-d',
        'sort'              =>  'require|number',
        'state'             =>  'require|number',
        'explain'           =>  'require',
        //工资分红
        'date'              =>  'require|date',
        'startdate'         =>  'require|date',
        'enddate'           =>  'require|date',
    ];

    protected $message =   [
        'title.require'             =>  '活动标题必须填写',
        'date_range.dateFormat'     =>  '时间格式不正确',
        'sort.require'              =>  '排序必须填写',
        'sort.number'               =>  '排序必须是数字',
        'state.require'             =>  '活动状态必须选择',
        'state.number'              =>  '活动状态格式不正确',
        'explain.require'           =>  '活动说明必须填写',
        'date.require'              =>  '请选择日期',
        'date.date'                 =>  '日期格式不正确',
        'startdate.require'         =>  '请选择开始日期',
        'startdate.date'            =>  '开始日期格式不正确',
        'enddate.require'           =>  '请选择结束日期',
        'enddate.date'              =>  '结束日期格式不正确',
    ];

    protected $scene = [
        'activityadd'           =>  ['title','date_range','sort','state','explain'],
        'everyday'              =>  ['date'],
        'between'               =>  ['startdate','enddate'],
    ];
}