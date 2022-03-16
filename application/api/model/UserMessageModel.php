<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\api\model;

use think\Model;

class UserMessageModel extends Model{
	/**	留言 **/
	protected $table	= 'ly_user_message';		// 留言表
	
	
	/** 发表留言或评论 **/
	public function makeMessage(){
		$token		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];
		
		$userInfo	= model('Users')->where('id', $uid)->find();
		if(!$userInfo){
			return ['code' => 0, 'code_dec'	=> '用户不存在'];
		}
		
		$message	= input('post.message/s');	// 留言内容
		if(!$message){
			return ['code' => 0, 'code_dec'	=> '留言不能为空'];
		}
		
		$picture_url= $param['pictures'];		// 上传图片文件名，“,”分隔
		
		$mName		= input('post.mname/s');	// 留言者姓名
		$mPhone		= input('post.mphone/d');	// 留言者电话
		$mMail		= input('post.memail/s');	// 留言者邮箱
		$mAddress	= input('post.maddress/s');	// 留言者地址
		$type		= input('post.type/d');		// 留言者方式。1=留言，2=评论
		
		if(empty($mPhone)) return ['code'=>0, 'code_dec'=>'电话号码不能为空'];
		
		$messageData	= [
			'uid'			=> $uid,
			'message'		=> $message,
			'time'			=> time(),
			'mName'			=> ($mName) ? $mName : '',
			'mPhone'		=> $mPhone,
			'mMail'			=> ($mMail) ? $mMail : '',
			'mAddress'		=> ($mAddress) ? $mAddress : '',
			'type'			=> $type,
			'picture_url'	=> ($picture_url) ? $picture_url : '',
		];
		if($type==2){	// 如果为评论，则需要有留言id
			$message_id		= input('post.message_id/d');
			if(empty($message_id)) return ['code'=>0, 'code_dec'=>'留言id不能为空'];
			$messageData['message_id']	= $message_id;
		}
		$message_id = $this->insertGetId($messageData);
		
		if(!$message_id) {
			return ['code' => 0, 'code_dec'	=> '发言失败'];			
		}
		
		return ['code' => 1, 'code_dec'	=> '发言成功'];
	}
	
	
	/** 获取留言列表 **/
	public function getMessageList(){
		$token		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));
		$uid		= $userArr[0];
		
		$chk_uid	= model('Users')->where('id', $uid)->count();
		if(!$chk_uid){
			return ['code' => 0, 'code_dec'	=> '获取留言失败'];
		}
				
		$messageCount	= $this->where('type', 1)->count();		// 获取最新留言的记录总数
		
		$pageSize		= (isset($post['page_size']) and $post['page_size']) ? $post['page_size'] : 15;	//每页显示记录
		
		$pageNo			= (isset($post['page_no']) and $post['page_no']) ? $post['page_no'] : 1;		//当前的页,还应该处理非数字的情况
		
		$pageTotal		= ceil($messageCount / $pageSize);												//总页数。当前页数大于最后页数，取最后
		
		$limitOffset	= ($pageNo - 1) * $pageSize;													//记录数
		
		// 按最新发布时间排序方式，获取所有朋友的留言
		$messageData	= $this->where(['type'=>1,'is_show'=>1])->order('time desc')->limit($limitOffset, $pageSize)->select()->toArray();
		if(!count($messageData)){
			return ['code' => 0, 'code_dec'	=> '暂无留言'];
		}
		//dump($messageData);die;
		foreach($messageData as $key => $value){
			// 留言信息
			$data['list'][$key]['m_id']			= $value['id'];								// 留言id
			$data['list'][$key]['message']		= $value['message'];						// 留言内容
			$data['list'][$key]['time']			= $this->conversionTime($value['time']);	// 留言时间
			if($uid == $value['uid']){
				$data['list'][$key]['is_del']	= 1;										// 本人发表的留言是否可以删除。1=可以删除，2=不能删除
			}else{
				$data['list'][$key]['is_del']	= 2;
			}
			// 留言者个人信息
			$userInfo	= model('Users')->where('id', $value['uid'])->find();
			$data['list'][$key]['uid']			= $value['uid'];							// 用户ID
			$data['list'][$key]['username']		= $userInfo['username'];					// 用户名
			$data['list'][$key]['realname']		= $userInfo['realname'];					// 用户名称
			$data['list'][$key]['header']		= $userInfo['header'];						// 头像
			// 图片列表
			$data['list'][$key]['pictures']		= '';
			if(!empty($value['picture_url'])){
				$data['list'][$key]['pictures']	= explode(',', $value['picture_url']);		// 图片列表
			}			
			// 评论
			$commentData	= $this->where(['message_id'=>$value['message_id'],'is_show'=>1,'type'=>2])->order('id asc')->select()->toArray();
			if(!count($commentData)) continue;
			foreach($commentData as $commKey => $commValue){
				$commUserData	= model('Users')->where('id', $commValue['uid'])->find();
				$commUsername	= ($commUserData['realname']) ? $commUserData['realname'] : $commUserData['username'];
				$data['list'][$key]['comment'][$commKey]	= $commUsername.' : '.$commValue['comment'];
			}			
		}
		
		$data['code']		= 1;
		$data['pageNo']		= $pageNo;
		$data['pageSize']	= $pageSize;
		return $data;
	}
	
	
	/** 删除留言 **/
	public function deleteMessage(){
		$token		= input('post.token/s');
		$userArr	= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid		= $userArr[0];
		$message_id	= input('post.message_id/d');
		
		$chk_uid	= model('Users')->where('id', $uid)->count();
		if(!$chk_uid){
			return ['code' => 0, 'code_dec'	=> '删除失败'];
		}
		
		$chk_message_id	= $this->where('id', $message_id)->count();
		if(!$chk_message_id){
			return ['code' => 0, 'code_dec'	=> '删除失败'];
		}
		
		// 首先删除与留言相关的评论
		$del_comment	= $this->where('message_id', $message_id)->delete();
		
		$del_message	= $this->where('id', $message_id)->delete();
		
		if($del_message) return [1, '删除成功'];
		else return [0, '删除失败'];
	}
	
	
	/** 转换显示时间 **/
	protected function conversionTime($time){
		$toDay_startTime	= strtotime(date("Y-m-d 00:00:00", time()));
		$toDay_nowTime		= time();
		
		// 今天的留言
		if($time > $toDay_startTime){
			$toDay_timeDifference	= $toDay_nowTime - $time;
			if($toDay_timeDifference > 3600){	// 小时前的留言
				return ceil($toDay_timeDifference / 3600)."小时前";
			}
			
			if($toDay_timeDifference > 60){		// 分钟前的留言
				return ceil($toDay_timeDifference / 60)."分钟前";;
			}
			
			return $toDay_timeDifference."秒前";;
		}
		
		// 昨天以前的留言
		$beforeTime	= strtotime(date("Y-m-d 00:00:00", $time));
		
		$days	= round(($toDay_startTime - $beforeTime)/3600/24);
		if($days == 1) return '昨天';
		else return $days.'天前';
	}
	
	

	
	
}