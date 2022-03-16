<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */
 
namespace app\api\controller;

use app\api\controller\BaseController;

class AgentmanageController extends BaseController{
	/**
	 * 代理使用该接口添加下级
	 * 			type 		param 				explain 							must 			default 			other
	 * @param 	string 		sign 				签名									1
	 * @param 	string 		token 				时间标签								1
	 * @param 	integer 	user_id 			用户ID								1
	 * @param 	integer 	user_type 			用户类型								1									1:代理，2：会员
	 * @param 	string 		user_name_n 		用户名(新)							1
	 * @param 	string 		nick_name_n 		昵称(新)								0
	 * @param 	string 		password_n 			用户密码(新)							1
	 * @param 	string 		re_password_n 		确认用户密码(新)						1
	 * @param 	float 		flevel_fd 			返点									1									不能超过代理本身
	 * @param 	float 		flevel_banker_fd 	庄家返点								1									不能超过代理本身
	 * 
	 * @return 	integer     code 				code = 0 失败
	 *                              			code = 1 成功
	 *                              			code = 2 用户名错误（用户名：6-16位，字母、数字组成。必须字母开头,字母必须小写）
	 *                              			code = 3 用户名已经存在
	 *                              			code = 4 昵称错误 （昵称：2-20位，由字母、数字、汉字组成）
	 *                              			code = 5 用户密码错误（密码：6-16位，字母、数字组成。必须字母开头,字母必须小写）
	 *                              			code = 6 两次密码不一致
	 *                              			code = 7 返点错误
	 *                              			code = 10 庄家返点错误 
	 *                              			code = 11 获取用户返点错误
	 *                              			code = 12 新用户的返点不能高于代理返点
	 *                              			code = 15 新用户的庄家返点不能高于代理庄家返点
	 */
	public function addUser(){
		$data = model('Users')->addUser();
		return json($data);
	}
	

	/**
	 * 新建注册链接并获取
	 * 			type 		param 				explain 							must 			default 			other
	 * @param 	string 		sign 				签名									1
	 * @param 	string 		token 				时间标签								1
	 * @param 	integer 	user_id 			用户ID								1
	 * @param 	integer 	user_type 			用户类型								1									1:代理，2：会员
	 * @param 	float 		flevel_fd 			返点									1
	 * @param 	float 		flevel_banker_fd 	庄家返点								1
	 * 
	 * @return 	integer     code 				code = 0 失败
	 *                              			code = 1 成功 (客户端自己设定时间 周期为一周.如果该链接在一周内不注册默认过期)
	 *                              			code = 2 返点错误
	 *                              			code = 5 庄家返点错误
	 *                              			code = 6 获取用户返点错误
	 *                              			code = 7 新用户的返点不能高于代理返点
	 *                              			code = 10 新用户的庄家返点不能高于代理庄家返点
	 * @return 	string     	url 				注册链接
	 */
	public function addUserLink(){
		$data = model('UserLink')->addUserLink();
		return json($data);
	}
	

	/**
	 * 新建注册码添加用户
	 * 			type 		param 				explain 							must 			default 			other
	 * @param 	string 		sign 				签名									1
	 * @param 	string 		idcode 				用户识别码							1
	 * @param 	datetime 	exp_date 			有效日期								1									客户端自己设定时间周期为一周
	 *                               																					如果该链接在 一周内不注册默认过期
	 * @param 	string 		user_name_n 		用户名(新)							1
	 * @param 	string		nick_name_n 		昵称(新)								0
	 * @param 	string		password_n 			用户密码(新)							1
	 * @param 	string		re_password_n 		确认用户密码(新)						1
	 * 
	 * @return 	integer     code 				code = 0 失败
	 *                              			code = 1 成功
	 *                              			code = 2 用户名错误（用户名：6-16位，字母、数字组成。必须字母开头,字母必须小写）
	 *                              			code = 3 用户名已经存在
	 *                              			code = 4 昵称错误 （昵称：2-20位，由字母、数字、汉字组成）
	 *                              			code = 5 用户密码错误（密码：6-16位，字母、数字组成。必须字母开头,字母必须小写）
	 *                              			code = 6 两次密码不一致
	 *                              			code = 7 返点错误
	 *                              			code = 10 庄家返点错误
	 *                              			code = 11 获取用户返点错误
	 *                              			code = 12 新用户的返点不能高于代理返点
	 *                              			code = 15 新用户的庄家返点不能高于代理庄家返点
	 *                              			code = 16 超过有效日期
	 */
	public function addCodeUser(){
		$data = model('Users')->addUser();
		return json($data);
	}

	/**
	 * 获取可以给下级会员开分配的返点配额
	 * 			type 		param 				explain 							must 			default 			other
	 * @param 	string 		sign 				签名									1
	 * @param 	string 		token 				时间标签								1
	 * @param 	integer 	user_id 			用户ID								1
	 * 
	 * @return 	integer     code 				code 				= 0 获取返点配额成功
	 *                              			code 				= 1 获取返点配额失败
	 * @return 	array     	info 				flevel_fd 			= 返点配额
	 *                              			flevel_banker_fd 	= 上庄返点
	 */
	public function getRebate(){
		$data = model('Users')->getRebate();
		return json($data);
	}

	/**
	 * 修改下级会员的返点配额
	 * 			type 		param 				explain 							must 			default 			other
	 * @param 	string 		sign 				签名									1
	 * @param 	string 		token 				时间标签								1
	 * @param 	integer 	user_id 			用户ID								1
	 * @param 	integer 	xid 				下级ID								1
	 * @param 	float 		flevel_fd 			用户返点								1
	 * @param 	float 		flevel_banker_fd 	庄家返点								1
	 * 
	 * @return 	integer     code 				code 				= 0 获取返点配额成功
	 *                              			code 				= 1 获取返点配额失败
	 * @return 	array     	info 				code = 0 失败
	 *                              			code = 1 成功
	 *                              			code = 2 下级返点不能高于代理返点
	 *                              			code = 3 下级返点不能低于当前下级返点
	 *                              			code = 4 下级庄家返点不能低于当前下级庄家返点
	 *                              			code = 5 下级庄家返点不能高于代理庄家返点
	 */
	public function updateRebate(){
		$data = model('Users')->updateRebate();
		return json($data);
	}

	/**
	 * 获取注册链接列表
	 * 			type 		param 				explain 							must 			default 			other
	 * @param 	string 		sign 				签名									1
	 * @param 	string 		token 				时间标签								1
	 * @param 	integer 	user_id 			用户ID								1
	 * @param 	integer 	page_no 			页数									0
	 * @param 	integer 	page_size 			每页显示条数							0
	 * 
	 * @return 	integer     code 				code = 0 读取成功
	 *                              			code = 1 读取失败
	 * @return 	array     	info 				ebate 			= 返点值
	 *                              			chess_rebate 	= 棋牌返点
	 *                              			person_rebate 	= 真人返点
	 *                              			banker_rebate 	= 庄家返点
	 *                              			exp_date 		= 有效时间
	 *                              			url 			= 注册链接
	 *                              			isreg 			= 链接注册次数
	 *                              			idcode 			= 用户识别码（用于删除注册链接）
	 *                              			user_type 		= 用户类型：1代理，2：会员
	 * @return 	integer     data_total_nums 	总数据条数
	 * @return 	integer     data_total_page 	总页数
	 * @return 	integer     data_current_page 	当前页
	 */
	//public function userLinkList(){
	public function getRegLink(){
		$data = model('UserLink')->getRegLink();
		return json($data);
	}
    //jason微信生成和获取链接
	public function userLinkList1(){
	$data = model('UserLink')->userLinkList1();
	return json($data);
	}

	/**
	 * 删除注册链接
	 * 			type 		param 				explain 							must 			default 			other
	 * @param 	string 		sign 				签名									1									
	 * @param 	string 		token 				时间标签								1									
	 * @param 	integer 	user_id 			用户ID								1
	 * @param 	string 		idcode 				用户识别码							1
	 * 
	 * @return 	integer     code 				code = 0 失败
	 *                              			code = 1 成功
	 *                              			code = 2 用户识别码不能为空
	 */
	public function delUserLink(){
		$data = model('UserLink')->delUserLink();
		return json($data);
	}


	/**
	 * [setupclientreg 第三方账号注册]
	 * 			type 		param 				explain 							must 			default 			other
	 * @param 	string 		sign 				签名									1
	 * @param 	string 		token 				时间标签								1
	 * @param 	integer 	user_id 			用户ID								1
	 * @param 	string 		reg_type 			第三方平台类型						1									person：真人游戏
																														chess：棋牌游戏
																														sports：体育游戏			
	 * 
	 * @return 	integer     code 				code = 0 失败
	 *                              			code = 1 成功
	 *                              			code = 2 用户已存在
	 */
	public function setupclientreg(){
		$data = model('Users')->setupclientreg();
		return json($data);

	}
	
	/**
	 * [preRegisterGuest 试玩账号注册]
	 */
	public function preRegisterGuest(){
		$data = model('Users')->preRegisterGuest();
		return json($data);
	}
}

?>