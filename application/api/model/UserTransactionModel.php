<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;

use think\Cache;

class UserTransactionModel extends model{
	
	protected $table = 'ly_user_transaction';
	
	//资金明显 流水
	public function FundDetails(){
		//获取参数
		$token 			= input('post.token/s');
		$userArr		= explode(',',auth_code($token,'DECODE'));
		$uid			= $userArr[0];
		$trade_type		= input('post.trade_type/i');		// 流水类型 0=全部 1=转入 2=转出 3 = 冻结 4 = 解冻
        $lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		$param			= input('param.');
		
		//进行中的订单
		if($trade_type != 0){
			switch($trade_type){
           		case 4://收入
           			$where = array(['uid','=',$uid],['trade_type','in',array(5,6,7,8,10,15)]);
           		break;
             	case 3://支出
                	$where = array(['uid','=',$uid],['trade_type','in',array(3,4,14,9)]);
                break;
			}
		}else{
			$where   = array(['uid','=',$uid]);
		}

		$count   =	model('TradeDetails')->where($where)->count();
		if(!$count){
			$data['code']		= 0;
				if($lang=='cn'){
				    $data['code_dec']	= '暂无交易记录';
				}elseif($lang=='en'){
				    $data['code_dec']	= 'No transaction record';
				}elseif($lang=='id'){
				    $data['code_dec']	= 'Tidak ada catatan transaksi';
				}elseif($lang=='ft'){
				    $data['code_dec']	= '暫無交易記錄';
				}elseif($lang=='yd'){
				    $data['code_dec']	= 'कोई ट्रांसेक्शन रेकॉर्ड नहीं';
				}elseif($lang=='vi'){
				    $data['code_dec']	= 'Không ghi nhận giao dịch';
				}elseif($lang=='es'){
				    $data['code_dec']	= 'No se dispone de registros';
				}elseif($lang=='ja'){
				    $data['code_dec']	= '取引記録がありません';
				}elseif($lang=='th'){
				    $data['code_dec']	= 'ไม่มีบันทึกการซื้อขาย';
				}
			
			return $data;
		}

		//每页显示记录
		$pageSize 			= (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		//当前的页,还应该处理非数字的情况
		$pageNo 			= (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		//总页数
		$pageTotal 			= ceil($count / $pageSize);//当前页数大于最后页数，取最后	
		//记录数
		$limitOffset 		= ($pageNo - 1) * $pageSize;
		
		$orderdata			= model('TradeDetails')->where($where)
							->field('id,trade_type,trade_amount,order_number,account_balance,trade_number,remarks,trade_time')
							->order('trade_time','DESC')
							//->limit($limitOffset, $pageSize)
							->select()->toArray();
			
		if(!$orderdata){
			$data['code']		= 0;
				if($lang=='cn'){
				    $data['code_dec']	= '暂无交易记录';
				}elseif($lang=='en'){
				    $data['code_dec']	= 'No transaction record';
				}elseif($lang=='id'){
				    $data['code_dec']	= 'Tidak ada catatan transaksi';
				}elseif($lang=='ft'){
				    $data['code_dec']	= '暫無交易記錄';
				}elseif($lang=='yd'){
				    $data['code_dec']	= 'कोई ट्रांसेक्शन रेकॉर्ड नहीं';
				}elseif($lang=='vi'){
				    $data['code_dec']	= 'Không ghi nhận giao dịch';
				}elseif($lang=='es'){
				    $data['code_dec']	= 'No se dispone de registros';
				}elseif($lang=='ja'){
				    $data['code_dec']	= '取引記録がありません';
				}elseif($lang=='th'){
				    $data['code_dec']	= 'ไม่มีบันทึกการซื้อขาย';
				}
			return $data;
		}


		$data						=	[];
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;
		
		foreach($orderdata as $key =>$value){
			$data['list'][$key]['id']					= $value['id'];
			$data['list'][$key]['trade_amount']			= $value['trade_amount'];			// 金额
			$data['list'][$key]['trade_time']			= date("Y-m-d H:i:s",$value['trade_time']);	// 时间
			$data['list'][$key]['trade_type']			= $value['trade_type'];//类型
			$data['list'][$key]['trade_dec']			= config('custom.transactionType')[$value['trade_type']];//订单状态
			$data['list'][$key]['remarks']				= $value['remarks'];//说明	
			$data['list'][$key]['order_number']			= $value['order_number'];//订单号
			$data['list'][$key]['trade_number']			= $value['trade_number'];//流水号
			$data['list'][$key]['account_balance']		= $value['account_balance'];//余额
			switch($value['trade_type']){
				case 2:case 3:case 11:
					$data['list'][$key]['jj']			= '-';//余额
				break;
				default:
					$data['list'][$key]['jj']			= '+';//余额
			}
		}
		return $data;
		
	}
	
	//转账
	public function Transfer(){
		
		$param 		= input('param.');
		$userArr  	= explode(',',auth_code($param['token'],'DECODE'));
		$uid      	= $userArr[0];
		$lang		= (input('post.lan')) ? input('post.lan') : 'id';	// 语言类型
		
		$username 		= (input('post.username')) ? input('post.username') : '';	// 转id
		$turn_money 	=	(input('post.turn_money')) ? input('post.turn_money') : 0;	// 金额
		$drawword 		=	(input('post.drawword')) ? input('post.drawword') : 0;	// 密码

		if($lang=='cn'){
			return ['code' => 0, 'code_dec' => '暂未开放'];
		}elseif($lang=='en'){
			return ['code' => 0, 'code_dec' => 'Not yet open'];
		}elseif($lang=='id'){
						return ['code' => 3, 'code_dec' => 'Belum terbuka'];
					}elseif($lang=='ft'){
						return ['code' => 3, 'code_dec' => '暫未開放'];
					}elseif($lang=='yd'){
						return ['code' => 3, 'code_dec' => 'अभी नहीं खोलें'];
					}elseif($lang=='vi'){
						return ['code' => 3, 'code_dec' => 'Chưa mở'];
					}elseif($lang=='es'){
						return ['code' => 3, 'code_dec' => 'No abierto'];
					}elseif($lang=='ja'){
						return ['code' => 3, 'code_dec' => 'まだ公開されていません'];
					}elseif($lang=='th'){
						return ['code' => 3, 'code_dec' => 'ไม่เปิด'];
					}
		
		
		
		if(!$username or !$turn_money or !$drawword){
		    
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}

		}
		//本人
		$userinfo		= model('Users')->field('ly_users.id,ly_users.fund_password,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.id', $uid)->find();
		if(!$userinfo){
			if($lang=='cn')
				return ['code' => 0, 'code_dec' => '用户不存在'];
			elseif($lang=='en')
				return ['code' => 0, 'code_dec' => 'user does not exist!'];
			elseif($lang=='id')
				return ['code' => 0, 'code_dec' => 'pengguna tidak ada'];
			elseif($lang=='ft')
				return ['code' => 0, 'code_dec' => '用戶不存在'];
			elseif($lang=='yd')
				return ['code' => 0, 'code_dec' => 'उपयोक्ता मौजूद नहीं है'];
			elseif($lang=='vi')
				return ['code' => 0, 'code_dec' => 'người dùng không tồn tại'];
			elseif($lang=='es')
				return ['code' => 0, 'code_dec' => 'Usuario no existente'];
			elseif($lang=='ja')
				return ['code' => 0, 'code_dec' => 'ユーザが存在しません'];
			elseif($lang=='th')
				return ['code' => 0, 'code_dec' => 'ผู้ใช้ไม่มี'];
			
		}
		//转给
		$tuserinfo		= model('Users')->field('ly_users.id,ly_users.fund_password,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.username', $username)->find();

		if(!$tuserinfo){
			if($lang=='cn')
				return ['code' => 0, 'code_dec' => '用户不存在'];
			elseif($lang=='en')
				return ['code' => 0, 'code_dec' => 'user does not exist!'];
			elseif($lang=='id')
				return ['code' => 0, 'code_dec' => 'pengguna tidak ada'];
			elseif($lang=='ft')
				return ['code' => 0, 'code_dec' => '用戶不存在'];
			elseif($lang=='yd')
				return ['code' => 0, 'code_dec' => 'उपयोक्ता मौजूद नहीं है'];
			elseif($lang=='vi')
				return ['code' => 0, 'code_dec' => 'người dùng không tồn tại'];
			elseif($lang=='es')
				return ['code' => 0, 'code_dec' => 'Usuario no existente'];
			elseif($lang=='ja')
				return ['code' => 0, 'code_dec' => 'ユーザが存在しません'];
		    elseif($lang=='th')
				return ['code' => 0, 'code_dec' => 'ผู้ใช้ไม่มี'];
			
		}

		if($userinfo['username'] == $tuserinfo['username']){
		    
			if($lang=='cn'){
				return ['code' => 2, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 2, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 2, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 2, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 2, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 2, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 2, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 2, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 2, 'code_dec' => 'เสียเหลี่ยม'];
					}		    

		}


		if($userinfo['balance'] < $turn_money){
			if($lang=='cn'){
				return ['code' => 2, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 2, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 2, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 2, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 2, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 2, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 2, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 2, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 2, 'code_dec' => 'เสียเหลี่ยม'];
					}	
		}
		
		//检查资金密码
		if(auth_code($userinfo['fund_password'],'DECODE') != $drawword){
			$data['code']		= 6;
			if($lang=='cn')	$data['code_dec']	= '密码错误';
			elseif($lang=='en') $data['code_dec'] 	= 'password error!';
			elseif($lang=='id')
				    $data['code_dec']	= 'Galat kata sandi';
				elseif($lang=='ft')
				    $data['code_dec']	= '密碼錯誤';
				elseif($lang=='yd')
				    $data['code_dec']	= 'पासवर्ड त्रुटि';
				elseif($lang=='vi')
				    $data['code_dec']	= 'Lỗi mật khẩu';
				elseif($lang=='es')
				    $data['code_dec']	= 'Contraseña incorrecta';
				elseif($lang=='ja')
				    $data['code_dec']	= 'パスワードエラー';
				elseif($lang=='th')
				    $data['code_dec']	= 'รหัสผ่านผิดพลาด';
				
			return $data;
		}

		//减钱
		$is_up_to = model('UserTotal')->where('uid', $userinfo['id'])->setDec('balance', $turn_money);
		if(!$is_up_to){
			if($lang=='cn'){
				return ['code' => 2, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 2, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 2, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 2, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 2, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 2, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 2, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 2, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 2, 'code_dec' => 'เสียเหลี่ยม'];
					}	
		}
		
		// 流水
		$financial_data['uid'] 					= $userinfo['id'];
		$financial_data['username'] 			= $userinfo['username'];
		$financial_data['order_number'] 		= 'Z'.trading_number();
		$financial_data['trade_number'] 		= 'L'.trading_number();
		$financial_data['trade_type'] 			= 11;
		$financial_data['trade_before_balance']	= $userinfo['balance'];
		$financial_data['trade_amount'] 		= $turn_money;
		$financial_data['account_balance'] 		= $userinfo['balance'] - $turn_money;
		$financial_data['remarks'] 				= '转账转出';
		$financial_data['types'] 				= 1;	// 用户1，商户2

		model('common/TradeDetails')->tradeDetails($financial_data);
		
		//加钱
		model('UserTotal')->where('uid', $tuserinfo['id'])->setInc('balance', $turn_money);

		// 流水
		$financial_data_p['uid'] 					= $tuserinfo['id'];
		$financial_data_p['username'] 				= $tuserinfo['username'];
		$financial_data_p['order_number'] 			= 'Z'.trading_number();
		$financial_data_p['trade_number'] 			= 'L'.trading_number();
		$financial_data_p['trade_type'] 			= 12;
		$financial_data_p['trade_before_balance']	= $tuserinfo['balance'];
		$financial_data_p['trade_amount'] 			= $turn_money;
		$financial_data_p['account_balance'] 		= $tuserinfo['balance'] + $turn_money;
		$financial_data_p['remarks'] 				= '转账转入';
		$financial_data_p['types'] 					= 1;	// 用户1，商户2

		model('common/TradeDetails')->tradeDetails($financial_data_p);
		if($lang=='cn'){
			return ['code' => 1, 'code_dec' => '成功'];
		}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}			

	}

	
}
