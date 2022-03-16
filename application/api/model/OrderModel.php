<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;
use think\Cache;

class OrderModel extends Model{
	
	protected $table = 'ly_order';
	
	//创建订单接口
	//返回支付页面 paymentUrl
	//直接创建订单 跳转 页面提交入库
	public function createOrder(){
		
		$param			= input('param.');
		$token			= $param['token'];
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid
		$username     	= $userArr[1];//username
		
		$pid		= (isset($param['pid']) and $param['pid']) ? $param['pid'] : 0;
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		$jiner		=	(isset($param['jiner']) and $param['jiner']) ? $param['jiner'] : 0;
		
		$pw			=	(isset($param['pw']) and $param['pw']) ? $param['pw'] : 0;
		
		$Projectinfo	=	model('Project')->field('ly_project.*,ly_repayment_method.*')->join('ly_repayment_method','ly_project.Repayment_method=ly_repayment_method.id')->where(array(['ly_project.state','<>',2],['ly_project.id','=',$param['pid']]))->findOrEmpty();
		
		if(!$Projectinfo){
			$data['code']		= 0;
			if($lang=='cn'){
			$data['code_dec']	= '暂无项目';
			}elseif($lang=='en'){
			$data['code_dec']	= 'No project';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Tidak ada proyek';
			}elseif($lang=='ft'){
			$data['code_dec']	= '暫無項目';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'कोई प्रोजेक्ट नहीं';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Không có dự án';
			}elseif($lang=='es'){
			$data['code_dec']	= 'Sin proyecto';
			}elseif($lang=='ja'){
			$data['code_dec']	= 'プロジェクトがありません';
			}
			return $data;
		}
		
		if($Projectinfo['Starting_money'] > $jiner){
			$data['code']		= 0;
			if($lang=='cn'){
			$data['code_dec']	= '不在起投金额范围内';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Not within the range of initial investment amount';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Tidak dalam jangkauan jumlah investasi awal';
			}elseif($lang=='ft'){
			$data['code_dec']	= '不在起投金額範圍內';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'प्रारंभिक निवेश मात्रा की सीमा में नहीं';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Không nằm trong phạm vi số lượng đầu tư';
			}elseif($lang=='es'){
			$data['code_dec']	= 'No dentro de los límites de la inversión inicial.';
			}elseif($lang=='ja'){
			$data['code_dec']	= '起投金額の範囲内ではない';
			}
			return $data;
		}
		
		if($jiner > $Projectinfo['due_money']){
			$data['code']		= 0;
			if($lang=='cn'){
			$data['code_dec']	= '超过投金额范围内';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Exceeding the investment amount';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Lebih dari jumlah investasi';
			}elseif($lang=='ft'){
			$data['code_dec']	= '超過投金額範圍內';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'निवेश मात्रा से अधिक';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Vượt quá mức đầu tư';
			}elseif($lang=='es'){
			$data['code_dec']	= 'Por encima de la cantidad invertida';
			}elseif($lang=='ja'){
			$data['code_dec']	= '投資額の範囲を超える';
			}
			return $data;
		}
		
		if (($jiner % $Projectinfo['Increase_money'])){
			$data['code']		= 0;
			if($lang=='cn'){
			$data['code_dec']	= '不在递增金额范围内';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Not within the range of incremental amount';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Tidak dalam jangkauan jumlah incremental';
			}elseif($lang=='ft'){
			$data['code_dec']	= '不在遞增金額範圍內';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'अधिक मात्रा की सीमा में नहीं';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Không nằm trong phạm vi số lượng thêm';
			}elseif($lang=='es'){
			$data['code_dec']	= 'No dentro de la cantidad incremental';
			}elseif($lang=='ja'){
			$data['code_dec']	= '増分された金額の範囲内ではない';
			}
			return $data;
		}
		
		//项目剩余金额
		if($Projectinfo['Project_remaining'] < $jiner){
			$data['code']		= 0;
			if($lang=='cn'){
			$data['code_dec']	= '超过项目剩余金额';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Excess of the remaining amount of the project';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Lebih dari jumlah yang tersisa proyek';
			}elseif($lang=='ft'){
			$data['code_dec']	= '超過項目剩餘金額';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'प्रोजेक्ट के बाकी मात्रा के सिवाय';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Vượt quá số còn lại của dự án';
			}elseif($lang=='es'){
			$data['code_dec']	= 'Exceso de la cantidad restante';
			}elseif($lang=='ja'){
			$data['code_dec']	= 'プロジェクトの残額を超過する';
			}
			return $data;
		}

		$userinfo = model('Users')->field('ly_user_total.*,ly_users.*')->join('ly_user_total','ly_users.id=ly_user_total.uid')->where(array('ly_users.id'=>$uid))->findOrEmpty();
		
		//资金密码

		if(auth_code($userinfo['fund_password'],'DECODE') != $pw){
			$data['code']		= 0;
			if($lang=='cn'){
			$data['code_dec']	= '资金密码错误';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Fund password error';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Galat kata sandi dana';
			}elseif($lang=='ft'){
			$data['code_dec']	= '資金密碼錯誤';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'फ़ॉन्ड पासवर्ड त्रुटि';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Lỗi mật khẩu quỹ';
			}elseif($lang=='es'){
			$data['code_dec']	= 'Código de error';
			}elseif($lang=='ja'){
			$data['code_dec']	= '資金のパスワードが間違っています';
			}
			return $data;
		}
		
		if(!$userinfo['realname'] or !$userinfo['idcard']){
			$data['code']		= 0;
			if($lang=='cn'){
			$data['code_dec']	= '您还未实名认证';
			}elseif($lang=='en'){
			$data['code_dec']	= 'You have not been certified by your real name';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Anda belum disertifikasi dengan nama asli Anda';
			}elseif($lang=='ft'){
			$data['code_dec']	= '您還未實名認證';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'आप अपने वास्तविक नाम से प्रमाणपत्र नहीं किया गया है';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Bạn chưa được xác nhận bằng tên thật của bạn';
			}elseif($lang=='es'){
			$data['code_dec']	= 'No tiene un nombre real.';
			}elseif($lang=='ja'){
			$data['code_dec']	= 'まだ認証されていません。';
			}

			return $data;
		}
		
		

		if($userinfo['balance'] < $jiner){
			$data['code']		= 0;
			if($lang=='cn'){
			$data['code_dec']	= '余额不足';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Sorry, your credit is running low';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Maaf, kreditmu kehabisan';
			}elseif($lang=='ft'){
			$data['code_dec']	= '餘額不足';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'माफ़ करें, आपका क्रेडिट कम चल रहा है';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Xin lỗi, tín dụng của anh đang cạn dần';
			}elseif($lang=='es'){
			$data['code_dec']	= 'Saldo insuficiente';
			}elseif($lang=='ja'){
			$data['code_dec']	= '残高が足りない';
			}

			return $data;
		}
		
		//投资次数限制
		$u_due_cont	=	$this->where(array(['uid','=',$uid],['pid','=',$pid],['state','=',3]))->count();
		
		if($u_due_cont >= $Projectinfo['due_cont']){
			$data['code']		= 0;
			if($lang=='cn'){
			$data['code_dec']	= '超过投资次数限制';
			}elseif($lang=='en'){
			$data['code_dec']	= 'Limit of investment times exceeded';
			}elseif($lang=='id'){
			$data['code_dec']	= 'Batas waktu investasi melebihi';
			}elseif($lang=='ft'){
			$data['code_dec']	= '超過投資次數限制';
			}elseif($lang=='yd'){
			$data['code_dec']	= 'निवेश समयों का सीमा बढ़ाया गया';
			}elseif($lang=='vi'){
			$data['code_dec']	= 'Vượt giới hạn trong quá trình đầu tư';
			}elseif($lang=='es'){
			$data['code_dec']	= 'Exceso del límite de inversión';
			}elseif($lang=='ja'){
			$data['code_dec']	= '投資回数制限を超える';
			}

			return $data;
		}

		$day				= 	$Projectinfo['Project_duration'];

		$daytime			=	time();
		
		$rebate				=	$userinfo['rebate'] + $Projectinfo['Daily_income'];
		
		$rebate				=	sprintf("%.2f", $rebate);//日化利率
		
		$interest_income	=	$jiner*$rebate*0.01;
		
		$interest_income	=	sprintf("%.2f", $interest_income);//日利息收益
		
		$d	=	mktime(date('H'), 0, 0, date('m'), date('d'), date('Y'))+3600;
		
		$trlist = array();
		
		$lixi	=	0;
		
		$order_number = trading_number();
		$trade_number = trading_number();
		
		$setting = model('Setting')->field('service_hotline,official_QQ,WeChat_official,Mobile_client,aboutus,company,contact,problem,guides,hezuomeiti,zhifufangshi,record_number,Company_name,Customer_QQ,Accumulated_investment_amount,Conduct_investment_amount,Cumulative_expected_earnings,registered_smart_investors')->find();

		switch($Projectinfo['Repayment_method']){
			case 1://每日返息，到期还本
				for ($x=0; $x<$day; $x++) {
						$trlist[$x]['uid']					=	$uid;
						$trlist[$x]['pid']					=	$pid;
						$trlist[$x]['state']				=	3;//未返还
						$trlist[$x]['add_time']				=	$daytime;
						$trlist[$x]['rebate']				=	$userinfo['rebate'];
						$trlist[$x]['daily_income']			=	$Projectinfo['Daily_income'];
						$trlist[$x]['order_number']			=	trading_number();
						$trlist[$x]['username']				=	$username;
						$trlist[$x]['title']				=	$Projectinfo['title'];
					
						$trlist[$x]['no']					=	$x;

						$trlist[$x]['no']					=	$x+1;
						$trlist[$x]['bearing_day']			=	$d + $x * 86400;
						$trlist[$x]['trtime']				=	$d + ($x+1) * 86400;
						$trlist[$x]['repayment_principal']	=	$jiner;
						$trlist[$x]['interest_income']		=	$interest_income;
						
				}
				$lixi										=	sprintf("%.2f", $interest_income*$day);
			break;
			case 2://每周返息，到期还本
				
				if($day%7){
					$day1	=	intval($day/7)+1;
					$day3	=	intval($day/7);
				}else{
					$day1	=	intval($day/7);
				}
				
				 
				for ($x=0; $x < $day1; $x++) {
							$trlist[$x]['uid']					=	$uid;
							$trlist[$x]['pid']					=	$pid;
							$trlist[$x]['state']				=	3;//未返还
							$trlist[$x]['add_time']				=	$daytime;
							$trlist[$x]['rebate']				=	$userinfo['rebate'];
							$trlist[$x]['daily_income']			=	$Projectinfo['Daily_income'];
							$trlist[$x]['order_number']			=	trading_number();
							$trlist[$x]['username']				=	$username;
							$trlist[$x]['title']				=	$Projectinfo['title'];
					if($day3==$x){
						if($day%7){
							$day2	=	$day - ($day1-1)*7;
							
							$trlist[$x]['no']					=	$x + 1;
							$trlist[$x]['bearing_day']			=	$d + 86400 * ($day-$day2);
							$trlist[$x]['trtime']				=	$d + 86400 * $day;
							$trlist[$x]['repayment_principal']	=	$jiner;
							$trlist[$x]['interest_income']		=	sprintf("%.2f", $interest_income*$day2);
						}else{
							$trlist[$x]['no']					=	$x + 1;
							$trlist[$x]['bearing_day']			=	$d + $x * 86400*7;
							$trlist[$x]['trtime']				=	$d + ($x+1)*86400*7;
							$trlist[$x]['repayment_principal']	=	$jiner;
							$trlist[$x]['interest_income']		=	sprintf("%.2f", $interest_income*7);
						}
						
					}else{
						$trlist[$x]['no']					=	$x+1;
						$trlist[$x]['bearing_day']			=	$d + $x * 86400*7;
						$trlist[$x]['trtime']				=	$d + ($x+1)*86400*7;
						$trlist[$x]['repayment_principal']	=	$jiner;
						$trlist[$x]['interest_income']		=	sprintf("%.2f", $interest_income*7);
					}
				}
				$lixi										=	sprintf("%.2f", $interest_income*$day);
			break;
			case 3://每月返息，到期还本

				if($day%30){
					$day1	=	intval($day/30)+1;
					$day3	=	intval($day/30);
				}else{
					$day1	=	intval($day/30);
				}

				for ($x=0; $x < $day1; $x++) {
						$trlist[$x]['uid']					=	$uid;
						$trlist[$x]['pid']					=	$pid;
						$trlist[$x]['state']				=	3;//未返还
						$trlist[$x]['add_time']				=	$daytime;
						$trlist[$x]['rebate']				=	$userinfo['rebate'];
						$trlist[$x]['daily_income']			=	$Projectinfo['Daily_income'];
						$trlist[$x]['order_number']			=	trading_number();
						$trlist[$x]['username']				=	$username;
						$trlist[$x]['title']				=	$Projectinfo['title'];

					if($day3 == $x){
						if($day%30){
							$day2	=	$day - ($day1-1)*30;
							$trlist[$x]['no']					=	$x + 1;
							$trlist[$x]['bearing_day']			=	$d + 86400 * ($day-$day2);
							$trlist[$x]['trtime']				=	$d + 86400 * $day;
							$trlist[$x]['repayment_principal']	=	$jiner;
							$trlist[$x]['interest_income']		=	sprintf("%.2f", $interest_income*$day2);
						}else{
							$trlist[$x]['no']					=	$x + 1;
							$trlist[$x]['trtime']				=	$d + ($x+1)*86400*30;
							$trlist[$x]['repayment_principal']	=	$jiner;
							$trlist[$x]['interest_income']		=	sprintf("%.2f", $interest_income*30);
						}
						
					}else{
						$trlist[$x]['no']					=	$x + 1;
						$trlist[$x]['bearing_day']			=	$d + $x * 86400*30;
						$trlist[$x]['trtime']				=	$d + ($x+1)*86400*30;
						$trlist[$x]['repayment_principal']	=	$jiner;
						$trlist[$x]['interest_income']		=	sprintf("%.2f", $interest_income*30);
					}
				}
				$lixi										=	sprintf("%.2f", $interest_income*$day);
			break;
			case 4://一次性还本付息
				$trlist[1]['uid']					=	$uid;
				$trlist[1]['username']				=	$username;
				$trlist[1]['pid']					=	$pid;
				$trlist[1]['title']					=	$Projectinfo['title'];
				$trlist[1]['state']					=	3;//未返还
				$trlist[1]['add_time']				=	$daytime;
				$trlist[1]['rebate']				=	$userinfo['rebate'];
				$trlist[1]['daily_income']			=	$Projectinfo['Daily_income'];
				$trlist[1]['order_number']			=	trading_number();
				$trlist[1]['no']					=	1;
				$trlist[1]['bearing_day']			=	$d;
				$trlist[1]['trtime']				=	$d + $day * 86400;
				$trlist[1]['repayment_principal']	=	$jiner;
				$trlist[1]['interest_income']		=	sprintf("%.2f", $interest_income*$day);
				$lixi								=	sprintf("%.2f", $interest_income*$day);
			break;
			case 5://每日复利，保本保息
				for ($x=0; $x<$day; $x++) {
					$trlist[$x]['uid']					=	$uid;
					$trlist[$x]['username']				=	$username;
					$trlist[$x]['pid']					=	$pid;
					$trlist[$x]['title']				=	$Projectinfo['title'];
					$trlist[$x]['state']				=	3;//未返还
					$trlist[$x]['add_time']				=	$daytime;
					$trlist[$x]['rebate']				=	$userinfo['rebate'];
					$trlist[$x]['daily_income']			=	$Projectinfo['Daily_income'];
					$trlist[$x]['order_number']			=	trading_number();
					$trlist[$x]['no']					=	$x+1;
					$trlist[$x]['bearing_day']			=	$d + $x * 86400;
					$trlist[$x]['trtime']				=	$d + ($x+1) * 86400;
					$trlist[$x]['repayment_principal']	=	$jiner;
					$trlist[$x]['interest_income']		=	sprintf("%.2f",$jiner*$rebate*0.01);
					$lixi								+=	sprintf("%.2f",$jiner*$rebate*0.01);
					$jiner 								= sprintf("%.2f",$jiner + $jiner*$rebate*0.01);
				}
			break;
		}
		$jinerer	= $jiner + sprintf("%.2f",$jiner*$rebate*0.01);
		
		$agreement	=	'<p style="text-align:center; font-size:30px"><b>投资理财合同书</b></p>
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tbody>
							<tr>
							<td align="left">甲方：'.$setting['Company_name'].'</td>
							<td align="right">合同编号：'.$order_number.'</td>
							</tr>
						</tbody>
						</table>
						<p>乙方：'.$userinfo['realname'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
						<p>甲乙双方经友好协商，本着互利互惠、真诚合作的原则，根据《中华人民共和国合同法》等有关法律、法规，就项目投资相关事宜，经友好协商，达成如下合同条款，以资双方共同遵守。&nbsp;</p>
						<p style="font-size:18px; font-weight:bold">第一条:乙方投资项目&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
						<style type="text/css">
						.STYLE1 {font-size: 12px}
							  .soldout_seal {
								position: absolute;
								top:0px;
								left:20px;
								width:150px;
							}</style>
						<table border="1" width="100%">
						<tbody>
							<tr>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">项目编号</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">项目类别</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">投入金额</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">分红周期</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">分红时长</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">分红次数</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">周期分红金额</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">总计收入（含本金）</p>
							</td>
							</tr>
							<tr>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">'.$pid.'</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">'.$Projectinfo['Repayment_method_info'].'</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">'.$param['jiner'].'.00元</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">'.date('Y-m-d',$d).'-'.date('Y-m-d',$d + $day * 86400).'</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">'.$day.' 个自然日</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">'.count($trlist).' 次</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">'.$lixi.'元</p>
							</td>
							<td align="center" height="30" valign="middle">
							<p class="STYLE1">'.$jinerer.' 元</p>
							</td>
							</tr>
						</tbody>
						</table>
						<p style="font-size:18px; font-weight:bold">第二条：投资、分红及亏损&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
						<p>1、乙方投资'.$jiner.'.00元整购买甲方理财产品，投资项目：河南蟠桃园综合旅游开发项目(限投一次）项目。</p>
						<p>2、甲方应当在每个分红周期届满之日起'.date('Y-m-d',$d).'日内按照本合同约定分红额度将本周期分红足额支付至乙方。</p>
						<p>3、本合同约定的分红周期届满之日起'.date('Y-m-d',$d + $day * 86400).'日内，甲方一次性将乙方投资款足额返还给乙方。</p>
						<p>4、本项目亏损由甲方承担，乙方不承担本项目亏损及风险。</p>
						<p style="font-size:18px; font-weight:bold">第三条：该项目经营管理事项</p>
						<p>1、该项目由甲方负责全权运营，甲方负责处理该项目运营过程中的一切事务；</p>
						<p>2、该项目独立核算，与甲方其他业务收支分离；</p>
						<p>3、未经甲方另行书面授权，乙方不得以任何理由、任何形式干预该项目运营，不得以任何理由、任何形式干涉甲方独立管理该项目事宜。</p>
						<p style="font-size:18px; font-weight:bold">第四条：禁止行为</p>
						<p>1、乙方不得从事损害甲方及该项目利益的活动，否则甲方有权终止该协议，收回乙方分红权并不予支付乙方任何投资款、补偿款；</p>
						<p>2、未经甲乙双方协商一致并签订书面协议，乙方不得向任何第三方转让、出售、赠与该项目分红权利。</p>
						<p style="font-size:18px; font-weight:bold">第五条：撤资</p>
						<p>本合同签订后，乙方不得以任何理由、任何形式向甲方提出撤资。如向甲方提出撤资的，本协议解除，甲方收回分红权，并不予向乙方退还任何投资款及补偿，甲乙双方权利义务终止。</p>
						<p style="font-size:18px; font-weight:bold">第六条、不可抗力</p>
						<p>1、签订本合同前，甲乙双方均已明确了解该项目投资有风险，该项目可能不会盈利，因此甲乙双方不可撤回地承诺：无论项目盈利或亏损，甲方均应当依照本合同约定向乙方支付分红，并在到期时足额返还乙方投资款。</p>
						<p>2、如该项目在运营过程中，因战争、地震或政府禁令等不可抗力原因导致该项目无法继续运营的，甲乙双方投资关系解除，甲方无息退还乙方投资款，双方互不负违约责任。</p>
						<p style="font-size:18px; font-weight:bold">第七条、违约责任</p>
						<p>如甲方未依照合同约定向乙方支付分红款或在投资期满未按时返还乙方投资款的，逾期按照月利息百分之二向乙方加付违约金。如逾期三个周期仍未支付违约金，乙方可起诉甲方，要求甲方一次性返还投资款、分红款并支付违约金。</p>
						<p style="font-size:18px; font-weight:bold">第八条、纠纷的解决</p>
						<p>凡因履行本合同所发生的争议，甲乙双方应友好协商解决，如协商不成，任何一方有权向甲方所在地人民法院起诉。</p>
						<p style="font-size:18px; font-weight:bold">第九条、其他</p>
						<p>1、本合同自双方签章且乙方实缴投资款之日起生效。</p>
						<p>2、本合同一式两份，甲乙双方各执一份，具有同等法律效力。</p>
						<p>3、本合同以在线方式签订的，电子件视为合同原件。</p>
						<p>【以下无正文】</p>
						';
						
		$orderdata = array(
			'uid'					=>	$uid,
			'username'				=>	$username,
			'pid'					=>	$pid,
			'title'					=>	$Projectinfo['title'],
			'state'					=>	3,//进行中
			'add_time'				=>	$daytime,//投资时间
			'order_number'			=>	$order_number,
			'trade_number'			=>	$trade_number,
			'daily_income'			=>	$Projectinfo['Daily_income'],//项目返点
			'rebate'				=>	$userinfo['rebate'],//会员返点
			'investment_amount'		=>	$param['jiner'],//投资金额
			'bearing_day'			=>	$d,//计息日
			'due_day'				=>	$d + $day * 86400,//到期日
			'agreement'				=>	$agreement,//合同
		);
		
		$is_insert = $this->insertGetId($orderdata);
		//进数据库
		if(!$is_insert){
			$data['code']		= 0;
			$data['code_dec']	= '投资失败';
			return $data;
		}
		
		foreach($trlist as $key=>&$value){
			$trlist[$key]['oid'] = $is_insert;
		}
		
		//付息还本记录
		model('OrderRecord')->insertAll($trlist);
		
		$is_update_user_b = model('UserTotal')->where('uid',$uid)->Dec('balance', $param['jiner'])->Inc('total_balance', $lixi)->Inc('balance_investment', $param['jiner'])->update();

		if($is_update_user_b){

			//卖币订单交易成功 生成流水
			$financial_data['uid'] 						= $uid;
			$financial_data['order_number'] 			= $order_number;
			$financial_data['trade_number'] 			= $trade_number;
			$financial_data['trade_type'] 				= 3;//投资付款
			$financial_data['trade_before_balance']		= $userinfo['balance'];
			$financial_data['trade_amount'] 			= $param['jiner'];
			$financial_data['account_balance'] 			= $userinfo['balance'] - $param['jiner'];
			$financial_data['account_total_balance'] 	= $userinfo['total_balance'] + $lixi;
			$financial_data['remarks'] 					= $Projectinfo['title'];
			model('TradeDetails')->tradeDetails($financial_data);

			//更新项目剩余
			model('Project')->where('id',$pid)->Dec('Project_remaining', $param['jiner'])->update();

		}
		
		$data['code']		= 1;
		$data['code_dec']	= '投资成功';
		return $data;
	}
	
	//投资记录
	public function orderList(){
	
		$param			= input('param.');
		$token			= $param['token'];
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid
		$username     	= $userArr[1];//username

		if (isset($param['state']) && $param['state']) {
			$where   = array(['state','=',$state],['uid','=',$uid]);
		}else{
			$where   = array(['uid','=',$uid]);
		}
		
		$count   = $this->where($where)->count();
		
		if(!$count){
			$data['code']		= 0;
			$data['code_dec']	= '暂无记录';
			return $data;
		}
		
		//每页显示记录
		$pageSize = (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		//当前的页,还应该处理非数字的情况
		$pageNo = (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		//总页数
		$pageTotal = ceil($count / $pageSize);//当前页数大于最后页数，取最后	
		//记录数
		$limitOffset = ($pageNo - 1) * $pageSize;
		$orderdata	=	$this->where($where)
							->field('id,pid,title,daily_income,rebate,investment_amount,bearing_day,due_day,state')
							->order('add_time','DESC')
							//->limit($limitOffset, $pageSize)
							->select()->toArray();

		$data						=	[];
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;
		
		foreach($orderdata as $key => $value){
			$data['list'][$key]['orderid']						=	$value['id'];
			$data['list'][$key]['title']						=	$value['title'];
			$data['list'][$key]['state']						= 	$value['state'];
			$data['list'][$key]['rebate']						= 	$value['rebate'];
			$data['list'][$key]['daily_income']					= 	$value['daily_income'];
			$data['list'][$key]['jiner']						= 	$value['investment_amount'];//投资金额
			$data['list'][$key]['bearing_day']					= 	date('Y-m-d',$value['bearing_day']);//计息日
			$data['list'][$key]['due_day']						= 	date('Y-m-d',$value['due_day']);//到期日
			$data['list'][$key]['y_paid']						= 	model('OrderRecord')->where(array(['uid','=',$uid],['state','=',1],['pid','=',$value['pid']],['oid','=',$value['id']]))->sum('interest_income');//已付利息
			$data['list'][$key]['n_paid']						= 	model('OrderRecord')->where(array(['uid','=',$uid],['state','=',3],['pid','=',$value['pid']],['oid','=',$value['id']]))->sum('interest_income');//未付利息
		}
		return $data;
	}
	
	//付息还本记录
	public function orderRecordList(){
	
		$param			= input('param.');
		$token			= $param['token'];
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid
		
		$oid		= (isset($param['orderid']) and $param['orderid']) ? $param['orderid'] : 0;

		if (isset($param['orderid']) && $param['orderid']) {
			$where   = array(['oid','=',$oid],['uid','=',$uid]);
		}else{
			$where   = array(['uid','=',$uid]);
		}
		
		$count   = model('OrderRecord')->where($where)->count();
		
		if(!$count){
			$data['code']		= 0;
			$data['code_dec']	= '暂无记录';
			return $data;
		}
		
		//每页显示记录
		$pageSize = (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		//当前的页,还应该处理非数字的情况
		$pageNo = (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		//总页数
		$pageTotal = ceil($count / $pageSize);//当前页数大于最后页数，取最后	
		//记录数
		$limitOffset = ($pageNo - 1) * $pageSize;
		$orderdata	=	model('OrderRecord')->where($where)
							->field('id,pid,title,daily_income,rebate,repayment_principal,trtime,no,state,interest_income,bearing_day,payment_date,payment_amount')
							->order('no','asc')
							//->limit($limitOffset, $pageSize)
							->select()->toArray();

		$data						=	[];
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;
		$data['data_total_page'] 	= $pageTotal;
		$data['data_current_page'] 	= $pageNo;
		
		foreach($orderdata as $key => $value){
			$data['list'][$key]['id']							=	$value['id'];
			$data['list'][$key]['title']						=	$value['title'];
			$data['list'][$key]['state']						= 	$value['state'];
			$data['list'][$key]['no']							= 	$value['no'];//期号
			$data['list'][$key]['jiner']						= 	$value['repayment_principal'];//投资金额
			$data['list'][$key]['bearing_day']					= 	date('Y-m-d H:i:s',$value['bearing_day']);//计息起始日
			$data['list'][$key]['trtime_day']					= 	date('Y-m-d H:i:s',$value['trtime']);//预计支付日
			$data['list'][$key]['payment_date']					= 	(isset($value['payment_date']) and $value['payment_date']) ? date('Y-m-d H:i:s',$value['payment_date']) : '';//实际支付日
			$data['list'][$key]['daily_income']					= 	$value['daily_income'];//	日化收益
			$data['list'][$key]['rebate']						= 	$value['rebate'];//	会员返点		
			$data['list'][$key]['payment_amount']				= 	$value['payment_amount'];//	实际支付金额
			$data['list'][$key]['trtime_amount']				= 	$value['interest_income'];//	预计支付金额	
			$data['list'][$key]['type_dec']						= 	'存续期';//	预计支付金额
		}
		return $data;
	}
	
	//合同
	public function hetong(){

		$param			= input('param.');
		$token			= $param['token'];
		$userArr		= explode(',',auth_code($token,'DECODE'));//uid,username
		$uid			= $userArr[0];//uid
		
		$oid		= (isset($param['orderid']) and $param['orderid']) ? $param['orderid'] : 0;
		
		$orderinfo   = $this->field('ly_order.*,ly_project.*')->join('ly_project','ly_order.pid=ly_project.id')->where(array(['ly_order.id','=',$oid],['ly_order.state','<>',2],['uid','=',$uid]))->find();
		
		if(!$orderinfo){
			$data['code']		= 0;
			$data['code_dec']	= '暂无记录';
			return $data;
		}
		
		$setting = model('Setting')->field('service_hotline,official_QQ,WeChat_official,Mobile_client,aboutus,company,contact,problem,guides,hezuomeiti,zhifufangshi,record_number,Company_name,Customer_QQ,Accumulated_investment_amount,Conduct_investment_amount,Cumulative_expected_earnings,registered_smart_investors,seal_img')->find();
		
		$userinfo = model('Users')->field('ly_user_total.*,ly_users.*')->join('ly_user_total','ly_users.id=ly_user_total.uid')->where(array('ly_users.id'=>$uid))->findOrEmpty();
		
		$data['code']							= 1;
		$data['info']['title'] 					=	$orderinfo['title'];//甲方
		$data['info']['Company_name'] 			=	$setting['Company_name'];//甲方
		$data['info']['seal_img'] 				=	$setting['seal_img'];//甲方印章
		
		$data['info']['hetong_number']			=	$orderinfo['order_number'];//合同编号
		$data['info']['realname']				=	$userinfo['realname'];//乙方
		$data['info']['idcard']					=	$userinfo['idcard'];//身份证号码
		$data['info']['pid']					=	$orderinfo['pid'];//项目编号
		$data['info']['repayment_method_info']	=	$orderinfo['Repayment_method_info'];//项目类别

		$data['info']['jiner']					=	$orderinfo['investment_amount'];//金额
		
		$data['info']['bearing_due_day']		=	date('Y-m-d',$orderinfo['bearing_day']+60*60*24);//计息日
		
		
		$data['info']['due_day']				=	date('Y-m-d',$orderinfo['due_day']);//到期日
		
		$data['info']['bearing_day']			=	date('Y-m-d',$orderinfo['bearing_day']);//签约日期
		

		$data['info']['project_duration']		=	$orderinfo['Project_duration'];//分红时长
		
		$data['info']['dividend_times']			=	model('OrderRecord')->where(array(['oid','=',$oid],['uid','=',$uid]))->count();//分红次数
		
		$data['info']['interest_income']		=	model('OrderRecord')->where(array(['uid','=',$uid],['pid','=',$orderinfo['pid']],['oid','=',$oid]))->sum('interest_income');//未付利息

		$data['info']['aggregate_income']		=	$data['info']['interest_income'] + $data['info']['jiner'];//未付利息

		return $data;
	}
	
	//付息还本
	public function repayMent(){
		
		$orderdata	=	model('OrderRecord')->field('ly_order_record.*,ly_project.Project_duration,ly_project.Repayment_method,ly_order.investment_amount')->join('ly_project','ly_order_record.pid=ly_project.id')->join('ly_order','ly_order_record.oid=ly_order.id')->where(array(['ly_order_record.trtime','<=',time()],['ly_order_record.state','=',3]))->select()->toArray();

		foreach($orderdata as $key	=> $value){

			$lastno	= $numno = 0;
			
			$userinfo = model('Users')->field('ly_user_total.*,ly_users.*')->join('ly_user_total','ly_users.id=ly_user_total.uid')->where(array('ly_users.id'=>$value['uid']))->where(array('ly_users.state'=>1))->find();
		
			if($userinfo){
				
				$updataarray		=	array(
					'state'				=>	1,
					'payment_amount'	=>	$value['interest_income'],
					'payment_date'		=>	time(),
				);

				$is_update_rm		= model('OrderRecord')->where('id',$value['id'])->update($updataarray);
				//更新订单成功	
				if($is_update_rm){
					//付息
					$is_update_user_b	= model('UserTotal')->where('uid',$value['uid'])->Inc('balance', $value['interest_income'])->update();
					if($is_update_user_b){
					
						//卖币订单交易成功 生成流水
						$financial_data['uid'] 						= $value['uid'];
						$financial_data['order_number'] 			= $value['order_number'];
						$financial_data['trade_number'] 			= trading_number();
						$financial_data['trade_type'] 				= 4;//支付利息
						$financial_data['trade_before_balance']		= $userinfo['balance'];
						$financial_data['trade_amount'] 			= $value['interest_income'];
						$financial_data['account_balance'] 			= $userinfo['balance'] + $value['interest_income'];
						$financial_data['account_total_balance'] 	= $userinfo['total_balance'];
						$financial_data['remarks'] 					= $value['title'];
						model('TradeDetails')->tradeDetails($financial_data);
						
						//还本		
						switch($value['Repayment_method']){
							case 1:case 5://1每日返息，到期还本 5 每日复利，保本保息 
								if($value['no']==$value['Project_duration']){
									$lastno	=1;
								}
							break;
							case 4://一次性还本付息
								$lastno	=1;
							break;
							case 2://每周返息，到期还本
								if($value['Project_duration']%7){
									$numno	=	intval($value['Project_duration']/7)+1;
								}else{
									$numno	=	intval($value['Project_duration']/7);
								}
								if($value['no']==$numno){
									$lastno	=1;
								}
							break;
							case 3://每月返息，到期还本
								if($value['Project_duration']%30){
									$numno	=	intval($value['Project_duration']/30)+1;
								}else{
									$numno	=	intval($value['Project_duration']/30);
								}
								
								if($value['no']==$numno){
									$lastno	=1;
								}
							break;
						}
						//最后一期 还本
						if($lastno){
							$userinfotwo = model('Users')->field('ly_user_total.*,ly_users.*')->join('ly_user_total','ly_users.id=ly_user_total.uid')->where(array('ly_users.id'=>$value['uid']))->where(array('ly_users.state'=>1))->find();
							//还本
							$is_update_user_bb	= model('UserTotal')->where('uid',$value['uid'])->Inc('balance', $value['investment_amount'])->update();
							if($is_update_user_bb){
								//卖币订单交易成功 生成流水
								$financial_datab['uid'] 					= $value['uid'];
								$financial_datab['order_number'] 			= $value['order_number'];
								$financial_datab['trade_number'] 			= trading_number();
								$financial_datab['trade_type'] 				= 5;//支付本金
								$financial_datab['trade_before_balance']	= $userinfotwo['balance'];
								$financial_datab['trade_amount'] 			= $value['investment_amount'];
								$financial_datab['account_balance'] 		= $userinfotwo['balance'] + $value['investment_amount'];
								$financial_datab['account_total_balance'] 	= $userinfotwo['total_balance'];
								$financial_datab['remarks'] 				= $value['title'];
								model('TradeDetails')->tradeDetails($financial_datab);

								//更新订单已经完成
								$this->where('id',$value['oid'])->update(array('state'=>1));
							}
						}
					}else{
						//更新订单失败
						model('OrderRecord')->where('id',$value['id'])->update(array('state'=>3));	
					}
					
				}
			}
		}
		return  date("Y-m-d H:i:s")."--success--";
	}

}