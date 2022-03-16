<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\common\validate;

use think\Validate;

class Common extends Validate{
    protected $rule =   [
        'qrcode'  =>  'require|url',
        'imgName' =>  'require|alphaDash',

        'file'       =>  'require|alphaDash',
        'uploadPath' =>  'require|url',
        'validate'   =>  'array',
        'rule'       =>  'alphaDash',
    ];

    protected $message =   [
        'qrcode.require'     =>  '缺少必要参数 - 1',
        'qrcode.url'         =>  '缺少必要参数 - 1 - 1',
        'imgName.integer'    =>  '缺少必要参数 - 2',
        'imgName.alphaDash'  =>  '缺少必要参数 - 2 - 1',
        
        'file.require'       =>  '缺少必要参数 1',
        'file.alphaDash'     =>  '必要参数 1 格式错误',
        'uploadPath.require' =>  '缺少必要参数 2',
        'uploadPath.url'     =>  '必要参数 2 格式错误',
        'validate.array'     =>  '验证失败',
        'rule.alphaDash'     =>  '验证失败',
        'fileName.alphaDash' =>  '文件名格式错误',
        'fileName.boolean'   =>  '文件名格式错误',
    ];

    protected $scene = [
        'produceQrcode' =>  ['qrcode', 'imgName'],  // logoPath
        'upload'        =>  ['file', 'uploadPath', 'validate', 'rule'],
    ];
}