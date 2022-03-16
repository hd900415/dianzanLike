<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;

class UserCommissionModel extends Model{
	//表名
	protected $table = 'ly_user_commission';

	/**
	 * 获取佣金发放历史
	 * @return [type] [description]
	 */
	public function getUserFeeHistory(){
		$param    =	input('param.');
		$userArr  =	explode(',',auth_code($param['token'],'DECODE'));
		$uid      =	$userArr[0];
		$username =	$userArr[1];
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		// 初始化查询条件
		$where[] = array('uid', '=', $uid);
		// 开始时间
		if (isset($param['start']) && $param['start']) {
			$where[] = ['date', '>=', strtotime($param['start'])];
		} else {
			$where[] = ['date', '>=', mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 86400 * 9];
		}
		// 结束时间
		if (isset($param['end']) && $param['end']) {
			$where[] = ['date', '<=', strtotime($param['end'])];
		} else {
			$where[] = ['date', '<=', mktime(23, 59, 59, date('m'), date('d'), date('Y'))];
		}

		// 总记录数
		$count       = $this->where($where)->count();
		if (!$count)
				    if($lang=='cn'){
		                return ['code' => 0, 'code_dec' => '暂无数据'];
		            }elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'No data available'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'Tidak ada data tersedia'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '暫無數據'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'कोई डाटा उपलब्ध नहीं'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'Không có dữ liệu'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Datos no disponibles'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => 'データがありません'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'ไม่มีข้อมูล'];
					}		
		
		// 每页显示记录
		$pageSize    = (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		// 当前的页,还应该处理非数字的情况
		$pageNo      = (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		// 总页数
		$pageTotal   = ceil($count / $pageSize);//当前页数大于最后页数，取最后	
		// 记录数
		$limitOffset = ($pageNo - 1) * $pageSize;
		// 获取直属下级用户数据
		$data['data'] = $this->where($where)->order('date', 'desc')->limit($limitOffset, $pageSize)->select()->toArray();
		foreach ($data['data'] as $key => &$value) {
			$value['date'] = date('Y-m-d', $value['date']);
		}

		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;

		return $data;
	}
}