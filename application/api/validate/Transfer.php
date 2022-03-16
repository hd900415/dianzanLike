<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\validate;

use think\Validate;
use think\Db;

/**
 * ============================================================================
 * ============================================================================
 * 用户资金验证
 */

class UserTotal extends Validate
{
	protected $rule =   [
		'zzusername'   		=> 'require|zzusername:1',
		'amount'     	=> 'require|float|between:10,10000|amount:1',	// 金额
		'drawword'  	=> 'require|drawword:1',						// 支付密码
	];
    
    protected $message  =   [
    	'zzusername.require'	=> 0,    	
    	'price.require'			=> 0,
    	'price.float'			=> 0,
    	'price.between'			=> 3,
    	'drawword'				=> 4,    	
    ];
    
    protected $scene = [
    	'transfer'		=> ['zzusername','amount','drawword'],    	
    ];

	
	/**
     * [zzuid 验证转账用户的id]
     * @param  [string] $value [description]
     * @return [type]          [description]
     */
    protected function zzusername($value){    	
		$zzusername	= input('post.zzusername/d');
		
    	$rs	= Model('Users')->where('username', $zzusername)->count();
		return ($rs) ? true : 2;		// 2被转账用户不存在
    }
	

    /**
     * [drawword 验证资金密码]
     * @param  [string] $value [description]
     * @return [type]          [description]
     */
    protected function drawword($value){
		$token		= input('post.token/s');		
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];

    	$where['id']   = $uid;

    	$rs	= Model('Users')->where($where)->value('fund_password');
		return ($value == auth_code($rs, 'DECODE')) ? true : 4;		// 4资金密码错误
    }

    /**
     * [price 验证用户余额]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function amount($value){
		$token		= input('post.token/s');		
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];

    	$where['uid']   = $uid;
    	$rs = Model('UserTotal')->where($where)->value('balance');

	    return ($rs >= $value) ? true : 5;	// 余额不足
    }
}