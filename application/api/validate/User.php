<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\validate;

use think\Validate;

use think\Db;

/**
 * ============================================================================
 * ============================================================================
 * 用户验证
 */

class User extends Validate
{
    protected $rule =   [
        'user_id'			=> 'require',
        'user_name_n'		=> 'require|length:6,16|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]{5,16}$/|checkRegName:1',
        'nick_name_n'		=> 'length:2,18|regex:/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]+$/u',
        'password_n'		=> 'require|length:6,16|regex:/^[a-zA-Z0-9]{6,15}$/',
        're_password_n'		=> 'confirm:password_n',
		'phone'				=> 'length:11|regex:/^1[3456789][0-9]{9}$/',
        'flevel_fd'			=> 'require|between:5,10|checkFlevelFd:1',
        'flevel_banker_fd'	=> 'require|between:0,1.5|checkFlevelBankerFd:1',
        'idcode'			=> 'require',
		'exp_date'			=> 'require|expDate:1',
        'drawword_o'		=> 'require|checkDrawWordO:1',
        //'drawword_n'		=> 'require|regex:/^[a-z][a-zA-Z0-9]{5,15}$/|checkUserPW:1',
		'drawword_n'		=> 'require|regex:/^[a-zA-Z0-9]{5,15}$/|checkUserPW:1',				// 满足修改的密码可以数字或字母开头。
        'drawword'			=> 'checkDrawWord:1',
        're_drawword_n'		=> 'confirm:drawword_n',
        'password_o'		=> 'require|checkPasswordO:1',
        //'password_nc'		=> 'require|regex:/^[a-z][a-zA-Z0-9]{5,15}$/|checkPassWord:1|checkPassWordN:1',
		'password_nc'		=> 'require|regex:/^[a-zA-Z0-9]{6,16}$/|checkPassWord:1|checkPassWordN:1',  // 满足修改的密码可以数字或字母开头。
        're_password_nc'	=> 'confirm:password_nc',
        'question'			=> 'require|question:1',
        'answer'			=> 'require|answer:1',
		'mail'				=> 'regex:/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
        'qq'				=> 'number',
        'alipay'			=> 'length:4,30',
		'user_type'			=> 'require|in:1,2',

    ];

    protected $message  =   [
        'user_id.require'			=> 0,
        'user_name_n.require'		=> 2,
        'user_name_n.length'		=> 2,
        'user_name_n.regex'			=> 2,
        'nick_name_n.length'		=> 4,
        'nick_name_n.regex'			=> 4,
        'password_n.require'		=> 5,
        'password_n.length'			=> 5,
        'password_n.regex'			=> 5,
        're_password_n.require'		=> 6,
        're_password_n.confirm'		=> 6,
        'flevel_fd.between'			=> 7,
        'flevel_banker_fd.between'	=> 10,
        'idcode.require'			=> 0,
        'exp_date.require'			=> 16,
        'drawword_n.require'		=> 0,
        'drawword_n.regex'			=> 2,
        're_drawword_n.confirm'		=> 3,
        'drawword_o.require'		=> 0,
        'password_o.require'		=> 0,
        'password_nc.require'		=> 0,
        'password_nc.regex'			=> 2,
        're_password_nc.confirm'	=> 3,
        'question.require'			=> 2,
        'answer.require'            => 3,
		'user_type.require'			=> 13,
		'user_type.in'			   	=> 13,
		'mail.regex'				=> 0,
		'phone.require'				=> 0,
		'phone.length'				=> 2,
		'phone.regex'				=> 4,
    ];

    protected $scene = [
        'addUser'		=> ['phone','nick_name_n','password_n','re_password_n','user_type'],
		'addPhoneUser'	=> ['phone','password_n','re_password_n',],
        'regAddUser'	=> ['phone','password_n','re_password_n','idcode'],
        'setDrawPW'		=> ['drawword_n','re_drawword_n'],
        'changeDrawPW'	=> ['drawword_o','drawword_n','re_drawword_n'],
        'changePassword'=> ['password_o','password_nc','re_password_nc'],
        'other'			=> ['nick_name_n','phone','qq','alipay','mail'],
        'security'		=> ['question','answer','drawword_o']
    ];

    /**
     * [checkRegName 验证用户是否存在]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    protected function checkRegName($value){
        $where['username'] = $value;
        $rs = Model('Users')
                ->where($where)
                ->count();
        return ($rs==0)?true:3;
    }

    /**
     * [checkFlevelFd 验证新用户返点是否大于老用户返点]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    protected function checkFlevelFd($value){
		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

		$where['id']               = $uid;
        $rs = Model('Users')
                ->where($where)
                ->value('rebate');

        return ($rs>=$value)?true:12;
    }

    /**
     * [checkFlevelBankerFd 验证新用户上庄返点是否大于老用户上庄返点]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    protected function checkFlevelBankerFd($value){
		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id']               = $uid;
        $rs = Model('Users')
                ->where($where)
                ->value('banker_rebate');
        return ($rs>$value)?true:15;
    }


    /**
     * [expDate 验证注册链接与注册码是否过期]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function expDate($value){
        return true;//(time()<$value)?true:16;
    }

    /**
     * [checkUserPW 验证资金密码是否与登录密码一致]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    protected function checkUserPW($value){

		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id'] =  $uid;
        $rs = Model('Users')
                ->where($where)
                ->value('password');
        return ($value != auth_code($rs,'DECODE'))?true:7;
    }

    /**
     * [checkDrawWordO 验证旧资金密码是否正确]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function checkDrawWordO($value){

		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id'] = $uid;

        $rs = Model('Users')
        ->where($where)
        ->value('fund_password');

        return ($value == auth_code($rs, 'DECODE'))?true:5;
    }

    /**
     * [checkDrawWord 验证新资金密码是否与旧资金密码相同]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function checkDrawWord($value){

		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id'] = $uid;
        $rs = Model('Users')
        ->where($where)
        ->value('fund_password');

        return ($value != auth_code($rs, 'DECODE'))?true:6;
    }

    /**
     * [checkPasswordO 验证旧登录密码是否正确]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function checkPasswordO($value){

		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id'] = $uid;

        $rs = Model('Users')
        ->where($where)
        ->value('password');

        return ($value == auth_code($rs, 'DECODE'))?true:4;
    }

    /**
     * [password 验证新登录密码是否与旧登录密码一致]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function checkPassWordN($value){

		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id'] = $uid;
        $rs = Model('Users')
        ->where($where)
        ->value('password');

        return ($value != auth_code($rs, 'DECODE'))?true:6;
    }

     /**
     * [checkPassWord 验证登录密码是否与资金密码一致]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    protected function checkPassWord($value){

		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id'] = $uid;
        $rs = Model('Users')
                ->where($where)
                ->value('fund_password');
        return ($value != auth_code($rs,'DECODE'))?true:5;
    }

    /**
     * [question 验证是否设置安全问题]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function question($value){

		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id'] = $uid;
        $rs = Model('Users')
                ->where($where)
                ->value('question');
        return ($rs)?true:5;
    }

    /**
     * [answer 验证是否设置安全问题答案]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function answer($value){

		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id'] = $uid;
        $rs = Model('Users')
                ->where($where)
                ->value('answer');
        return ($rs)?true:5;
    }

    /**
     * 验证新用户棋牌返点是否大于上级
     */
    protected function checkCR($value){

		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id'] = $uid;

        $rs = Model('Users')
                ->where($where)
                ->value('chess_rebate');
        return ($value <= $rs)?true:18;
    }

    /**
     * 验证新用户真人返水是否大于上级
     */
    protected function checkPR($value){

		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id'] = $uid;

        $rs = Model('Users')
                ->where($where)
                ->value('person_rebate');
        return ($value <= $rs)?true:19;
    }

    /**
     * 验证新用户体育返点是否大于上级
     */
    protected function checkSR($value){

		$token			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid

        $where['id'] = $uid;

        $rs = Model('Users')
                ->where($where)
                ->value('sports_rebate');
        return ($value <= $rs)?true:20;
    }
}
