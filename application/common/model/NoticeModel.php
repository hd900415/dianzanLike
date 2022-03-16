<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\common\model;

use think\Model;

class NoticeModel extends Model{
	//表名
	protected $table = 'ly_notice';

	/**
	 * [getNotice 平台公告]
	 * @return [type] [description]
	 */
	public function getNotice(){
		//获取公告列表
		$param 		= input('post.');

		if (isset($param['gropid']) && $param['gropid']) {
			$notice = $this->where(array('gropid'=>$param['gropid']))->order('add_time','desc')->select()->toArray();
		} else {
			$notice = $this->order('add_time','desc')->select()->toArray();		
		}
		
		if(!$notice){
			$data['code'] = 0;
			return $data;
		}

		$data['code'] = 1;
		foreach ($notice as $key => $value) {
			$noticedata[$key]['title']    = $value['title'];
			$noticedata[$key]['content']	= $value['content'];
			$noticedata[$key]['add_time'] = date('Y-m-d H:i:s',$value['add_time']);
		}
		
		$data['info'] = $noticedata;
		
		return $data;
	}

}