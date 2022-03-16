<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\manage\validate;

use think\Validate;

class lottery extends Validate
{
	protected $rule =   [
        'class'				=>	'require|alphaDash',
		'class_name'		=>	'require',
		'maxno'				=>	'require|alphaNum',
		'notime'			=>	'require|integer',
		'bet_table'			=>	'require',
		'model'				=>	'require',
		'price'				=>	'require|integer',
		'starttime'			=>	'require|dateFormat:H:i:s',
		'endtime'			=>	'require|dateFormat:H:i:s',
		'closetime'			=>	'require|integer',
		'rebate_min'		=>	'require|float',
		'rebate_max'		=>	'require|float',
		'rebate_mode_min'	=>	'require|float',
		'rebate_mode_max'	=>	'require|float',
		'hover_type'		=>	'require',
		'ball'				=>	'require|number',
		'min_ball'			=>	'require|number',
		'max_ball'			=>	'require|number',
    ];
    
    protected $message  =   [
        'class.require'				=>	'彩种标识必须',
        'class.alphaDash'			=>	'彩种标识格式不正确',
		'class_name.require'		=>	'彩种名必须',
		'maxno.require'				=>	'开奖期数必须',
		'maxno.alphaNum'			=>	'开奖期数格式不正确',
		'notime.require'			=>	'每期时间必须',
		'notime.integer'			=>	'每期时间格式不正确',
		'bet_table.require'			=>	'投注存放表必须',
		'model.require'				=>	'开奖模型必须',
		'price.require'				=>	'单注金额必须',
		'price.integer'				=>	'单注金额格式不正确',
		'starttime.require'			=>	'开始时间必须',
		'starttime.dateFormat'		=>	'开始时间格式不正确',
		'endtime.require'			=>	'结束时间必须',
		'endtime.dateFormat'		=>	'结束时间格式不正确',
		'closetime.require'			=>	'封单时间必须',
		'closetime.integer'			=>	'封单时间格式不正确',
		'rebate_min.require'		=>	'最低返点值必须',
		'rebate_min.float'			=>	'最低返点值格式不正确',
		'rebate_max.require'		=>	'最高返点值必须',
		'rebate_max.float'			=>	'最高返点值格式不正确',
		'rebate_mode_min.require'	=>	'最小返点模式必须',
		'rebate_mode_min.float'		=>	'最小返点模式格式不正确',
		'rebate_mode_max.require'	=>	'最大返点模式必须',
		'rebate_mode_max.float'		=>	'最大返点模式格式不正确',
		'hover_type.require'		=>	'菜单类型必须',
		'ball.require'				=>	'开奖球数量必须',
		'ball.number'				=>	'开奖球数量格式不正确',
		'min_ball.require'			=>	'最小开奖球必须',
		'min_ball.number'			=>	'最小开奖球格式不正确',
		'max_ball.require'			=>	'最大开奖球必须',
		'max_ball.number'			=>	'最大开奖球格式不正确',
    ];
}
