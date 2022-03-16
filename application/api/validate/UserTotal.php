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
		'user_id'   	=> 'require',
		//'z_user_id'		=> 'require',
		'price'     	=> 'require|float|between:10,50000000|price:1',
		'drawword'  	=> 'require|drawword:1',
		'draw_money' 	=> 'require|between:20,50000000|price:1',
		'user_bank_id'	=> 'require|user_bank_id:1|draw_id:1',
		'inpour_money'	=> 'require|between:100,50000000',
		'type_id'		=> 'in:1,2',
	];
    
    protected $message  =   [
    	'user_id.require'		=> 0,
    	//'z_user_id.require'		=> 0,
    	'price.require'			=> 0,
    	'price.float'			=> 0,
    	'price.between'			=> 3,
    	'drawword'				=> 4,
    	'draw_money.require'	=> 0,
    	'draw_money.between'	=> 3,
    	'user_bank_id.require'	=> 7,
    	'inpour_money.require'	=> 0,
    	'inpour_money.between'	=> 0,
		'type_id.in'			=> 0,
    ];
    
    protected $scene = [
    	'transfer'		=> ['price','drawword','z_user_id','balance'],
    	'draw'			=> ['draw_money','user_bank_id','drawword'],
    	'inpourpay'		=> ['inpour_money'],
		'Roomtransfer'	=> ['price'],
        'agent_client_transfer' => ['price','drawword'],
		'agent_client_transfer2' => ['drawword']
    ];


    /**
     * [drawword 验证资金密码]
     * @param  [string] $value [description]
     * @return [type]          [description]
     */
    protected function drawword($value){
    	
		$token = input('post.token/s');		
		$userArr	= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid		= $userArr[0];//uid

    	$where['id']   =  $uid;

    	$rs= Model('Users')
		    	->where($where)
		    	->value('fund_password');
		return ($value == auth_code($rs, 'DECODE'))?true:4;
    }

    /**
     * [price 验证用户余额]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function price($value){
		$token = input('post.token/s');		
		$userArr	= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid		= $userArr[0];//uid

    	$where['uid']   =  $uid;
    	$rs = Model('UserTotal')
	    	->where($where)
	    	->value('balance');

	    return ($rs >= $value)?true:5;
    }

    /**
     * [user_bank_id 验证提现银行卡]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function user_bank_id($value){
		
		$draw_type = input('post.draw_type/s');
		
		$token = input('post.token/s');		
		
		$userArr	= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid		= $userArr[0];//uid

		switch($draw_type){
			case 'bank':
				$rs = Model('UserBank')
						->where('id',$value)
						->count();
			break;
			case 'alipay':
				$rs = Model('Users')
		    	->where('id',$uid)
				->where('alipay',$value)
		    	->count();

			break;
		}
		
				
		return ($rs)?true:7;
    }

    /**
     * [draw_id 验证提现银行卡所属银行]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function draw_id($value){
		$draw_type = input('post.draw_type/s');
		
		$token = input('post.token/s');		
		$userArr	= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid		= $userArr[0];//uid
		
		switch($draw_type){
			case 'bank':

			$bid = Model('UserBank')
					->where('id',$value)
					->value('bid');

			$rs = Model('Bank')->where([
						['id','=',$bid],
						['q_state','=',1],
					])
					->count();
			break;
			
			case 'alipay':
				$rs = Model('Users')
		    	->where('id',$uid)
				->where('alipay',$value)
		    	->count();
			break;
		}
		
		return ($rs)?true:8;
    }
}