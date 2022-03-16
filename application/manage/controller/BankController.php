<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\manage\controller;

use app\manage\controller\Common;

class BankController extends CommonController{
	/**
	 * 空操作处理
	 */
	public function _empty(){
		return $this->lists();
	}

	/**
	 * 银行配置
	 */
	public function lists(){
		$data = model('RechangeType')->RechargeList();

		$this->assign('data',$data['data']);
		$this->assign('power',$data['power']);

		return $this->fetch();
	}

	/**
	 * 添加充值渠道下属银行
	 */
	public function add(){
		if(request()->isAjax()){
			return model('Bank')->rechargeBankAdd();
		}

		$data = model('RechangeType')->rechargeAddView();

		$this->assign('rechargeList',$data['rechargeList']);
		$this->assign('rid',$data['rid']);

		return $this->fetch();
	}

	/**
	 * 编辑充值渠道下属银行
	 */
	public function edit(){
		if(request()->isAjax()){
			return model('Bank')->rechargeBankEdit();
		}
		$data = model('Bank')->rechargeBankEditView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 删除充值渠道下属银行
	 */
	public function delete(){
		return model('Bank')->rechargeBankDel();
	}

	/**
	 * 存取款开关
	 */
	public function bank_on_off(){
		return model('Bank')->onOff();
	}

	/**
	 * 充值渠道
	 */
	public function recharge_channel(){
		$data = model('RechangeType')->RechargeType();

		$this->assign('data',$data['data']);
		$this->assign('where', $data['where']);
		$this->assign('power',$data['power']);

		return $this->fetch();
	}

	/**
	 * 添加充值渠道
	 */
	public function recharge_channel_add(){
		if(request()->isAjax()){
			return model('RechangeType')->rechargeTypeAdd();
		}

		return $this->fetch();
	}

	/**
	 * 充值渠道编辑
	 */
	public function recharge_channel_edit(){
		if(request()->isAjax()){
			return model('RechangeType')->rechargeTypeEdit();
		}

		$data = model('RechangeType')->rechargeTypeEditView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 渠道开关
	 */
	public function recharge_channel_on_off(){
		return model('RechangeType')->onOff();
	}

	/**
	 * 删除充值渠道
	 */
	public function recharge_channel_delete(){
		return model('RechangeType')->rechargeTypeDel();
	}

	/**
	 * 充值记录
	 */
	public function recharge_record(){
		if (request()->isAjax()) {
			$param = input('param.');

			//查询条件组装
			$where = array();

			$where[] = array('ly_user_recharge.type', '<>', 0);
			//用户名搜索
			if (isset($param['user_type']) && $param['user_type']) {
			    $where[] = array('users.user_type', '=', $param['user_type']);
			}
			
			if(isset($param['username']) && $param['username']){
				$uid = model('Users')->where('username', $param['username'])->value('id');
				$where[] = array('ly_user_recharge.uid','=',$uid);
			}
			//订单号搜索
			if(isset($param['order_number']) && $param['order_number']){
				$where[] = array('order_number','=',$param['order_number']);
			}
			//状态搜索
			if(isset($param['state']) && $param['state']){
				$where[] = array('ly_user_recharge.state','=',$param['state']);
			}
			// 时间
			if(isset($param['datetime_range']) && $param['datetime_range']){
				$dateTime = explode(' - ', $param['datetime_range']);
				$where[] = array('add_time','>=',strtotime($dateTime[0]));
				$where[] = array('add_time','<=',strtotime($dateTime[1]));
			}else{
				$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
				$where[] = array('add_time','>=',$todayStart);
				$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
				$where[] = array('add_time','<=',$todayEnd);
			}

// 			$count              = model('UserRecharge')->where($where)->count(); // 总记录数
			$count              = model('UserRecharge')->join('users','ly_user_recharge.uid = users.id')->join('rechange_type','ly_user_recharge.type=rechange_type.id','left')->where($where)->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'ly_user_recharge.add_time';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			$data = model('UserRecharge')->field('ly_user_recharge.*,users.username,rechange_type.name')->join('users','ly_user_recharge.uid = users.id')->join('rechange_type','ly_user_recharge.type=rechange_type.id','left')->where($where)->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			foreach ($data as $key => &$value) {
				switch ($value['state']) {
					case '1':
						$value['statusStr'] = '成功';
						break;
					case '2':
						$value['statusStr'] = '失败';
						break;
					default:
						$value['statusStr'] = '处理中';
						break;
				}
				$value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
				$value['dispose_time']   = date('Y-m-d H:i:s', $value['dispose_time']);
				if ($value['daozhang_money'] <= 0) {
				    $value['daozhang_money'] = $value['money'];
				}
			}

			//权限查询
			// if ($count) $data['power'] = model('ManageUserRole')->getUserPower(['uid'=>session('manage_userid')]);

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
	 * 充值订单审核
	 */
	public function rechargeDispose(){
		if(request()->isAjax()){
			return model('UserRecharge')->rechargeDispose();
		}
		$data = model('UserRecharge')->rechargeDisposeView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 充值订单详情
	 */
	public function rechargeDetail(){
		$data = model('UserRecharge')->rechargeDisposeView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 提现记录
	 */
	public function present_record(){
		if (request()->isAjax()) {
			$param = input('param.');
			//查询条件组装
			$where = array();
			if (isset($param['user_type']) && $param['user_type']) {
			    $where[] = array('users.user_type', '=', $param['user_type']);
			}
			// 状态搜索
			if (isset($param['isUser']) && $param['isUser'] == 1) $pageParam['isUser'] = $param['isUser'];
			//搜索类型
			if(isset($param['search_t']) && $param['search_t'] && isset($param['search_c']) && $param['search_c']){
				switch ($param['search_t']) {
					case 'username':
						$userId = model('Users')->where('username',$param['search_c'])->value('id');
						$where[] = array('ly_user_withdrawals.uid','=',$userId);
						break;
					case 'order_number':
						$where[] = array('ly_user_withdrawals.order_number','=',$param['search_c']);
						break;
					case 'card_name':
						$where[] = array('ly_user_withdrawals.card_name','=',$param['search_c']);
						break;
					case 'card_number':
						$where[] = array('ly_user_withdrawals.card_number','=',$param['search_c']);
						break;
				}
			}

			//状态搜索
			if(isset($param['state']) && $param['state']){
				$where[] = array('ly_user_withdrawals.state','=',$param['state']);
			}
			// 时间
			if(isset($param['datetime_range']) && $param['datetime_range']){
				$dateTime = explode(' - ', $param['datetime_range']);
				$where[] = array('ly_user_withdrawals.time','>=',strtotime($dateTime[0]));
				$where[] = array('ly_user_withdrawals.time','<=',strtotime($dateTime[1]));
			}else{
				$todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
				$where[] = array('ly_user_withdrawals.time','>=',$todayStart);
				$todayEnd = mktime(23,59,59,date('m'),date('d'),date('Y'));
				$where[] = array('ly_user_withdrawals.time','<=',$todayEnd);
			}

			$count              = model('UserWithdrawals')->join('users','ly_user_withdrawals.uid = users.id')->join('manage','ly_user_withdrawals.aid = manage.id','left')->join('bank','ly_user_withdrawals.bank_id = bank.id','left')->where($where)->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'time';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'desc';

			//查询符合条件的数据
			$data = model('UserWithdrawals')->field('ly_user_withdrawals.*,manage.username as aname,users.username,danger,bank.bank_name')->join('users','ly_user_withdrawals.uid = users.id')->join('manage','ly_user_withdrawals.aid = manage.id','left')->join('bank','ly_user_withdrawals.bank_id = bank.id','left')->where($where)->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();
			foreach ($data as $key => &$value) {
				switch ($value['state']) {
					case '1':
						$value['statusStr'] = '成功';
						break;
					case '2':
						$value['statusStr'] = '失败';
						break;
					default:
						$value['statusStr'] = '处理中';
						break;
				}
				$value['time'] = date('Y-m-d H:i:s', $value['time']);
				$value['set_time']   = date('Y-m-d H:i:s', $value['set_time']);
			}

			//权限查询
			// if ($count) $data['power'] = model('ManageUserRole')->getUserPower(['uid'=>session('manage_userid')]);

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
	 * 风控审核
	 */
	public function controlAudit(){
		if(request()->isAjax()){
			return model('UserWithdrawals')->controlAudit();
		}
		$data = model('UserWithdrawals')->controlAuditView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 财务审核
	 */
	public function financialAudit(){
		if(request()->isAjax()){
			return model('UserWithdrawals')->financialAudit();
		}
		$data = model('UserWithdrawals')->controlAuditView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 提现详情
	 */
	public function withdrawalsDetails(){
		$data = model('UserWithdrawals')->controlAuditView();

		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 出款
	 */
	public function withdrawalsPayment(){
		return model('UserWithdrawals')->withdrawalsPayment();
	}

	/**
	 * 收款账号
	 */
	public function receivables(){
		if (request()->isAjax()) {
			$param = input('param.');

			$count              = model('Recaivables')->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'ly_recaivables.id';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'asc';

			//查询符合条件的数据
			$data = model('Recaivables')->field('ly_recaivables.*,bank.bank_name,rechange_type.name as rname')->join('bank','ly_recaivables.bid = bank.id','left')->join('rechange_type','ly_recaivables.type = rechange_type.id','left')->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();

			// 平台会员等级
			$userLevel = model('UserVip')->count();

			foreach ($data as $key => &$value) {
				$value['open_level'] = ($value['open_level']) ? json_decode($value['open_level']) : array();
				for ($i=0; $i < $userLevel; $i++) {
					$value['openLevel'.$i] = (in_array($i, $value['open_level'])) ? 1 : 2;
				}
			}

			//权限查询
			// if ($count) $data['power'] = model('ManageUserRole')->getUserPower(['uid'=>session('manage_userid')]);

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
	 * 收款账号开关
	 */
	public function receivables_on_off(){
		return model('Recaivables')->receivablesOnoff();
	}

	/**
	 * 收款账号删除
	 */
	public function receivables_delete(){
		return model('Recaivables')->receivablesDel();
	}

	/**
	 * 添加收款账户
	 */
	public function receivables_add(){

		if(request()->isAjax()){
			return model('Recaivables')->receivablesAdd();
		}

		$data = model('Recaivables')->receivablesAddView();

		$this->assign('rechargeList',$data['rechargeList']);
		$this->assign('bankList',$data['bankList']);

		return $this->fetch();
	}

	/**
	 * 编辑收款账户
	 */
	public function receivables_edit(){

		if(request()->isAjax()){
			return model('Recaivables')->receivablesEdit();
		}

		$data = model('Recaivables')->receivablesEditView();

		$this->assign('rechargeList',$data['rechargeList']);
		$this->assign('bankList',$data['bankList']);
		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 二维码收款账号开放等级
	 * @return [type] [description]
	 */
	public function openLevel(){
		return model('Recaivables')->openLevel();
	}

	/**
	 * 添加收款二维码
	 * @return [type] [description]
	 */
	public function receivablesQrcodeAdd(){
		if(request()->isAjax()){
			return model('Recaivables')->receivablesQrcodeAdd();
		}

		$data = model('Recaivables')->receivablesQrcodeAddView();

		$this->assign('rechargeList',$data['rechargeList']);

		return $this->fetch();
	}

	/**
	 * 收款二维码编辑
	 * @return [type] [description]
	 */
	public function receivablesQrcodeEdit(){
		if(request()->isAjax()){
			return model('Recaivables')->receivablesQrcodeEdit();
		}

		$data = model('Recaivables')->receivablesQrcodeEditView();

		$this->assign('rechargeList',$data['rechargeList']);
		$this->assign('data',$data['data']);

		return $this->fetch();
	}

	/**
	 * 二维码上传
	 * @return [type] [description]
	 */
	public function qrcodeUpload(){
		//文件名
		$fileName = input('post.fileName/s');

		//数据验证
		$validate = validate('app\manage\validate\Bank');
		if(!$validate->scene('qrcodeUpload')->check(['fileName'=>$fileName])){
			return json(['success'=>$validate->getError()]);
		}

		//二维码图片
		$file = request()->file('file');

		//上传路径
		$uploadPath = './upload/file/rechargeQrcode';
		if(!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

		$info = $file->validate(['size'=>1000*1024*5,'ext'=>'jpg,png,gif,jpeg'])->rule('date')->move($uploadPath, $fileName);
		if($info){
			// 成功上传后 获取上传信息
			return json(['success'=>ltrim($uploadPath, '.').'/'.$info->getSaveName()]);
		}else{
			// 上传失败获取错误信息
			return json(['success'=>$file->getError()]);
		}
	}

	/**
	 * 出款设置
	 */
	// public function set_out_money(){

	// 	if(request()->isAjax()){
	// 		return model('DrawConfig')->setPayment();
	// 	}

	// 	$cashStatus = model('Setting')->where('id','>',0)->value('cash_status');

	// 	$drawConfig = model('DrawConfig')->select()->toArray();

	// 	$this->assign('cashStatus',$cashStatus);
	// 	$this->assign('drawConfig',$drawConfig);

	// 	return $this->fetch();
	// }
	public function set_out_money(){
		if (request()->isAjax()) {
			$param = input('param.');

			$count              = model('DrawConfig')->count(); // 总记录数
			$param['limit']     = (isset($param['limit']) and $param['limit']) ? $param['limit'] : 15; // 每页记录数
			$param['page']      = (isset($param['page']) and $param['page']) ? $param['page'] : 1; // 当前页
			$limitOffset        = ($param['page'] - 1) * $param['limit']; // 偏移量
			$param['sortField'] = (isset($param['sortField']) && $param['sortField']) ? $param['sortField'] : 'id';
			$param['sortType']  = (isset($param['sortType']) && $param['sortType']) ? $param['sortType'] : 'asc';

			//查询符合条件的数据
			$data = model('DrawConfig')->order($param['sortField'], $param['sortType'])->limit($limitOffset, $param['limit'])->select()->toArray();

			//权限查询
			// if ($count) $data['power'] = model('ManageUserRole')->getUserPower(['uid'=>session('manage_userid')]);

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
	 * 出款商户添加
	 * @return [type] [description]
	 */
	public function paymentAdd(){
		if (request()->isAjax()) return model('DrawConfig')->add();

		return view();
	}

	/**
	 * 出款商户编辑
	 * @return [type] [description]
	 */
	public function paymentEdit(){
		if (request()->isAjax()) return model('DrawConfig')->edit();

		$id = input('get.id/d');
		$data = model('DrawConfig')->where('id', $id)->find();

		return view('', [
			'data' => $data
		]);
	}

	/**
	 * 出款商户删除
	 * @return [type] [description]
	 */
	public function paymentDel(){
		return model('DrawConfig')->del();
	}

	/**
	 * 出款商户添加
	 * @return [type] [description]
	 */
	public function paymentSwitch(){
		return model('DrawConfig')->switch();
	}
}