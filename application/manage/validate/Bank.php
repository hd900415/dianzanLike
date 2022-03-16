<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\manage\validate;

use think\Validate;

class Bank extends Validate
{
    protected $rule =   [
        //银行配置
        'bank_name'         =>  'require',
        'bank_code'         =>  'require|alphaDash',
        'q_start_time'      =>  'require|dateFormat:H:i:s',
        'q_end_time'        =>  'require|dateFormat:H:i:s',
        'q_min'             =>  'require|float|lt:q_max',
        'q_max'             =>  'require|float|gt:q_min',
        'c_start_time'      =>  'require|dateFormat:H:i:s',
        'c_end_time'        =>  'require|dateFormat:H:i:s',
        'c_min'             =>  'require|float|lt:c_max',
        'c_max'             =>  'require|float|gt:c_min',
        //充值渠道
        'name'              =>  'require',
        'code'              =>  'require|alphaDash',
        'submitUrl'         =>  'require|url',
        'fee'               =>  'require|float',
        'minPrice'          =>  'require|float|lt:maxPrice',
        'maxPrice'          =>  'require|float|gt:minPrice',
        'mode'              =>  'require|alphaDash',
        'type'              =>  'require|alpha',
        'sort'              =>  'require|number',
        'state'             =>  'require|number',
        //收款账号
        'account'           =>  'require',
        'bank'              =>  'require',
        //二维码上传
        'fileName'          =>  'require|alphaDash',
        //收款二维码
        'qrcodeType'        =>  'require|number',
        'qrcodeName'        =>  'require|chsAlphaNum',
        'qrcodeAccount'     =>  'require|alphaDash',
        'qrcode'            =>  'require|regex:/^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/',
    ];

    protected $message =   [
        'bank_name.require'         =>  '银行名称必须填写',
        'bank_name.chsAlpha'        =>  '银行名称只能是汉字、字母',

        'bank_code.require'         =>  '直连代码必须填写',
        'bank_code.alphaDash'       =>  '直连代码只能是汉字、字母、下划线、破折号',

        'q_start_time.require'      =>  '取款开始时间必须填写',
        'q_start_time.dateFormat'   =>  '取款开始时间格式不正确',

        'q_end_time.require'        =>  '取款结束时间必须填写',
        'q_end_time.dateFormat'     =>  '取款结束时间格式不正确',

        'q_min.require'             =>  '最小取款金额必须填写',
        'q_min.float'               =>  '最小取款金额必须是数字',
        'q_min.lt'                  =>  '最小取款金额不能大于等于最大取款金额',

        'q_max.require'             =>  '最大取款金额必须填写',
        'q_max.float'               =>  '最大取款金额必须是数字',
        'q_max.gt'                  =>  '最大取款金额不能小于等于最小取款金额',

        'c_start_time.require'      =>  '充值开始时间必须填写',
        'c_start_time.dateFormat'   =>  '充值开始时间格式不正确',

        'c_end_time.require'        =>  '充值结束时间必须填写',
        'c_end_time.dateFormat'     =>  '充值结束时间格式不正确',

        'c_min.require'             =>  '最小充值金额必须填写',
        'c_min.float'               =>  '最小充值金额必须是数字',
        'c_min.lt'                  =>  '最小充值金额不能大于等于最大取款金额',

        'c_max.require'             =>  '最大充值金额必须填写',
        'c_max.float'               =>  '最大充值金额必须是数字',
        'c_max.gt'                  =>  '最大充值金额不能小于等于最小取款金额',

        'name.require'              =>  '名称必须填写',
        'name.chsAlphaNum'          =>  '名称只能是汉字、字母、数字',

        'code.require'              =>  '渠道编码必须填写',
        'code.alphaDash'            =>  '渠道编码只能是汉字、字母、下划线、破折号',

        'submitUrl.require'         =>  '提交地址必须填写',
        'submitUrl.url'             =>  '提交地址格式不正确',

        'fee.require'               =>  '手续费必须填写',
        'fee.float'                 =>  '手续费必须是数字',

        'minPrice.require'          =>  '最小充值金额必须填写',
        'minPrice.float'            =>  '最小充值金额必须是数字',
        'minPrice.lt'               =>  '最小充值金额不能大于等于最大充值金额',

        'maxPrice.require'          =>  '最大充值金额必须填写',
        'maxPrice.float'            =>  '最大充值金额必须是数字',
        'maxPrice.gt'               =>  '最大充值金额不能小于等于最小充值金额',

        'mode.require'              =>  '渠道类型必须选择',
        'mode.alphaDash'            =>  '渠道类型格式不正确',

        'type.require'              =>  '渠道所属客户端必须选择',
        'type.alpha'                =>  '渠道所属客户端格式不正确',

        'sort.require'              =>  '渠道排序必须填写',
        'sort.number'               =>  '渠道排序必须是数字',

        'state.require'             =>  '渠道状态必须填写',
        'state.number'              =>  '渠道状态必须是数字',

        'account.require'           =>  '收款账号必须填写',
        'account.number'            =>  '收款账号必须是数字',

        'bank.chsAlpha'             =>  '开户行只能是汉字、字母',

        'fileName.require'          =>  false,
        'fileName.regex'            =>  false,

        'qrcodeType.require'        =>  '请选择收款渠道',
        'qrcodeType.number'         =>  '收款渠道格式不正确',

        'qrcodeName.require'        =>  '请填写通道名称',
        'qrcodeName.chsAlpha'       =>  '通道名称只能是汉字、字母和数字',

        'qrcodeAccount.require'     =>  '请填写收款账号',
        'qrcodeAccount.alphaDash'   =>  '收款账号只能是字母和数字和下划线',

        'qrcode.require'            =>  '请选择二维码',
        'qrcode.regex'              =>  '二维码URL不正确',
    ];

    protected $scene = [
        'bankadd'               =>  ['bank_name','bank_code','q_start_time','q_end_time','q_min','q_max','c_start_time','c_end_time','c_min','c_max'],
        'rechargeadd'           =>  ['name','code','submitUrl','minPrice','maxPrice','mode','type','sort','state'],
        'receivablesAdd'        =>  ['name','account','bank'],
        'qrcodeUpload'          =>  ['fileName'],
        'receivablesQrcodeAdd'  =>  ['qrcodeType','qrcodeName','qrcodeAccount','qrcode'],
    ];
}