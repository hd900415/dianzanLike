<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\validate;

use think\Validate;

class Recharge extends Validate
{
	protected $rule =   [
        // 用户充值订单
        'recharge_id' => 'require|float|chenkRechargeType:1',
        'money'       => 'require|float'
	];
    
    protected $message  =   [
        // 用户充值订单
        'recharge_id.require' => '提交失败',
        'recharge_id.float'   => '提交失败',
        'money.require'       => '提交失败',
        'money.float'         => '提交失败',
    ];
    
    protected $scene = [
        'userRechargeSub' => ['recharge_id','money'], // 用户充值订单
    ];

    /**
     * 验证充值渠道
     */
    protected function chenkRechargeType($value){
        $data = model('RechangeType')->where('id', $value)->count();
        if (!$data) return '提交失败';

        return true;
    }
}