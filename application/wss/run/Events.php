<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events{
    /**
     * 当客进程开始时触发
     * 
     * @param $businessWorker 进程实例
     */
    public static function onWorkerStart($businessWorker){
        
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id){
        Gateway::sendToClient($client_id, json_encode(['type'=>'init','client_id'=>$client_id]));
    }
    
    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接客户端id
     * @param int $id 房间或会员id
     * @param mixed $message 具体消息
     * @param [type] $sendType  接收类型 bind|绑定，chat|聊天，ping|心跳
     * @param [type] $msgType   消息类型 text|普通消息，images|发送图片，
     * @param [type] $userData  用户信息
     * @param [type] $online    在线用户数
     */
    public static function onMessage($client_id, $message){
        $param = json_decode($message, true);
        if (!isset($param['sendType']) || !$param['sendType']){
            Gateway::sendToClient($client_id, 
                json_encode([
                    'type' => 'error',
                    'code_dec' => '非法操作'
                ])
            );
            return;
        }
        // 检查客户端是否在线
        if (!Gateway::isOnline($client_id)) {
            Gateway::sendToClient($client_id, 
                json_encode([
                    'type' => 'offline',
                    'code_dec' => '通信已断开'
                ])
            );
            // $id = Gateway::getUidByClientId($client_id);
            // self::getGroupMember($id);
        } else {
            switch ($param['sendType']) {
                // 绑定用户信息
                case 'bind':
                    if (!isset($param['uid']) || !$param['uid'] || !isset($param['room']) || !$param['room']){
                        Gateway::sendToClient($client_id, 
                            json_encode([
                                'type' => 'error',
                                'code_dec' => '非法操作'
                            ])
                        );
                        return;
                    }

                    // onClose 使用
                    $_SESSION['room'] = $param['room'];

                    //查找房间是否已存在该用户
                    $old_client_id;
                    $member = Gateway::getClientSessionsByGroup($param['room']);
                    $uid = array_column($member, 'uid');
                    // 当前登录会员是否已经在线
                    if(in_array($param['uid'], $uid)){
                        foreach($member as $key=>$value) {
                            if($value['uid']==$param['uid']){
                                $old_client_id = $key;
                                break;
                            }
                        }
                        // 把之前用户下线
                        Gateway::sendToClient($old_client_id, 
                            json_encode([
                                'type' => 'logout',
                                'code_dec' => '账户在别处登录，已自动退出'
                            ])
                        );
                        Gateway::closeClient($old_client_id);
                    }
                    // if($param['isLogin']){
                    //     // 全部在线用户信息
                    //     $all_member = Gateway::getAllClientSessions();
                    //     $all_uid = array_column($all_member, 'uid');
                        
                    //     // 当前登录会员是否已经在线
                    //     if(in_array($param['userData']['uid'], $all_uid)){
                    //         foreach($all_member as $key=>$value) {
                    //             if($value['uid']==$param['userData']['uid']){
                    //                 $old_client_id = $key;
                    //                 break;
                    //             }
                    //         }
                    //         // 把之前用户下线
                    //         Gateway::sendToClient($old_client_id, 
                    //             json_encode([
                    //                 'type' => 'logout',
                    //                 'code_dec' => '账户在别处登录，已自动退出'
                    //             ])
                    //         );
                    //     }
                        
                    // }else{
                    //     // 查找房间是否已存在该用户
                    //     $member = Gateway::getClientSessionsByGroup($param['id']);
                    //     $uid = array_column($member, 'uid');
                    //     if(in_array($param['userData']['uid'], $uid)){
                    //         foreach($member as $key=>$value) {
                    //             if($value['uid']==$param['userData']['uid']){
                    //                 $old_client_id = $key;
                    //                 break;
                    //             }
                    //         }
                    //         Gateway::sendToClient($old_client_id, 
                    //             json_encode([
                    //                 'type' => 'exists',
                    //                 'code_dec' => '当前用户在别处进入该房间，你已被断开'
                    //             ])
                    //         );
                    //     }
                    // }
                    // 绑定用户信息
                    self::bindUser($client_id,$param['room'],$param['uid']);
                    break;
                // 聊天
                // case 'chat':
                //     if (!isset($param['msgType']) || !$param['msgType'] || !isset($param['id']) || !$param['id']){
                //         Gateway::sendToClient($client_id, 
                //             json_encode([
                //                 'type' => 'error',
                //                 'code_dec' => '非法操作'
                //             ])
                //         );
                //         return;
                //     }
                //     // 获取当前用户Session
                //     $session = Gateway::getSession($client_id);

                //     Gateway::sendToGroup($param['id'], 
                //         json_encode([
                //             'type' => 'message',
                //             'msg_type' => $param['msgType'],
                //             'time' => $param['msgDate'],
                //             'msg' => self::filterScript($param['msgCon']),
                //             'user' => $session['user_data'],
                //         ])
                //     );
                //     break;
                // 获取在线用户
                // case 'member':
                //     self::getGroupMember($param['id']);
                //     break;
            }
        }
        
    }
    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id){
        // if(isset($_SESSION['room'])){
        //     self::getGroupMember($_SESSION['room']);
        // }
    }
    /**
     * 绑定用户信息
     * @param int $client_id 连接id
     * @param int $id 用户或房间ID
     * @param int $user_data 用户信息
     */
    public static function bindUser($client_id,$room,$uid){
        $_SESSION['uid'] = $uid;
        Gateway::bindUid($client_id, $uid);
        // 私聊群聊都用joinGroup绑定
        Gateway::joinGroup($client_id, $room);

        Gateway::sendToClient($client_id, 
            json_encode([
                'type' => 'bind',
                'code_dec' => '绑定成功'
            ])
        );
        // self::getGroupMember($id);
    }
    /**
     * 获取在线成员信息
     * @param [type] $id        房间号
     * @param [type] $all_online    在线总用户数
     * @param [type] $all    在线总用户
     * @param [type] $group_online    群组在线用户数
     * @param [type] $group    群组在线用户
     */
    // public static function getGroupMember($id){
    //     // 全部在线用户
    //     $all_online = Gateway::getAllClientCount();
    //     $all = Gateway::getAllClientSessions();
    //     $allArr = array();
    //     foreach($all as $key=>$value) {
    //         array_push($allArr, $value['user_data']);
    //     }
    //     // 当前群组在线用户
    //     $group_online = Gateway::getClientIdCountByGroup($id);
    //     $group = Gateway::getClientSessionsByGroup($id);
    //     $groupArr = array();
    //     foreach($group as $key=>$value) {
    //         array_push($groupArr, $value['user_data']);
    //     }
    //     Gateway::sendToGroup($id, 
    //         json_encode([
    //             'type' => 'member',
    //             'all_online' => $all_online,
    //             'all' => $allArr,
    //             'group_online' => $group_online,
    //             'group' => $groupArr
    //         ])
    //     );
    // }
    // public static function filterScript($str){
    //     // 过滤JS
    //     $pattern = array(
    //         '/script|javascript|iframe|frame|frameset|html|body|link|meta|object|layer/',
    //         '/(javascript:)?on(click|load|key|mouse|error|abort|activate|move|unload|change|dblclick|move|reset|resize|submit|scroll|select|start|stop|focus|before|after|blur|bounce|cellchange|data|drag|filterchange)/i'
    //     );
    //     return preg_replace($pattern,'',$str);
    // }
}
