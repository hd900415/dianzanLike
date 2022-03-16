<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\manage\validate;

use think\Validate;

class Users extends Validate
{
    protected $rule =   [
        'username'           =>  'require|alphaNum|unique:users',
        'rebate'             =>  'require|float|chenkRebate:1',
        'password'           =>  'require|alphaNum|length:6,18',
        'repassword'         =>  'require|alphaNum|confirm:password',
        'min_rebate'         =>  'require|float',
        'max_rebate'         =>  'require|float',
        'max_user'           =>  'require|float',
        'bUsername'          =>  'require',
        'artificialUsername' =>  'require|chenkUserName:1',
        'artificialPrice'    =>  'require|regex:/^[-]?\d+\.?\d{0,3}$/',
        'artificialType'     =>  'require|number',
        'artificialSafeCode' =>  'require|chenkSafeCode:1',
        // 用户等级
        'grade'              => 'require|number',
        'mix'                => 'require|integer',
        'max'                => 'require|integer',
        'profit'             => 'require|float',
    ];

    protected $message =   [
        'username.require'           =>  '用户名必须填写',
        'username.alphaNum'          =>  '用户名必须数字或者字母',
        'username.unique'            =>  '用户名已存在',
        
        'rebate.require'             =>  '返点值必须填写',
        'rebate.float'               =>  '返点值必须为数字',
        
        'password.require'           =>  '密码必须填写',
        'password.alphaNum'          =>  '密码必须数字或者字母',
        'password.length'            =>  '密码长度应限制在6-18位',
        
        'repassword.require'         =>  '密码必须填写',
        'repassword.alphaNum'        =>  '密码必须数字或者字母',
        'repassword.confirm'         =>  '两次密码不一致',
        
        'min_rebate.require'         =>  '最小返点必须填写',
        'min_rebate.float'           =>  '最小返点必须是数字',
        'max_rebate.require'         =>  '最大返点必须填写',
        'max_rebate.float'           =>  '最小返点必须是数字',
        'max_user.require'           =>  '最大注册数必须填写',
        'max_user.integer'           =>  '最小返点必须是数字',
        
        'bUsername.require'          =>  '请填写被迁移代理账号',
        'artificialUsername.require' =>  '请填写会员账号',
        'artificialPrice.require'    =>  '请填写金额',
        'artificialType.require'     =>  '请选择类型',
        'artificialSafeCode.require' =>  '请输入安全码',
        // 用户等级
        'grade.require'              =>  '等级必须填写',
        'grade.number'               =>  '等级必须数字',
        'mix.require'                =>  '最小投资必须填写',
        'mix.integer'                =>  '最小投资必须数字',
        'max.require'                =>  '最大投资必须填写',
        'max.integer'                =>  '最大投资必须数字',
        'profit.require'             =>  '收益比例必须填写',
        'profit.float'               =>  '收益比例必须数字或浮点数',
    ];

    protected $scene = [
        'add'             =>  ['username','password','repassword'],
        'ruleadd'         =>  ['min_rebate','max_rebate','max_user'],
        'capital'         =>  ['artificialPrice','artificialType',],
        'artificial'      =>  ['artificialUsername','artificialPrice','artificialType','artificialSafeCode'],
        'artificialBatch' =>  ['artificialType','artificialSafeCode'],
        'teamMove'        =>  ['bUsername','artificialUsername','artificialSafeCode'],
        'userLevelAdd'    =>  ['grade','mix','max','profit'],
    ];

    /**
     * 验证用户返点
     */
    protected function chenkRebate($value){
        $data = model('Setting')->getFieldsById('min_rebate,max_rebate');

        if(!$data){
            $data = array(
                'min_rebate'    =>  0,
                'max_rebate'    =>  8.5,
            );
        }

        if($value < $data['min_rebate']) return '返点值不能低于'.$data['min_rebate'];
        if($value > $data['max_rebate']) return '返点值不能高于'.$data['max_rebate'];

        return true;
    }

    /**
     * 验证用户上庄返点
     */
    protected function chenkBankerRebate($value){
        $data = model('Setting')->getFieldsById('min_banker_rebate,max_banker_rebate');

        if(!$data){
            $data = array(
                'min_banker_rebate'    =>  0,
                'max_banker_rebate'    =>  1.5,
            );
        }

        if($value < $data['min_banker_rebate']) return '上庄返点值不能低于'.$data['min_banker_rebate'];
        if($value > $data['max_banker_rebate']) return '上庄返点值不能高于'.$data['max_banker_rebate'];

        return true;
    }

     /**
     * 验证用户棋牌返点
     */
    protected function chenkChessRebate($value){
        $data = model('Setting')->getFieldsById('min_chess_rebate,max_chess_rebate');

        if(!$data){
            $data = array(
                'min_chess_rebate'    =>  0,
                'max_chess_rebate'    =>  2,
            );
        }

        if($value < $data['min_chess_rebate']) return '上庄返点值不能低于'.$data['min_chess_rebate'];
        if($value > $data['max_chess_rebate']) return '上庄返点值不能高于'.$data['max_chess_rebate'];

        return true;
    }

     /**
     * 验证用户真人返点
     */
    protected function chenkPersonRebate($value){
        $data = model('Setting')->getFieldsById('min_person_rebate,max_person_rebate');

        if(!$data){
            $data = array(
                'min_person_rebate'    =>  0,
                'max_person_rebate'    =>  2,
            );
        }

        if($value < $data['min_person_rebate']) return '上庄返点值不能低于'.$data['min_person_rebate'];
        if($value > $data['max_person_rebate']) return '上庄返点值不能高于'.$data['max_person_rebate'];

        return true;
    }

     /**
     * 验证用户体育返点
     */
    protected function chenkSportsRebate($value){
        $data = model('Setting')->getFieldsById('min_sports_rebate,max_sports_rebate');

        if(!$data){
            $data = array(
                'min_sports_rebate'    =>  0,
                'max_sports_rebate'    =>  2,
            );
        }

        if($value < $data['min_sports_rebate']) return '上庄返点值不能低于'.$data['min_sports_rebate'];
        if($value > $data['max_sports_rebate']) return '上庄返点值不能高于'.$data['max_sports_rebate'];

        return true;
    }

    /**
     * 用户名验证
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function chenkUserName($value){
        $user = model('Users')->where('username',$value)->count();
        if (!$user) return '用户不存在';

        return true;
    }

    /**
     * 安全码验证
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function chenkSafeCode($value){
        $safeCode = model('Manage')->where('id',session('manage_userid'))->value('safe_code');
        if ($value != auth_code($safeCode,'DECODE')) return '安全码不正确';

        return true;
    }
}