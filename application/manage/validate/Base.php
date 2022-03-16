<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\manage\validate;

use think\Validate;

class Base extends Validate
{
    protected $rule =   [
        // IP添加
        'ip'            =>  'require|ip',
        // 公告
        'title'         =>  'require',
        'content'       =>  'require',
        // 公告分类
        'group_name'    =>  'require|chsAlphaNum',
        // 基本设置
        'q_server_name' =>  'require',
        'h_server_name' =>  'require',
        'max_rebate'    =>  'require|number',
        'min_rebate'    =>  'require|number',
        'max_room'      =>  'require|number',
        'admin_title'   =>  'chsDash',
        // 添加绑定群组
        'rank'          => 'require|number|integer',
        'lottery'       => 'require|alphaNum',
        'gid'           => 'require|regex:^(\d+,?)+',
        'min'           => 'require|number|integer',
        'max'           => 'require|number|integer',
        'balance'       => 'require|float',
        // 聊天房间
        'chatroom_name' =>  'require',
        'chatroom_pwd'  =>  'alphaNum',
    ];

    protected $message =   [
        'ip.require'                =>  'IP必须填写',
        'ip.ip'                     =>  'IP无效',
        
        'title.require'             =>  '公告标题必须填写',
        'content.require'           =>  '公告内容必须填写',
        
        'group_name.require'        =>  '请填写分类名称',
        'group_name.chsAlphaNum'    =>  '分类名称只能是汉字、字母和数字',
        
        'q_server_name.require'     =>  '前台域名/IP必须填写',
        'h_server_name.require'     =>  '后台域名/IP必须填写',
        'max_rebate.require'        =>  '最大返点值必须填写',
        'max_rebate.number'         =>  '最大返点值只能填写数字',
        'min_rebate.require'        =>  '最小返点值必须填写',
        'min_rebate.number'         =>  '最小返点值只能填写数字',
        'max_room.require'          =>  '最大房间数必须填写',
        'max_room.number'           =>  '最大房间数只能填写数字',
        'admin_title.chsDash'       =>  '后台网站标题只能是汉字、字母、数字和下划线_及破折号-',
        
        'rank.require'              =>  '请选择房间等级',
        'rank.number'               =>  '房间等级格式错误',
        'rank.integer'              =>  '房间等级格式错误',
        
        'lottery.require'           =>  '请选择彩种',
        'lottery.alphaNum'          =>  '彩种格式错误',
        
        'gid.require'               =>  '请填写群号',
        'gid.regex'                 =>  '群号格式错误',
        
        'min.require'               =>  '请填写最小限额',
        'min.number'                =>  '最小限额格式错误',
        'min.integer'               =>  '最小限额格式错误',
        
        'max.require'               =>  '请选择最大限额',
        'max.number'                =>  '最大限额格式错误',
        'max.integer'               =>  '最大限额格式错误',
        
        'balance.require'           =>  '请填写余额要求',
        'balance.float'             =>  '余额格式错误',
        
        'chatroom_name.require'     =>  '请填写房间名',
        
        'chatroom_pwd.alphaNum'     =>  '密码格式错误',
    ];

    protected $scene = [
        'ipwhiteadd'   =>  ['ip'],
        'noticeadd'    =>  ['title','content'],
        'settingedit'  =>  ['q_server_name','h_server_name','max_rebate','min_rebate','max_room','admin_title'],
        'groupadd'     =>  ['group_name'],
        'addbindgroup' =>  ['rank','lottery','gid','min','max','balance'],
        'addChatRoom'  =>  ['chatroom_name','chatroom_pwd'],
    ];
}