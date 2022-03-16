<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\controller;

use app\manage\controller\Common;
/**
	A-阿大
	系统设置控制器
**/
class BaseController extends CommonController{
	//空操作处理
	public function _empty(){
		return $this->setting();
	}

	//权限管理
	public function role_list(){

		$data = model('ManageRole')->role_list();
		//权限列表
		$this->assign('list',$data['role_list']);
		//权限
		$this->assign('power',$data['power']);
		return $this->fetch();

	}
	//添加权限
	public function role_add(){
		if($this->request->isAjax()){
			$param = model('ManageRole')->add_role();
			return $param;
		}

		$data = model('ManageRole')->getRoleByIdAdd();
		$this->assign('data',$data);

		return $this->fetch();
	}

	//修改权限
	public function role_edit(){

		if($this->request->isAjax()){
			$param = model('ManageRole')->role_edit();
			return $param;
		}

		$data = $data = model('ManageRole')->getRoleByIdEdit();
		$this->assign('data',$data);
		return $this->fetch();

	}
	//删除权限
	public function role_delete(){
		$param = model('ManageRole')->role_delete();
		return $param;
	}

	//管理员admins
	public function admins(){
		if (request()->isAjax()) {
			$param = input('post.');

			$count              = model('Manage')->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'id';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			$data = model('Manage')->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();

			return json([
				'code'  => 0,
				'msg'   => '',
				'count' => $count,
				'data'  => $data
			]);
		}
		return view();
	}
	//添加管理员
	public function admins_add(){
		if($this->request->isAjax()){
			$param = model('Manage')->admins_add();
			return $param;
		}
		return $this->fetch();
	}
	//修改管理员
	public function admins_edit(){
		//修改管理员信息
		if($this->request->isAjax()){
			$data = model('Manage')->admins_edit();
			return $data;
		}

		$data = model('Manage')->getAdminsById();
		$this->assign('data',$data);

		return $this->fetch();
	}
	//删除管理
	public function admins_delete(){
		$param = model('Manage')->admins_delete();
		return $param;
	}
	//管理权限管理
	public function admins_set_role(){
		//权限开关
		if($this->request->isAjax()){
			$param = model('Manage')->admins_set_role_do();
			return $param;
		}

		$param = model('Manage')->admins_set_role();
		$this->assign('list',$param);
		return $this->fetch();

	}

	/**
	 * 后端白名单
	 */
	public function ip_white(){
		if (request()->isAjax()) {
			$param = input('post.');

			$count              = model('IpWhite')->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'id';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			$data = model('IpWhite')->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();

			return json([
				'code'  => 0,
				'msg'   => '',
				'count' => $count,
				'data'  => $data
			]);
		}
		return view();
	}

	/**
	 * 白名单添加
	 */
	public function ip_white_add(){

		if(request()->isAjax()){
			return model('IpWhite')->ipWhiteAdd();
		}
		return $this->fetch();
	}

	/**
	 * IP删除
	 */
	public function ip_white_delete(){
		return model('IpWhite')->ipWhiteDel();
	}

	/**
	 * 公告
	 */
	public function notice(){
		if (request()->isAjax()) {
			$param = input('post.');

			$count              = model('Notice')->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'add_time';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			$data = model('Notice')->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			foreach ($data as $key => &$value) {
				$value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
			}

			return json([
				'code'  => 0,
				'msg'   => '',
				'count' => $count,
				'data'  => $data
			]);
		}
		return view();
	}

	/**
	 * 添加公告
	 */
	public function notice_add(){
		if(request()->isAjax()){
			return model('Notice')->noticeAdd();
		}
		$noticeGroup = model('NoticeGroup')->field('id,group_name')->select();

		$this->assign('noticeGroup',$noticeGroup);

		return $this->fetch();
	}

	/**
	 * 编辑公告
	 */
	public function notice_edit(){

		$data = model('Notice')->noticeEditView();

		$this->assign('data',$data['data']);
		$this->assign('noticeGroup',$data['noticeGroup']);

		return $this->fetch();
	}

	/**
	 * 删除公告
	 */
	public function notice_delete(){
		return model('Notice')->noticeDel();
	}

	/**
	 * 基本设置
	 */
	public function setting(){
		$data = model('Setting')->getFieldsById();

		$this->assign('data',$data);

		return $this->fetch();
	}

	/**
	 * 基本设置修改
	 */
	public function setting_edit(){
		return model('Setting')->settingEdit();
	}

	/*
	 *	公告分类列表	老李
	 */
	public function groupList() {
		if (request()->isAjax()) {
			//获取参数
			$param = input('post.');

			$count              = model('NoticeGroup')->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'addtime';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			$data = model('NoticeGroup')->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			foreach ($data as $key => &$value) {
				$value['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
			}

			return json([
				'code'  => 0,
				'msg'   => '',
				'count' => $count,
				'data'  => $data
			]);
		}

		return view();
	}

	/*
	 *	公告分类添加	老李
	 */
	public function groupAdd() {
		if(request()->isAjax()){
			return model('NoticeGroup')->groupAdd();
		}
		return $this->fetch();
	}

	/*
	 *	公告分类编辑	老李
	 */
	public function groupEdit() {
		if(request()->isAjax()){
			return model('NoticeGroup')->groupAdd();
		}
		$id = input('get.id/d');

		$data = model('NoticeGroup')->where('id',$id)->find();

		$this->assign('data',$data);

		return $this->fetch();
	}

	public function groupDel(){
		return model('NoticeGroup')->groupDel();
	}

	/**
	 * 绑定聊天群组
	 */
	public function bindChatGroup(){
		$data = model('BindChatGroup')->bindChatGroup();

		return view('', [
			'bindGroupList' =>	$data['bindGroupList'],
			'page'          =>	$data['page'],
			'where'         =>	$data['where'],
			'power'         =>	$data['power'],
			'lotteryList'   =>	$data['lotteryList'],
		]);
	}

	/**
	 * 添加绑定
	 */
	public function addBindGroup(){
		if(request()->isAjax()){
			return model('BindChatGroup')->addBindGroup();
		}

		$lotteryList = model('PlayClass')->field('class,class_name')->where(array(['name','<>',''],['is_chat','=',1]))->select()->toArray();

		return view('', [
			'lotteryList'	=>	$lotteryList
		]);
	}

	/**
	 * 编辑绑定
	 */
	public function editBindGroup(){
		if(request()->isAjax()){
			return model('BindChatGroup')->editBindGroup();
		}

		$param = input('get.');

		$bindInfo = model('BindChatGroup')->where('id', $param['id'])->find()->toArray();

		$lotteryList = model('PlayClass')->field('class,class_name')->where(array(['name','<>',''],['is_chat','=',1]))->select()->toArray();

		return view('', [
			'bindInfo'    =>	$bindInfo,
			'lotteryList' =>	$lotteryList
		]);
	}

	/**
	 * 删除绑定
	 */
	public function delBindGroup(){
		return model('BindChatGroup')->delBindGroup();
	}

	/**
	 * 聊天房间
	 */
	public function chatRoom(){
		$data = model('ChatRoom')->chatRoom();

		return view('', [
			'chatRoomList' =>	$data['chatRoomList'],
			'page'         =>	$data['page'],
			'where'        =>	$data['where'],
			'power'        =>	$data['power'],
		]);
	}

	/**
	 * 添加绑定
	 */
	public function addChatRoom(){
		if(request()->isAjax()){
			return model('ChatRoom')->addChatRoom();
		}

		return view();
	}

	/**
	 * 编辑绑定
	 */
	public function editChatRoom(){
		if(request()->isAjax()){
			return model('ChatRoom')->editChatRoom();
		}

		$param = input('get.');

		$chatRoom = model('ChatRoom')->where('id', $param['id'])->find()->toArray();

		return view('', [
			'chatRoom'    =>	$chatRoom
		]);
	}

	/**
	 * 删除绑定
	 */
	public function delChatRoom(){
		return model('ChatRoom')->delChatRoom();
	}

	//权限管理
	public function merchantRole(){

		$data = model('AdminRole')->role_list();
		//权限列表
		$this->assign('list',$data['role_list']);
		//权限
		$this->assign('power',$data['power']);
		return $this->fetch();

	}
	//添加权限
	public function merchantRoleAdd(){
		if (request()->isAjax()) return  model('AdminRole')->add_role();

		$data = model('AdminRole')->getRoleByIdAdd();
		$this->assign('data',$data);

		return $this->fetch();
	}

	//修改权限
	public function merchantRoleEdit(){
		if (request()->isAjax()) return model('AdminRole')->role_edit();

		$data = $data = model('AdminRole')->getRoleByIdEdit();
		$this->assign('data',$data);
		return $this->fetch();

	}
	//删除权限
	public function merchantRoleDel(){
		return model('AdminRole')->role_delete();
	}

	public function setMerchantRole(){
		return model('AdminRole')->setMerchantRole();
	}

	/**
	 * 上传文件
	 * @return [type] [description]
	 */
	public function upload(){
		$param = input('param.');
		//文件名
		$fileName = (isset($param['fileName']) && $param['fileName']) ? $param['fileName'] : trading_number();
		// 目标文件夹
		$targetFolder = (isset($param['targetFolder']) && $param['targetFolder']) ? $param['targetFolder'] : 'file';
		//二维码图片
		$file = request()->file('file');
		//上传路径
		$uploadPath = './upload/'.$targetFolder;
		if(!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

		$info = $file->validate(['size'=>1000*1024*10])->rule('date')->move($uploadPath, $fileName);
		if($info){
			// 成功上传后 获取上传信息
			return json(['code'=>1,'success'=>ltrim($uploadPath, '.').'/'.$info->getSaveName()]);
		}else{
			// 上传失败获取错误信息
			return json(['code'=>0,'success'=>$file->getError()]);
		}
	}


	// 幻灯片
	public function slideList(){
		if (request()->isAjax()) {
			$param = input('post.');

			$count              = model('Slide')->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'id';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			$data = model('Slide')->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();

			return json([
				'code'  => 0,
				'msg'   => '',
				'count' => $count,
				'data'  => $data
			]);
		}
		return view();
	}
	// 添加幻灯片
	public function slideAdd(){
		if (request()->isAjax()) return model('Slide')->add();
		return view();
	}
	// 删除幻灯片
	public function slideDel(){
		return model('Slide')->del();
	}
	// 改变状态
	public function slideStatus(){
		return model('Slide')->setField();
	}
}
